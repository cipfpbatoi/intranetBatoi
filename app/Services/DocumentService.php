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
        return $this->$key??($this->features[$key]??null);
    }

    public function load()
    {
       return $this->elements;
    }

    public function render()
    {
        if (isset($this->document->email)) {
            return $this->mail();
        } else {
            return $this->print();
        }
    }


    private function mail()
    {
        $elemento = $this->elements->first();
        if ($elemento) {
            if (!$this->document->email['editable']) {
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
                $this->document->email['editable']
            );
            return $mail->render($this->document->route??'misColaboraciones');
        } else {
            Alert::danger('No hi ha cap destinatari');
            return back();
        }
    }

    private function print()
    {
        // Document simple pdf desde vista
        if (isset($this->document->view)) {
            if ($this->zip && $this->document->zip && count($this->elements) > 1) {
                return response()->file(
                    Pdf::hazZip(
                        $this->document->view,
                        $this->elements,
                        $this->document->pdf,
                        $this->document->pdf['orientacion']
                    )
                );
            } else {
                return Pdf::hazPdf(
                    $this->document->view,
                    $this->elements,
                    $this->document->pdf,
                    $this->document->pdf['orientacion']
                )->stream();
            }
        }
        // Document pdf desde plantilla
        if (isset($this->document->printResource)) {
            if ($this->document->sign && $this->finder->getRequest()->mostraDiv) {
                $resource = PrintResource::build($this->document->printResource, $this->elements);
                $resource->setFlatten(true);
                $tmp_name =FDFPrepareService::exec($resource);
                if ($this->document->sign && file_exists(storage_path('app/zip/'.authUser()->fileName.'.tmp'))) {
                    $x = config("signatures.files.AutTutor.owner.x");
                    $y = config("signatures.files.AutTutor.owner.y");
                    DigitalSignatureService::signCrypt(
                        $tmp_name,
                        storage_path('tmp/auttutor_'.authUser()->dni.'signed.pdf'),
                        $x,
                        $y,
                        authUser()->fileName,
                        $this->finder->getRequest()->decrypt,
                        $this->finder->getRequest()->cert
                    );
                    return response()->file(storage_path('tmp/auttutor_'.authUser()->dni.'signed.pdf'));
                }
            } else {
                if ($this->document->zip) {
                    $pdfs = [];
                    foreach ($this->elements as $element) {
                        $resource = PrintResource::build($this->document->printResource, $element);
                        $pdfs[] = FDFPrepareService::exec($resource, $element->idPrint);
                    }
                    if ($this->zip && count($this->elements) > 1) {
                        return response()->file(storage_path(ZipService::exec($pdfs, 'annexes_'.authUser()->dni)));
                    } else {
                        $pdf = new PdfTk($pdfs);
                        $tmpFile = "tmp/annexes_".authUser()->dni.".pdf";
                        $pdf->saveAs(storage_path($tmpFile));

                        return response()->file(storage_path($tmpFile), ['Content-Type', 'application/pdf']);
                    }
                } else {
                    $resource = PrintResource::build($this->document->printResource, $this->elements);
                    return response()->file(FDFPrepareService::exec($resource));
                }
            }
        }
        // Concatenar pdfs ja fets
        $archivos = [];
        foreach ($this->elements as $element) {
            $archivos[] = $element->routeFile;
        }
        $pdf = new PdfTk($archivos);
        $tmpFile = "tmp/annexes_".authUser()->dni.".pdf";
        $pdf->saveAs(storage_path($tmpFile));

        return response()->file(storage_path($tmpFile), ['Content-Type', 'application/pdf']);
    }
}
