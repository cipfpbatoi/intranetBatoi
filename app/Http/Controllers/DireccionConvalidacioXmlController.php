<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Intranet\Application\Convalidacio\ConvalidacioException;
use Intranet\Application\Convalidacio\ConvalidacioXmlManager;

/** Gestor privat de les exportacions d'avaluació dins del panell de Direcció. */
class DireccionConvalidacioXmlController extends Controller
{
    public function __construct(private readonly ConvalidacioXmlManager $manager)
    {
        parent::__construct();
    }

    /** Mostra únicament les metadades necessàries de les fonts carregades. */
    public function index(): View
    {
        return view('intranet.convalidacions.direccion.xml', [
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

        return back()->with('success', 'Avaluació d\'ITACA afegida correctament.');
    }

    /** Elimina una font de les consultes futures. */
    public function destroy(string $fitxer): RedirectResponse
    {
        try {
            $this->manager->delete($fitxer);
        } catch (ConvalidacioException $exception) {
            return back()->withErrors(['xml' => $exception->getMessage()]);
        }

        return back()->with('success', 'Avaluació d\'ITACA eliminada correctament.');
    }

    /** @return array<int, string> */
    private function xmlRules(): array
    {
        return [
            'required',
            'file',
            'extensions:xml',
            'mimetypes:application/xml,text/xml,text/plain,application/octet-stream',
            'max:' . config('convalidacions.max_xml_kb', 20480),
        ];
    }
}
