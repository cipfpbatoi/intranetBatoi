<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
            'maxXmlFilesPerUpload' => (int) config('convalidacions.max_xml_files_per_upload', 20),
        ]);
    }

    /** Incorpora una o més exportacions i informa del resultat de cadascuna. */
    public function store(Request $request): RedirectResponse
    {
        $uploads = $request->file('xml');
        $files = $uploads instanceof UploadedFile ? [$uploads] : $uploads;
        $files ??= [];

        $batchValidator = Validator::make(['xml' => $files], [
            'xml' => ['required', 'array', 'min:1', 'max:' . $this->maxFilesPerUpload()],
        ]);

        if ($batchValidator->fails()) {
            return back()->withErrors($batchValidator);
        }

        $resultat = ['afegits' => [], 'errors' => []];
        foreach ($files as $index => $file) {
            $nom = $file instanceof UploadedFile
                ? $file->getClientOriginalName()
                : 'Fitxer ' . ($index + 1);
            $validator = Validator::make(['xml' => $file], ['xml' => $this->xmlRules()]);

            if ($validator->fails()) {
                $resultat['errors'][] = [
                    'nom' => $nom,
                    'missatge' => implode(' ', $validator->errors()->all()),
                ];
                continue;
            }

            try {
                $this->manager->store($file);
                $resultat['afegits'][] = $nom;
            } catch (ConvalidacioException $exception) {
                $resultat['errors'][] = ['nom' => $nom, 'missatge' => $exception->getMessage()];
            }
        }

        return back()->with('resultatPujadaXml', $resultat);
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
            'max:' . config('convalidacions.max_xml_kb', 20480),
        ];
    }

    /** Nombre màxim de fitxers processats per petició. */
    private function maxFilesPerUpload(): int
    {
        return max(1, (int) config('convalidacions.max_xml_files_per_upload', 20));
    }
}
