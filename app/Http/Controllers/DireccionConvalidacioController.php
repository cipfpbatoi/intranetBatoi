<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Intranet\Application\Convalidacio\ConvalidacioException;
use Intranet\Application\Convalidacio\ConvalidacioAccessService;
use Intranet\Application\Convalidacio\ConvalidacioAutomaticaService;
use Intranet\Application\Convalidacio\ConvalidacioQueryService;
use Intranet\Application\Convalidacio\ConvalidacioService;
use Intranet\Entities\Convalidacio;
use Intranet\Entities\DocumentConvalidacio;
use Intranet\Entities\Profesor;
use Intranet\Entities\SollicitudConvalidacio;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Pantalles i accions de revisió reservades a Direcció. */
class DireccionConvalidacioController extends Controller
{
    public function __construct(
        private readonly ConvalidacioService $service,
        private readonly ConvalidacioQueryService $queries,
        private readonly ConvalidacioAccessService $access,
        private readonly ConvalidacioAutomaticaService $automatitzacions
    ) {
        parent::__construct();
    }

    /** Mostra el panell amb filtres d'estat i origen. */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'estat' => ['nullable', 'string', Rule::in(array_keys(Convalidacio::estatOptions()))],
            'origen' => ['nullable', 'string', Rule::in(array_merge(array_keys(Convalidacio::origenOptions()), [Convalidacio::ORIGEN_AUTOMATICA]))],
        ]);
        $origens = Convalidacio::origenOptions();
        $origens[Convalidacio::ORIGEN_AUTOMATICA] = Convalidacio::origenLabel(Convalidacio::ORIGEN_AUTOMATICA);

        $preview = $this->automatitzacions->previsualitzar();

        return view('intranet.convalidacions.direccion.index', [
            'sollicituds' => $this->queries->sollicitudsDireccion($filters['estat'] ?? null, $filters['origen'] ?? null),
            'estats' => Convalidacio::estatOptions(),
            'origens' => $origens,
            'filters' => $filters,
            'accessBlocked' => $this->access->isBlocked(),
            'accessPassword' => (string) config('convalidacions_access.password'),
            'automatitzacions' => $preview['per_sollicitud'],
            'peticionsAutomatiquesElegibles' => count($preview['casos']),
        ]);
    }

    /** Canvia l'accés de proves de l'alumnat. */
    public function access(Request $request): RedirectResponse
    {
        $validated = $request->validate(['blocked' => ['required', 'boolean']]);
        $blocked = (bool) $validated['blocked'];
        $this->access->setBlocked($blocked);

        return redirect()->route('convalidacions.direction.index')->with(
            'success',
            $blocked ? 'Accés de l\'alumnat bloquejat.' : 'Accés de l\'alumnat desbloquejat.'
        );
    }

    /** Elimina una sol·licitud i els documents associats després de confirmació explícita. */
    public function destroy(SollicitudConvalidacio $sollicitud): RedirectResponse
    {
        Gate::forUser($this->profesor())->authorize('view', $sollicitud);
        abort_if(
            $sollicitud->convalidacions()
                ->where(function ($query): void {
                    $query->whereNotNull('regla_automatica_id')
                        ->orWhere('origen', Convalidacio::ORIGEN_AUTOMATICA);
                })
                ->exists(),
            403,
            'Les convalidacions automàtiques no es poden eliminar des del panell de proves.'
        );
        $this->service->eliminarSollicitud($sollicitud);

        return redirect()->route('convalidacions.direction.index')->with('success', 'Sol·licitud de prova eliminada.');
    }

    /** Mostra totes les peticions d'una sol·licitud. */
    public function show(int $sollicitud): View
    {
        $model = $this->queries->sollicitudDetail($sollicitud) ?? abort(404);
        Gate::forUser($this->profesor())->authorize('view', $model);
        $peticionsElegibles = collect($this->automatitzacions->previsualitzar()['casos'])
            ->filter(fn (array $cas): bool => (int) $cas['peticio']->sollicitud_convalidacio_id === (int) $model->id)
            ->keyBy('peticio_id');

        return view('intranet.convalidacions.direccion.show', [
            'sollicitud' => $model,
            'estats' => Convalidacio::estatManualOptions() + [Convalidacio::ESTAT_RESOLTA => Convalidacio::estatOptions()[Convalidacio::ESTAT_RESOLTA]],
            'peticionsElegibles' => $peticionsElegibles,
        ]);
    }

    /** Canvia l'estat d'una única petició. */
    public function resolve(Request $request, Convalidacio $convalidacio): RedirectResponse
    {
        Gate::forUser($this->profesor())->authorize('resolve', $convalidacio);
        $validated = $request->validate([
            'estat' => ['required', 'string', Rule::in(array_keys(Convalidacio::estatManualOptions()))],
            'observacions' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->service->revisar($convalidacio, $this->profesor(), $validated['estat'], $validated['observacions'] ?? null);
        } catch (ConvalidacioException $exception) {
            return back()->withErrors(['estat' => $exception->getMessage()]);
        }

        return back()->with('success', 'Petició actualitzada correctament.');
    }

    /** Descarrega un adjunt autoritzat des del disc privat. */
    public function download(Convalidacio $convalidacio): StreamedResponse
    {
        Gate::forUser($this->profesor())->authorize('viewPeticio', $convalidacio);
        abort_unless($convalidacio->document_path && Storage::disk('convalidacions')->exists($convalidacio->document_path), 404);

        return Storage::disk('convalidacions')->download($convalidacio->document_path, $convalidacio->document_original_name);
    }

    /** Descarrega el certificat PRL privat després d'autoritzar la petició. */
    public function downloadPrl(Convalidacio $convalidacio): StreamedResponse
    {
        Gate::forUser($this->profesor())->authorize('viewPeticio', $convalidacio);
        abort_unless($convalidacio->document_prl_path && Storage::disk('convalidacions')->exists($convalidacio->document_prl_path), 404);

        return Storage::disk('convalidacions')->download($convalidacio->document_prl_path, $convalidacio->document_prl_original_name);
    }

    /** Descarrega un adjunt genèric després de validar-ne la relació amb la petició. */
    public function downloadAttachment(Convalidacio $convalidacio, DocumentConvalidacio $document): StreamedResponse
    {
        Gate::forUser($this->profesor())->authorize('viewPeticio', $convalidacio);
        abort_unless((int) $document->convalidacio_id === (int) $convalidacio->id, 404);
        abort_unless(Storage::disk('convalidacions')->exists($document->path), 404);

        return Storage::disk('convalidacions')->download($document->path, $document->original_name);
    }

    /** Retorna la identitat autenticada pel guard de professorat. */
    private function profesor(): Profesor
    {
        $profesor = auth('profesor')->user();
        abort_unless($profesor instanceof Profesor, 401);

        return $profesor;
    }
}
