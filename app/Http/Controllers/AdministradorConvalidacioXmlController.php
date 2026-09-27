<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Intranet\Application\Convalidacio\ConvalidacioException;
use Intranet\Application\Convalidacio\ConvalidacioXmlManager;

/** Gestor privat de les exportacions d'avaluació per a administració. */
class AdministradorConvalidacioXmlController extends Controller
{
    public function __construct(private readonly ConvalidacioXmlManager $manager)
    {
        parent::__construct();
    }

    /** Mostra únicament les metadades necessàries de les fonts carregades. */
    public function index(): View
    {
        return view('intranet.convalidacions.administrador.xml', [
            'fitxers' => $this->manager->all(),
            'maxXmlKb' => (int) config('convalidacions.max_xml_kb', 20480),
        ]);
    }

    /** Incorpora una exportació nova. */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['xml' => $this->xmlRules()]);

        try {
            $this->manager->store($validated['xml']);
        } catch (ConvalidacioException $exception) {
            return back()->withErrors(['xml' => $exception->getMessage()]);
        }

        return back()->with('success', 'XML d\'avaluació incorporat correctament.');
    }

    /** Substituïx una font sense canviar-ne la identitat administrativa. */
    public function replace(Request $request, string $fitxer): RedirectResponse
    {
        $validated = $request->validate(['xml' => $this->xmlRules()]);

        try {
            $this->manager->replace($fitxer, $validated['xml']);
        } catch (ConvalidacioException $exception) {
            return back()->withErrors(['xml' => $exception->getMessage()]);
        }

        return back()->with('success', 'XML d\'avaluació substituït correctament.');
    }

    /** Elimina una font de les consultes futures. */
    public function destroy(string $fitxer): RedirectResponse
    {
        try {
            $this->manager->delete($fitxer);
        } catch (ConvalidacioException $exception) {
            return back()->withErrors(['xml' => $exception->getMessage()]);
        }

        return back()->with('success', 'XML d\'avaluació eliminat correctament.');
    }

    /** @return array<int, string> */
    private function xmlRules(): array
    {
        return [
            'required',
            'file',
            'mimes:xml',
            'max:' . config('convalidacions.max_xml_kb', 20480),
        ];
    }
}
