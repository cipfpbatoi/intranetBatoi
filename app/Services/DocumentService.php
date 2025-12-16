<?php
namespace Intranet\Services;

use Intranet\Componentes\MyMail;
use Intranet\Componentes\Pdf;
use Intranet\Finders\Finder;
use Intranet\Http\PrintResources\PrintResource;
use Styde\Html\Facades\Alert;
use mikehaertl\pdftk\Pdf as PdfTk;

class DocumentService
{
    private $elements;
    private $document;
    private $zip;
    private $finder;



    /**
     * DocumentService constructor.
     * @param $elements
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

    public function load()
    {
       return $this->elements;
    }

    public function render()
    {
        if (isset($this->document->email)) {
            return $this->mail() ?? response()->json(['error' => 'No s\'ha pogut enviar el correu'], 400);
        }
        return $this->print() ?? response()->json(['error' => 'No s\'ha pogut generar el PDF'], 400);

    }


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
     */
    private function generatePdfFromView()
    {
        if (!$this->elements || count($this->elements) === 0) {
            return response()->json(['error' => 'No s\'ha pogut generar el PDF'], 400);
        }

        if ($this->zip && $this->document->zip && count($this->elements) > 1) {
            return response()->file(
                Pdf::hazZip(
                    $this->document->view,
                    $this->elements,
                    $this->document->pdf,
                    $this->document->pdf['orientacion']
                )
            );
        }

        return Pdf::hazPdf(
            $this->document->view,
            $this->elements,
            $this->document->pdf,
            $this->document->pdf['orientacion']
        )->stream();
    }

    /**
     * Genera un PDF a partir d'una plantilla (`printResource`).
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
        return response()->file(FDFPrepareService::exec($resource));
    }

    /**
     * Genera un PDF signat si està activada la signatura digital.
     */
    private function generateSignedPdf()
    {
        $resource = PrintResource::build($this->document->printResource, $this->elements);
        $resource->setFlatten(true);
        $tmp_name = FDFPrepareService::exec($resource);

        if ($this->document->sign && file_exists(storage_path('app/zip/'.authUser()->fileName.'.tmp'))) {
            try {
                $x = config("signatures.files.".$this->document->route.".owner.x");
                $y = config("signatures.files.".$this->document->route.".owner.y");
                $file = DigitalSignatureService::decryptCertificateUser($this->finder->getRequest()->decrypt,
                    authUser());
                $cert = DigitalSignatureService::readCertificat($file, $this->finder->getRequest()->cert);

                DigitalSignatureService::sign(
                    $tmp_name,
                    storage_path('tmp/auttutor_'.authUser()->dni.'signed.pdf'),
                    $x,
                    $y,
                    $cert
                );

                return response()->file(storage_path('tmp/auttutor_'.authUser()->dni.'signed.pdf'));
            } catch (\Exception $e) {
                Alert::info($e->getMessage());
            }
        }

        return response()->json(['error' => 'No s\'ha pogut signar el document'], 400);
    }


    /**
     * Genera múltiples PDFs i els empaqueta en un ZIP.
     */
    private function generateMultiplePdfs()
    {
        if (!$this->elements || count($this->elements) === 0) {
            return response()->json(['error' => 'No hi ha elements per generar el PDF'], 400);
        }

        $pdfs = [];
        foreach ($this->elements as $element) {
            $resource = PrintResource::build($this->document->printResource, $element);
            $pdfs[] = FDFPrepareService::exec($resource, $element->idPrint);
        }

        if ($this->zip && count($this->elements) > 1) {
            return $this->generateZip($pdfs, 'annexes_' . authUser()->dni);
        }

        // Si només hi ha un PDF, el retorna directament
        $pdf = new PdfTk($pdfs);
        $tmpFile = "tmp/annexes_" . authUser()->dni . ".pdf";
        $pdf->saveAs(storage_path($tmpFile));

        return response()->file(storage_path($tmpFile), ['Content-Type', 'application/pdf']);
    }


    /**
     * Concatenar PDFs existents.
     */
    // Substitueix el teu concatenatePdfs per aquesta versió robusta
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

        $pdf = new PdfTk($pdfs);
        $tmpFile = "tmp/annexes_" . authUser()->dni . ".pdf";

        // Opcionalment, comprovar $pdf->saveAs() i l’error de pdftk
        if (!$pdf->saveAs(storage_path($tmpFile))) {
            // $pdf->getError() retorna info de pdftk si falla
            return response()->json(['error' => 'Error concatenant PDFs: '.$pdf->getError()], 400);
        }

        return response()->file(storage_path($tmpFile), ['Content-Type', 'application/pdf']);
    }


    /**
     * Genera un ZIP amb múltiples PDFs.
     */


    private function generateZip($pdfs, $filename)
    {
        return response()->file(storage_path(ZipService::exec($pdfs, $filename)));
    }

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
