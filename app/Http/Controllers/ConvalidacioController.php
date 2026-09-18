<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Convalidacio\ConvalidacioQueryService;
use Intranet\Application\Convalidacio\ConvalidacioService;
use Intranet\Entities\Convalidacio;
use Intranet\Http\Controllers\Core\BaseController;
use Intranet\Services\UI\AppAlert as Alert;
use Illuminate\Http\Request;

/**
 * Class ConvalidacioController
 * Controlador per a la gestió de convalidacions d'alumne i direcció.
 */
class ConvalidacioController extends BaseController
{
    protected $perfil = 'alumno';

    public function __construct(
        private readonly ConvalidacioService $convalidacioService,
        private readonly ConvalidacioQueryService $convalidacioQueryService
    ) {
        parent::__construct();
        $this->model = 'SollicitudConvalidacio';
    }

    /**
     * Mostra la llista de sol·licituds de l'alumne.
     */
    public function index()
    {
        $this->authorize('viewAny', SollicitudConvalidacio::class);
        $alumnoId = authUser()->dni;
        $sollicituds = $this->convalidacioQueryService->sollicitudsAlumne($alumnoId);
        $this->panel->setOpcions(['modal' => false]);
        return $this->grid($sollicituds);
    }

    /**
     * Mostra el formulari de creació de sol·licitud.
     */
    public function create()
    {
        $this->authorize('create', SollicitudConvalidacio::class);
        $alumnoId = authUser()->dni;
        
        $grupsAlumne = authUser()->Grupo()->pluck('id')->toArray();
        $modulsDisponibles = \Intranet\Entities\Modulo::query()
            ->whereHas('Grupos', fn ($q) => $q->whereIn('id', $grupsAlumne))
            ->distinct()
            ->get(['codigo', 'cliteral', 'vliteral']);

        $ciclesCursats = \Intranet\Entities\CicleFormatiuCursat::query()
            ->where('alumno_id', $alumnoId)
            ->with('cicle')
            ->get();

        return view('convalidacions.alumno.create', compact('modulsDisponibles', 'ciclesCursats'));
    }

    /**
     * Guarda un certificat adjunt.
     */
    public function uploadCertificat(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if (!$this->convalidacioService->validarTipusFitxer($request->file('file'))) {
            return response()->json(['error' => 'Tipus de fitxer invàlid. Només PDF i imatges (JPG, PNG).'], 400);
        }

        $ruta = $this->convalidacioService->guardarCertificat($request->file('file'), authUser()->dni);

        return response()->json(['path' => $ruta, 'nom' => $request->file('file')->getClientOriginalName()]);
    }

    /**
     * Crea una nova sol·licitud de convalidació.
     */
    public function store(Request $request)
    {
        $this->authorize('create', SollicitudConvalidacio::class);

        $this->validate($request, [
            'items' => 'required|array|min:1',
            'items.*.modulo_id' => 'nullable|string',
            'items.*.tipus_convalidacio' => 'required|string|in:mateix_centre,altre_centre,escola_idiomes,titol_universitari,titol_fp',
            'items.*.cicle_formatiu_cursat_id' => 'nullable|integer',
            'items.*.certificat_path' => 'nullable|string',
            'items.*.certificat_autentic' => 'nullable|boolean',
        ]);

        try {
            $this->convalidacioService->tramitar(authUser()->dni, $request->items);
            Alert::success('Sol·licitud creada amb èxit.');
            return redirect()->route('convalidacions.index');
        } catch (\Intranet\Application\Convalidacio\ConvalidacioException $e) {
            Alert::warning($e->getMessage());
            return back()->withInput();
        } catch (\Exception $e) {
            report($e);
            Alert::danger('S\'ha produït un error inesperat. Si us plau, intenta-ho més tard.');
            return back()->withInput();
        }
    }

    /**
     * Mostra el detall d'una sol·licitud.
     */
    public function show(int $sollicitudId)
    {
        $sollicitud = $this->convalidacioQueryService->sollicitudDetail($sollicitudId);
        
        if (!$sollicitud) {
            Alert::danger('Sol·licitud no trobada.');
            return redirect()->route('convalidacions.index');
        }

        $this->authorize('view', $sollicitud);
        
        return view('convalidacions.alumno.show', compact('sollicitud'));
    }

    /**
     * Descarrega un document adjunt.
     */
    public function download(int $convalidacioId)
    {
        $convalidacio = $this->convalidacioQueryService->convalidacioDetail($convalidacioId);
        
        if (!$convalidacio) {
            Alert::danger('Document no trobat.');
            return redirect()->route('convalidacions.index');
        }

        $this->authorize('downloadDocument', $convalidacio->sollicitud, $convalidacio);

        try {
            $path = $this->convalidacioService->descarregarDocument($convalidacioId);
            return response()->download($path);
        } catch (\Intranet\Application\Convalidacio\ConvalidacioException $e) {
            Alert::warning($e->getMessage());
            return back();
        }
    }

    /**
     * Mostra la llista de sol·licituds per a direcció.
     */
    public function directionIndex()
    {
        $this->perfil = 'direccion';
        $this->authorize('viewAny', SollicitudConvalidacio::class);
        
        $sollicituds = $this->convalidacioQueryService->sollicitudsDireccion();
        $this->panel->setOpcions(['modal' => false]);
        
        return $this->grid($sollicituds);
    }

    /**
     * Mostra el detall d'una sol·licitud per a direcció.
     */
    public function directionShow(int $sollicitudId)
    {
        $this->perfil = 'direccion';
        
        $sollicitud = $this->convalidacioQueryService->sollicitudDetail($sollicitudId);
        
        if (!$sollicitud) {
            Alert::danger('Sol·licitud no trobada.');
            return redirect()->route('convalidacions.direction.index');
        }

        $this->authorize('view', $sollicitud);
        
        return view('convalidacions.direccion.show', compact('sollicitud'));
    }

    /**
     * Resol una sol·licitud (aprovar/rebutjar).
     */
    public function resolve(Request $request, int $sollicitudId)
    {
        $this->perfil = 'direccion';
        
        $sollicitud = SollicitudConvalidacio::query()->findOrFail($sollicitudId);
        
        $this->authorize('resolve', $sollicitud);

        $this->validate($request, [
            'estat' => 'required|string|in:aprovat,rebutjat,documents_requerits',
            'observacions' => 'nullable|string',
        ]);

        try {
            $this->convalidacioService->canviarEstat($sollicitudId, $request->estat, $request->observacions ?? '');
            Alert::success('Sol·licitud resolta amb èxit.');
        } catch (\Intranet\Application\Convalidacio\ConvalidacioException $e) {
            Alert::warning($e->getMessage());
        } catch (\Exception $e) {
            report($e);
            Alert::danger('S\'ha produït un error inesperat.');
        }

        return redirect()->route('convalidacions.direction.index');
    }
}
