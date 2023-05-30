<?php
namespace Intranet\Services;

use Intranet\Componentes\MyMail;
use Intranet\Componentes\Pdf;
use Intranet\Entities\AlumnoFct;
use Intranet\Finders\Finder;
use Intranet\Http\PrintResources\PrintResource;
use Styde\Html\Facades\Alert;
use mikehaertl\pdftk\Pdf as PdfTk;

class DocumentService
{
    private $elements;
    private $document;

    /**
     * DocumentService constructor.
     * @param $elements
     */
    public function __construct(Finder $finder)
    {
        $this->elements = $finder->exec();
        $this->document = $finder->getDocument();
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
            if ($this->document->zip &&
                SignaturaService::exists(authUser()->dni) &&
                count($this->elements) > 1
            ) {
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
            if (!$this->document->multiple) {
                $resource = PrintResource::build($this->document->printResource, $this->elements);
                return response()->file(FDFPrepareService::exec($resource));
            } else {
                $pdfs = [];
                foreach ($this->elements as $element) {
                    $resource = PrintResource::build($this->document->printResource, $element);
                    $pdfs[] = FDFPrepareService::exec($resource, $element->idPrint);
                }
                return response()->file(storage_path(ZipService::exec($pdfs, 'annexes_'.authUser()->dni)));
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
