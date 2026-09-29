<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Intranet\Application\Convalidacio\ConvalidacioException;
use Intranet\Application\Convalidacio\ConvalidacioAccessService;
use Intranet\Application\Convalidacio\ConvalidacioQueryService;
use Intranet\Application\Convalidacio\ConvalidacioService;
use Intranet\Entities\Convalidacio;
use Intranet\Entities\Alumno;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Pantalles i accions de convalidacions exclusives de l'alumnat. */
class AlumnoConvalidacioController extends Controller
{
    public function __construct(
        private readonly ConvalidacioService $service,
        private readonly ConvalidacioQueryService $queries,
        private readonly ConvalidacioAccessService $access
    ) {
        parent::__construct();
    }

    /** Mostra el formulari de contrasenya mentre Direcció manté el bloqueig actiu. */
    public function accessForm(): View|RedirectResponse
    {
        if (!$this->access->isBlocked()) {
            return redirect()->route('convalidacions.index');
        }

        return view('intranet.convalidacions.alumno.access');
    }

    /** Desbloqueja l'accés de proves per a la sessió actual. */
    public function unlock(Request $request): RedirectResponse
    {
        $validated = $request->validate(['password' => ['required', 'string', 'max:255']]);
        if (!$this->access->isBlocked() || !$this->access->verifyPassword($validated['password'])) {
            return back()->withErrors(['password' => 'La contrasenya no és correcta.']);
        }

        $this->access->unlockSession();

        return redirect()->route('convalidacions.index');
    }

    /** Mostra exclusivament les sol·licituds de l'alumne autenticat. */
    public function index(): View
    {
        return view('intranet.convalidacions.alumno.index', [
            'sollicituds' => $this->queries->sollicitudsAlumne((string) $this->alumno()->nia),
        ]);
    }

    /** Mostra el formulari temporal de composició. */
    public function create(): View
    {
        return view('intranet.convalidacions.alumno.create', [
            'modulsDisponibles' => $this->queries->modulsActuals($this->alumno()),
            'modulsAprovats' => $this->queries->modulsAprovats($this->alumno()),
            'origens' => Convalidacio::origenOptions(),
            'submissionToken' => (string) Str::uuid(),
            'maxDocumentKb' => (int) config('convalidacions.max_document_kb', 5120),
        ]);
    }

    /** Tramita la composició completa en una sola operació. */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'submission_token' => ['required', 'string', 'max:64'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.modulo_destino_id' => ['required', 'string'],
            'items.*.origen' => ['required', 'string', Rule::in(array_keys(Convalidacio::origenOptions()))],
            'items.*.resultat_origen_id' => ['nullable', 'string', 'size:64'],
            'items.*.declaracio_responsable' => ['nullable', 'boolean'],
            'items.*.document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:' . config('convalidacions.max_document_kb', 5120)],
        ]);

        $items = array_values($validated['items']);
        foreach ($items as &$item) {
            $item['declaracio_responsable'] = filter_var($item['declaracio_responsable'] ?? false, FILTER_VALIDATE_BOOL);
        }

        try {
            $sollicitud = $this->service->tramitar($this->alumno(), $validated['submission_token'], $items);
        } catch (ConvalidacioException $exception) {
            return back()->withErrors(['items' => $exception->getMessage()])->withInput();
        } catch (\Throwable $exception) {
            Log::error('Error tècnic en tramitar una sol·licitud de convalidació.', ['exception' => $exception]);

            return back()->withErrors([
                'items' => 'No s\'ha pogut tramitar la sol·licitud per un problema tècnic. No s\'ha guardat cap petició; torna-ho a intentar més tard.',
            ])->withInput();
        }

        return redirect()->route('convalidacions.show', $sollicitud)->with('success', 'Sol·licitud tramitada correctament.');
    }

    /** Mostra el detall d'una sol·licitud pròpia. */
    public function show(int $sollicitud): View
    {
        $model = $this->queries->sollicitudDetail($sollicitud) ?? abort(404);
        Gate::forUser($this->alumno())->authorize('view', $model);

        return view('intranet.convalidacions.alumno.show', ['sollicitud' => $model]);
    }

    /** Descarrega un adjunt després de validar-ne la propietat. */
    public function download(Convalidacio $convalidacio): StreamedResponse
    {
        Gate::forUser($this->alumno())->authorize('viewPeticio', $convalidacio);
        abort_unless($convalidacio->document_path && Storage::disk('convalidacions')->exists($convalidacio->document_path), 404);

        return Storage::disk('convalidacions')->download($convalidacio->document_path, $convalidacio->document_original_name);
    }

    /** Substituïx el document d'una petició retornada per Direcció. */
    public function correct(Request $request, Convalidacio $convalidacio): RedirectResponse
    {
        Gate::forUser($this->alumno())->authorize('correct', $convalidacio);
        $validated = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:' . config('convalidacions.max_document_kb', 5120)],
        ]);
        $this->service->corregirDocument($convalidacio, $this->alumno(), $validated['document']);

        return back()->with('success', 'Documentació substituïda correctament.');
    }

    /** Retorna la identitat autenticada pel guard d'alumnat. */
    private function alumno(): Alumno
    {
        $alumno = auth('alumno')->user();
        abort_unless($alumno instanceof Alumno, 401);

        return $alumno;
    }
}
