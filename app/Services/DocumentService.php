<?php
namespace Intranet\Services;

use Intranet\Componentes\MyMail;
use Intranet\Componentes\Pdf;
use Intranet\Finders\Finder;
use Intranet\Http\PrintResources\PrintResource;
use Styde\Html\Facades\Alert;
use Intranet\Services\FDFPrepareService;

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
            return $mail->render('misColaboraciones');
        } else {
            Alert::danger('No hi ha cap destinatari');
            return back();
        }
    }

    private function print()
    {
        if (isset($this->document->view)) {
            return Pdf::hazPdf(
                $this->document->view,
                $this->elements,
                $this->document->pdf,
                $this->document->pdf['orientacion']
            )->stream();
        } else {
            $resource = PrintResource::build($this->document->printResource, $this->elements);
            return response()->file(FDFPrepareService::exec($resource));
        }
    }
}
