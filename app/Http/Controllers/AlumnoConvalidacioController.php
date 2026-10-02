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
use Intranet\Entities\DocumentConvalidacio;
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
            'declaracio_responsable_sollicitud' => ['nullable', 'boolean'],
            'items.*.fol_logse' => ['nullable', 'boolean'],
            'items.*.modulo_origen_es_fol' => ['nullable', 'boolean'],
            'items.*.documents' => ['nullable', 'array', 'max:3'],
            'items.*.documents.*.descripcio' => ['required_with:items.*.documents.*.fitxer', 'string', 'max:120'],
            'items.*.documents.*.fitxer' => ['required_with:items.*.documents.*.descripcio', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:' . config('convalidacions.max_document_kb', 5120)],
            'items.*.document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:' . config('convalidacions.max_document_kb', 5120)],
            'items.*.document_prl' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:' . config('convalidacions.max_document_kb', 5120)],
        ]);

        $items = array_values($validated['items']);
        $declaracioResponsableSollicitud = filter_var(
            $validated['declaracio_responsable_sollicitud'] ?? false,
            FILTER_VALIDATE_BOOL
        );
        foreach ($items as &$item) {
            $declaracioItem = filter_var($item['declaracio_responsable'] ?? false, FILTER_VALIDATE_BOOL);
            $teAdjunts = !empty($item['documents']) || isset($item['document']) || isset($item['document_prl']);
            $requereixDeclaracio = $item['origen'] !== Convalidacio::ORIGEN_PROPI_CENTRE || $teAdjunts;
            $item['declaracio_responsable'] = $requereixDeclaracio
                ? ($declaracioResponsableSollicitud || $declaracioItem)
                : false;
            if (array_key_exists('fol_logse', $item)) {
                $item['fol_logse'] = filter_var($item['fol_logse'], FILTER_VALIDATE_BOOL);
            }
            if (array_key_exists('modulo_origen_es_fol', $item)) {
                $item['modulo_origen_es_fol'] = filter_var($item['modulo_origen_es_fol'], FILTER_VALIDATE_BOOL);
            }
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

    /** Descarrega el certificat PRL privat després d'autoritzar la petició. */
    public function downloadPrl(Convalidacio $convalidacio): StreamedResponse
    {
        Gate::forUser($this->alumno())->authorize('viewPeticio', $convalidacio);
        abort_unless($convalidacio->document_prl_path && Storage::disk('convalidacions')->exists($convalidacio->document_prl_path), 404);

        return Storage::disk('convalidacions')->download($convalidacio->document_prl_path, $convalidacio->document_prl_original_name);
    }

    /** Descarrega un adjunt genèric després de validar-ne la petició propietària. */
    public function downloadAttachment(Convalidacio $convalidacio, DocumentConvalidacio $document): StreamedResponse
    {
        Gate::forUser($this->alumno())->authorize('viewPeticio', $convalidacio);
        abort_unless((int) $document->convalidacio_id === (int) $convalidacio->id, 404);
        abort_unless(Storage::disk('convalidacions')->exists($document->path), 404);

        return Storage::disk('convalidacions')->download($document->path, $document->original_name);
    }

    /** Substituïx un adjunt concret sense perdre'n la descripció ni els altres fitxers. */
    public function correctAttachment(Request $request, Convalidacio $convalidacio, DocumentConvalidacio $document): RedirectResponse
    {
        Gate::forUser($this->alumno())->authorize('correct', $convalidacio);
        abort_unless((int) $document->convalidacio_id === (int) $convalidacio->id, 404);
        $validated = $request->validate([
            'fitxer' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:' . config('convalidacions.max_document_kb', 5120)],
        ]);
        $this->service->corregirAdjunt($convalidacio, $document, $this->alumno(), $validated['fitxer']);

        return back()->with('success', 'Document substituït correctament.');
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
