<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Intranet\Application\Convalidacio\ConvalidacioAnglesCorrespondenciesService;
use Intranet\Application\Convalidacio\ConvalidacioException;
use Intranet\Entities\CorrespondenciaCicleAngles;

/** Gestiona el catàleg de correspondències d'anglés des del panell de Direcció. */
class DireccionConvalidacioAnglesController extends Controller
{
    public function __construct(private readonly ConvalidacioAnglesCorrespondenciesService $correspondencies)
    {
        parent::__construct();
    }

    /** Mostra la importació i el catàleg actual. */
    public function index(): View
    {
        return view('intranet.convalidacions.direccion.angles.index', [
            'correspondencies' => CorrespondenciaCicleAngles::query()
                ->orderBy('codi_cicle_contenidor')
                ->orderBy('codi_cicle_angles')
                ->get(),
        ]);
    }

    /** Mostra el formulari de creació manual. */
    public function create(): View
    {
        return view('intranet.convalidacions.direccion.angles.form', [
            'correspondencia' => new CorrespondenciaCicleAngles(),
            'action' => route('convalidacions.direction.angles.store'),
            'method' => 'POST',
        ]);
    }

    /** Guarda una correspondència creada manualment. */
    public function store(Request $request): RedirectResponse
    {
        CorrespondenciaCicleAngles::query()->create($this->validarDades($request));

        return redirect()->route('convalidacions.direction.angles.index')->with('success', 'Correspondència creada.');
    }

    /** Mostra el formulari d'edició. */
    public function edit(CorrespondenciaCicleAngles $correspondencia): View
    {
        return view('intranet.convalidacions.direccion.angles.form', [
            'correspondencia' => $correspondencia,
            'action' => route('convalidacions.direction.angles.update', $correspondencia),
            'method' => 'PUT',
        ]);
    }

    /** Actualitza una correspondència existent. */
    public function update(Request $request, CorrespondenciaCicleAngles $correspondencia): RedirectResponse
    {
        $correspondencia->update($this->validarDades($request, $correspondencia));

        return redirect()->route('convalidacions.direction.angles.index')->with('success', 'Correspondència actualitzada.');
    }

    /** Elimina una correspondència del catàleg. */
    public function destroy(CorrespondenciaCicleAngles $correspondencia): RedirectResponse
    {
        $correspondencia->delete();

        return redirect()->route('convalidacions.direction.angles.index')->with('success', 'Correspondència eliminada.');
    }

    /** Importa el CSV després de validar totes les files. */
    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'csv' => ['required', 'file', 'max:2048', 'extensions:csv,txt'],
        ]);

        try {
            $importades = $this->correspondencies->importar($validated['csv']);
        } catch (ConvalidacioException $exception) {
            return back()->withInput()->withErrors(['csv' => $exception->getMessage()]);
        }

        return redirect()->route('convalidacions.direction.angles.index')->with(
            'success',
            "S'han importat {$importades} correspondències. Les dades s'han reemplaçat per als cicles inclosos al CSV."
        );
    }

    /** Valida els mateixos camps per a creació i edició manuals. */
    private function validarDades(Request $request, ?CorrespondenciaCicleAngles $correspondencia = null): array
    {
        $unique = Rule::unique('convalidacions_correspondencies_angles', 'codi_cicle_angles')
            ->where(fn ($query) => $query->where('codi_cicle_contenidor', $request->input('codi_cicle_contenidor')));
        if ($correspondencia !== null) {
            $unique->ignore($correspondencia->id);
        }

        return $request->validate([
            'codi_cicle_angles' => ['required', 'string', 'max:50', $unique],
            'nom_cicle_angles_val' => ['required', 'string', 'max:255'],
            'nom_cicle_angles_cas' => ['required', 'string', 'max:255'],
            'codi_cicle_contenidor' => ['required', 'string', 'max:50'],
            'nom_cicle_contenidor_val' => ['required', 'string', 'max:255'],
            'nom_cicle_contenidor_cas' => ['required', 'string', 'max:255'],
            'es_grau_superior' => ['required', 'boolean'],
        ]);
    }
}
