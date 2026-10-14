<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
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
use Intranet\Mail\ConvalidacioEstatActualitzat;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/** Gestió de Direcció i consultes/registre operatiu per al càrrec de Secretaria. */
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

    /** Mostra una fila per sol·licitud, sense filtrar capçaleres pels estats dels mòduls. */
    public function index(Request $request): View
    {
        $preview = $this->automatitzacions->previsualitzar();
        $filters = $request->validate([
            'pendent_direccio' => ['nullable', 'boolean'],
        ]);
        $sollicituds = $this->queries->totesSollicitudsDireccion();
        $pendentDireccio = filter_var($filters['pendent_direccio'] ?? false, FILTER_VALIDATE_BOOL);

        if ($pendentDireccio) {
            $sollicituds = $sollicituds->filter(static fn (SollicitudConvalidacio $sollicitud): bool =>
                $sollicitud->convalidacions->contains(static fn (Convalidacio $peticio): bool => in_array(
                    $peticio->estat,
                    [Convalidacio::ESTAT_ENVIADA, Convalidacio::ESTAT_EN_PROCES],
                    true
                ))
            )->values();
        }

        return view('intranet.convalidacions.direccion.index', [
            'sollicituds' => $sollicituds,
            'accessBlocked' => $this->access->isBlocked(),
            'accessPassword' => (string) config('convalidacions_access.password'),
            'automatitzacions' => $preview['per_sollicitud'],
            'peticionsAutomatiquesElegibles' => count($preview['casos']),
            'filters' => ['pendent_direccio' => $pendentDireccio],
        ]);
    }

    /** Mostra els accessos a les ferramentes de configuració de convalidacions. */
    public function configuracio(): View
    {
        return view('intranet.convalidacions.direccion.configuracio');
    }

    /** Mostra una fila i estat independents per cada mòdul sol·licitat. */
    public function moduleRequests(Request $request): View
    {
        Gate::forUser($this->profesor())->authorize('viewModuleRequests', Convalidacio::class);
        $filters = $request->validate([
            'estat' => ['nullable', 'string', Rule::in(array_keys(Convalidacio::estatOptions()))],
        ]);

        return view('intranet.convalidacions.direccion.moduls', [
            'peticions' => $this->queries->peticionsDireccion($filters),
            'estats' => Convalidacio::estatOptions(),
            'filters' => $filters,
            'itacaRegistrationEnabled' => (bool) config('convalidacions.itaca_registration_enabled', false),
        ]);
    }

    /** Genera el PDF de les peticions que coincideixen amb els mateixos filtres del llistat. */
    public function moduleRequestsPdf(Request $request)
    {
        Gate::forUser($this->profesor())->authorize('viewModuleRequests', Convalidacio::class);
        $filters = $request->validate([
            'estat' => ['nullable', 'string', Rule::in(array_keys(Convalidacio::estatOptions()))],
        ]);

        return Pdf::loadView('intranet.convalidacions.direccion.moduls-pdf', [
            'peticions' => $this->queries->peticionsDireccion($filters),
            'generat' => now(),
        ])->setPaper('a4', 'landscape')->stream('convalidacions-per-modul.pdf');
    }

    /** Deixa constància en la intranet que les peticions Acceptada ja s'han anotat manualment en ITACA. */
    public function registerAccepted(Request $request): RedirectResponse
    {
        $responsable = $this->profesor();
        Gate::forUser($responsable)->authorize('registerModuleRequests', Convalidacio::class);

        if (!(bool) config('convalidacions.itaca_registration_enabled', false)) {
            return back()->with('warning', 'La funcionalitat de registre en ITACA està pendent de definir. No s’ha modificat cap petició ni s’ha enviat cap correu.');
        }

        $validated = $request->validate([
            'peticions' => ['required', 'array', 'min:1'],
            'peticions.*' => ['required', 'integer', 'distinct'],
        ]);

        try {
            $peticions = $this->service->registrarAcceptades($validated['peticions'], $responsable);
        } catch (ConvalidacioException $exception) {
            return back()->withErrors(['peticions' => $exception->getMessage()]);
        }

        $errorsCorreu = 0;
        foreach ($peticions as $peticio) {
            if (!$this->notificarCanvi($peticio)) {
                $errorsCorreu++;
            }
        }

        $quantitat = count($peticions);
        $missatge = $quantitat . ' ' . ($quantitat === 1 ? 'petició marcada' : 'peticions marcades')
            . ' en la intranet com a registrades en ITACA.';
        if ($errorsCorreu > 0) {
            $detallCorreu = $errorsCorreu === 1 ? 'petició' : 'peticions';

            return back()->with('warning', $missatge . " No s’ha pogut enviar el correu de {$errorsCorreu} {$detallCorreu}.");
        }

        return back()->with('success', $missatge . ' S’ha notificat cada alumne.');
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
            'estats' => Convalidacio::estatManualOptions(),
            'peticionsElegibles' => $peticionsElegibles,
        ]);
    }

    /** Inicia la revisió i passa a En procés únicament els mòduls que estaven Enviada. */
    public function review(SollicitudConvalidacio $sollicitud): RedirectResponse
    {
        $revisor = $this->profesor();
        Gate::forUser($revisor)->authorize('view', $sollicitud);

        $peticions = $this->service->iniciarRevisio($sollicitud, $revisor);
        if ($peticions->isEmpty()) {
            return redirect()->route('convalidacions.direction.show', $sollicitud)
                ->with('success', 'No hi havia cap mòdul en estat Enviada; no s’ha canviat cap estat.');
        }

        $errorsCorreu = 0;
        foreach ($peticions as $peticio) {
            if (!$this->notificarCanvi($peticio)) {
                $errorsCorreu++;
            }
        }

        $quantitat = $peticions->count();
        $missatge = $quantitat . ' ' . ($quantitat === 1 ? 'mòdul ha passat' : 'mòduls han passat') . ' a En procés.';
        if ($errorsCorreu > 0) {
            return redirect()->route('convalidacions.direction.show', $sollicitud)
                ->with('warning', $missatge . " No s’ha pogut notificar {$errorsCorreu} mòduls.");
        }

        return redirect()->route('convalidacions.direction.show', $sollicitud)
            ->with('success', $missatge . ' S’ha notificat l’alumnat.');
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
            $peticioActualitzada = $this->service->revisar(
                $convalidacio,
                $this->profesor(),
                $validated['estat'],
                $validated['observacions'] ?? null
            );
        } catch (ConvalidacioException $exception) {
            return back()->withErrors(['estat' => $exception->getMessage()]);
        }

        if (!$peticioActualitzada->wasChanged(['estat', 'observacions'])) {
            return back()->with('success', 'La petició no ha canviat; no s’ha enviat cap correu.');
        }

        $peticioActualitzada->loadMissing(['sollicitud.alumno', 'moduloDestino']);
        $alumne = $peticioActualitzada->sollicitud?->alumno;
        if (!$alumne || !filter_var(trim((string) ($alumne->email ?? '')), FILTER_VALIDATE_EMAIL)) {
            return back()->with('warning', 'La petició s’ha actualitzat, però l’alumne no té una adreça de correu vàlida. No s’ha enviat cap notificació.');
        }
        if (!$this->notificarCanvi($peticioActualitzada)) {
            return back()->with('warning', 'La petició s’ha actualitzat, però no s’ha pogut enviar el correu a l’alumne.');
        }

        return back()->with('success', 'Petició actualitzada i correu enviat a l’alumne.');
    }

    /** Envia una notificació individual d'estat i retorna si s'ha pogut enviar. */
    private function notificarCanvi(Convalidacio $peticio): bool
    {
        $peticio->loadMissing(['sollicitud.alumno', 'moduloDestino']);
        $alumne = $peticio->sollicitud?->alumno;
        $correu = trim((string) ($alumne?->email ?? ''));
        if (!$alumne || !filter_var($correu, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        try {
            Mail::to($correu)->send(new ConvalidacioEstatActualitzat(
                nomAlumne: filled($alumne->fullName) ? $alumne->fullName : $alumne->nia,
                modul: $peticio->moduloDestino?->vliteral ?? $peticio->modulo_destino_id,
                estat: Convalidacio::estatOptions()[$peticio->estat] ?? $peticio->estat,
                observacions: $peticio->observacions
            ));
        } catch (Throwable $exception) {
            Log::warning('No s’ha pogut enviar la notificació d’una petició de convalidació.', [
                'convalidacio_id' => $peticio->id,
            ]);
            return false;
        }

        return true;
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
