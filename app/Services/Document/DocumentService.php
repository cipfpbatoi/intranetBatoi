<?php
namespace Intranet\Services\Document;

use Intranet\Services\Mail\MyMail;
use Intranet\Services\Document\PdfService;
use Intranet\Services\Signature\DigitalSignatureService;
use Intranet\Finders\Finder;
use Intranet\Http\PrintResources\PrintResource;
use Intranet\Services\UI\AppAlert as Alert;
use Illuminate\Support\Facades\Log;

/**
 * Servei per generar documents (PDF, ZIP o correus) a partir de la configuració
 * aportada per un Finder (vista Blade, plantilla signable o recursos ja existents).
 */
class DocumentService
{
    /** @var \Illuminate\Support\Collection|\Illuminate\Support\Collection<int, mixed> */
    private $elements;
    /** @var object Configuració del document (vista, plantilla, opcions) */
    private $document;
    /** @var bool Indica si cal empaquetar en ZIP */
    private $zip;
    /** @var Finder */
    private $finder;



    /**
     * DocumentService constructor.
     *
     * @param Finder $finder Proveeix configuració i dades per al document.
     */
    public function __construct(Finder $finder)
    {
        $this->zip = $finder->getZip();
        $this->elements = $finder->exec();
        $this->document = $finder->getDocument();
        $this->finder = $finder;
    }

    public function __get($key)
    {
        return property_exists($this, $key) ? $this->$key : ($this->features[$key] ?? null);
    }

    /**
     * Retorna els elements carregats pel Finder.
     *
     * @return \Illuminate\Support\Collection
     */
    public function load()
    {
       return $this->elements;
    }

    /**
     * Renderitza el document segons la configuració (email o impressió).
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    public function render()
    {
        if (isset($this->document->email)) {
            return $this->mail() ?? response()->json(['error' => 'No s\'ha pogut enviar el correu'], 400);
        }
        return $this->print() ?? response()->json(['error' => 'No s\'ha pogut generar el PDF'], 400);

    }


    /**
     * Envia el document per correu utilitzant la configuració del Finder.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
     */
    private function mail()
    {
        $elemento = $this->elements->first();
        $editable = $this->document->email['editable'] ?? false;
        if (!$elemento) {
            Alert::danger('No hi ha cap destinatari');
            return back();
        }
        $attached = isset($this->document->email['attached'])?$this->document->email['attached']:null;

        if (!$editable) {
            $contenido['view'] = view($this->document->template, compact('elemento'));
            $contenido['template'] = $this->document->template;
        } else {
            $contenido = view($this->document->template, compact('elemento'));
        }
        $mail = new MyMail(
            $this->elements,
            $contenido,
            $this->document->email,
            null,
            $editable
        );
        return $mail->render($this->document->route??'misColaboraciones');


    }

    /**
     * Decideix el mode d'impressió segons la configuració.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    private function print()
    {
        if (isset($this->document->view)) {
            return $this->generatePdfFromView();
        }

        if (isset($this->document->printResource)) {
            return $this->generatePdfFromTemplate();
        }

        return $this->concatenatePdfs();
    }

    /**
     * Genera un PDF a partir d'una vista Blade.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    private function generatePdfFromView()
    {
        if (!$this->elements || count($this->elements) === 0) {
            return response()->json(['error' => 'No s\'ha pogut generar el PDF'], 400);
        }

        if ($this->zip && $this->document->zip && count($this->elements) > 1) {
            return response()->file(
                app(PdfService::class)->hazZip(
                    $this->document->view,
                    $this->elements,
                    $this->document->pdf,
                    $this->document->pdf['orientacion']
                )
            );
        }

        return app(PdfService::class)->hazPdf(
            $this->document->view,
            $this->elements,
            $this->document->pdf,
            $this->document->pdf['orientacion']
        )->stream();
    }

    /**
     * Genera un PDF a partir d'una plantilla (`printResource`).
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */

    private function generatePdfFromTemplate()
    {
        if ($this->document->sign && $this->finder->getRequest()->mostraDiv) {
            return $this->generateSignedPdf();
        }

        if ($this->document->zip) {
            return $this->generateMultiplePdfs();
        }

        // Generació simple d'un PDF des d'una plantilla
        $resource = PrintResource::build($this->document->printResource, $this->elements);
        $pdfPath = FDFPrepareService::exec($resource);

        if (!$pdfPath || !file_exists($pdfPath)) {
            Log::error('No s\'ha pogut generar el PDF des de plantilla', [
                'resource' => $this->document->printResource,
                'path' => $pdfPath,
            ]);
            Alert::danger('No s\'ha pogut generar el document. Reviseu els logs o aviseu a l\'administrador.');

            $request = $this->finder->getRequest();
            if ($request && !$request->wantsJson()) {
                return back();
            }

            return response()->json(['error' => 'No s\'ha pogut generar el PDF'], 400);
        }

        return response()->file($pdfPath);
    }

    /**
     * Genera un PDF signat si està activada la signatura digital.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    private function generateSignedPdf()
    {
        $resource = PrintResource::build($this->document->printResource, $this->elements);
        $resource->setFlatten(true);
        $tmp_name = FDFPrepareService::exec($resource);
        if (!is_string($tmp_name) || $tmp_name === '' || !file_exists($tmp_name)) {
            Log::error('No s\'ha pogut generar el PDF temporal per a signatura', [
                'resource' => $this->document->printResource,
                'path' => $tmp_name,
            ]);
            Alert::danger('No s\'ha pogut generar el document temporal per a signar.');
            return response()->json(['error' => 'No s\'ha pogut generar el document temporal per a signar'], 400);
        }

        if ($this->document->sign && file_exists(storage_path('app/zip/'.authUser()->fileName.'.tmp'))) {
            try {
                $x = config("signatures.files.".$this->document->route.".owner.x");
                $y = config("signatures.files.".$this->document->route.".owner.y");
                $file = DigitalSignatureService::decryptCertificateUser(
                    $this->finder->getRequest()->decrypt,
                    authUser()
                );
                $passCert = $this->finder->getRequest()->cert;
                DigitalSignatureService::readCertificat($file, $passCert);

                DigitalSignatureService::sign(
                    $tmp_name,
                    storage_path('tmp/auttutor_'.authUser()->dni.'signed.pdf'),
                    $x,
                    $y,
                    $file,
                    $passCert
                );

                return response()->file(storage_path('tmp/auttutor_'.authUser()->dni.'signed.pdf'));
            } catch (\Exception $e) {
                Alert::info($e->getMessage());
            }
        }

        return response()->json(['error' => 'No s\'ha pogut signar el document'], 400);
    }


    /** Genera diversos PDFs i els empaqueta si cal.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    private function generateMultiplePdfs()
    {
        if (!$this->elements || count($this->elements) === 0) {
            return response()->json(['error' => 'No hi ha elements per generar el PDF'], 400);
        }

        $pdfs = [];
        foreach ($this->elements as $element) {
            $resource = PrintResource::build($this->document->printResource, $element);
            $pdfPath = FDFPrepareService::exec($resource, $element->idPrint);
            if ($pdfPath && file_exists($pdfPath)) {
                $pdfs[] = $pdfPath;
            } else {
                Log::error('No s\'ha pogut generar un PDF en sèrie', [
                    'resource' => $this->document->printResource,
                    'path' => $pdfPath,
                    'element' => $element->idPrint ?? null,
                ]);
            }
        }

        if (empty($pdfs)) {
            Alert::danger('No s\'ha pogut generar cap PDF');
            return response()->json(['error' => 'No s\'ha pogut generar cap PDF'], 400);
        }

        if ($this->zip && count($this->elements) > 1) {
            return $this->generateZip($pdfs, 'annexes_' . authUser()->dni);
        }

        // Si només hi ha un PDF, el retorna directament
        $tmpFile = "tmp/annexes_" . authUser()->dni . ".pdf";
        try {
            app(PdfMergeService::class)->merge($pdfs, storage_path($tmpFile));
        } catch (\Throwable $e) {
            Log::error('Error concatenant PDFs en sèrie', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error concatenant PDFs: ' . $e->getMessage()], 400);
        }

        return response()->file(storage_path($tmpFile), ['Content-Type', 'application/pdf']);
    }


    /**
     * Concatenar PDFs existents.
     */
    // Substitueix el teu concatenatePdfs per aquesta versió robusta
    /**
     * Concatenació de PDFs existents.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|null
     */
    private function concatenatePdfs()
    {
        $pdfs = $this->normalizePdfPaths($this->elements);

        if (empty($pdfs)) {
            Alert::danger('No s’han trobat PDFs per concatenar');
            return back();
        }

        if (!empty($this->document->zip)) {
            return $this->generateZip($pdfs, 'annexes_' . authUser()->dni);
        }

        $tmpFile = "tmp/annexes_" . authUser()->dni . ".pdf";
        try {
            app(PdfMergeService::class)->merge($pdfs, storage_path($tmpFile));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error concatenant PDFs: ' . $e->getMessage()], 400);
        }

        return response()->file(storage_path($tmpFile), ['Content-Type', 'application/pdf']);
    }


    /**
     * Genera un ZIP amb múltiples PDFs.
     */


    /**
     * Genera un ZIP amb els PDFs indicats i retorna una resposta de fitxer.
     *
     * @param array<string> $pdfs     Rutes completes als PDFs ja generats.
     * @param string        $filename Nom base del ZIP.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    private function generateZip($pdfs, $filename)
    {
        try {
            $zipPath = ZipService::exec($pdfs, $filename);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            Log::error('Error generant ZIP', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'No s\'ha pogut generar el ZIP'], 500);
        }

        return response()->file(storage_path($zipPath));
    }

    /**
     * Normalitza un conjunt d'entrades a rutes de fitxers PDF existents.
     *
     * @param iterable $elements Llista d'objectes, arrays o strings amb la ruta.
     *
     * @return array<string> Rutes existents de fitxers PDF.
     */
    private function normalizePdfPaths($elements): array
    {
        // Converteix a array pla
        if ($elements instanceof \Illuminate\Support\Collection) {
            $arr = $elements->all();
        } elseif (is_array($elements)) {
            $arr = $elements;
        } else {
            throw new \Exception('Format inesperat per a elements');
        }

        // Extrau rutes: permet objecte->routeFile, array['routeFile'], o string directe
        $paths = array_map(function ($el) {
            if ($el === null) {
                return null;
            }
            if (is_string($el)) {
                return $el; // ja és un path
            }
            if (is_array($el) && isset($el['routeFile'])) {
                return $el['routeFile'];
            }
            if (is_object($el)) {
                // evita Notice si no té la propietat
                return $el->routeFile ?? null;
            }
            return null;
        }, $arr);

        // Netegem nulls/buits i comprovem existència
        $paths = array_values(array_filter($paths, function ($p) {
            return is_string($p) && $p !== '' && file_exists($p);
        }));

        return $paths;
    }

}
