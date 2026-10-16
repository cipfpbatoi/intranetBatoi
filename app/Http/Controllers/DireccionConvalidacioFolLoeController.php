<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Intranet\Entities\ModulFolLoe;
use Illuminate\Validation\Rule;

/** Administra el catàleg de mòduls FOL LOE des de la configuració de Direcció. */
class DireccionConvalidacioFolLoeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /** Mostra el catàleg FOL LOE. */
    public function index(): View
    {
        return view('intranet.convalidacions.direccion.fol-loe.index', [
            'moduls' => ModulFolLoe::query()->orderBy('codigo_normalitzat')->get(),
        ]);
    }

    /** Crea una entrada del catàleg amb normalització i unicitat del codi. */
    public function store(Request $request): RedirectResponse
    {
        $dades = $this->validar($request);
        ModulFolLoe::query()->create($dades);

        return redirect()->route('convalidacions.direction.fol-loe.index')->with('success', 'Mòdul FOL LOE afegit.');
    }

    /** Actualitza una entrada del catàleg. */
    public function update(Request $request, ModulFolLoe $modul): RedirectResponse
    {
        $dades = $this->validar($request, $modul);
        $modul->update($dades);

        return redirect()->route('convalidacions.direction.fol-loe.index')->with('success', 'Mòdul FOL LOE actualitzat.');
    }

    /** Elimina una entrada del catàleg. */
    public function destroy(ModulFolLoe $modul): RedirectResponse
    {
        $modul->delete();

        return redirect()->route('convalidacions.direction.fol-loe.index')->with('success', 'Mòdul FOL LOE eliminat.');
    }

    /** Valida i normalitza dades compartides per a creació i edició. */
    private function validar(Request $request, ?ModulFolLoe $modul = null): array
    {
        $dades = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'regex:/^\s*\d+\s*$/'],
            'cicle_cas' => ['required', 'string', 'max:255'],
            'cicle_val' => ['required', 'string', 'max:255'],
            'nivell' => ['required', Rule::in(['GM', 'GS'])],
        ]);
        $dades['codigo'] = trim($dades['codigo']);
        $dades['codigo_normalitzat'] = ModulFolLoe::normalitzarCodi($dades['codigo']);

        Validator::make($dades, [
            'codigo' => [Rule::unique('convalidacions_moduls_fol_loe', 'codigo')->ignore($modul?->id)],
            'codigo_normalitzat' => [Rule::unique('convalidacions_moduls_fol_loe', 'codigo_normalitzat')->ignore($modul?->id)],
        ], [
            'codigo.unique' => 'Ja existeix este codi FOL LOE.',
            'codigo_normalitzat.unique' => 'Ja existeix un codi que representa el mateix nombre natural.',
        ])->validate();

        return $dades;
    }
}
