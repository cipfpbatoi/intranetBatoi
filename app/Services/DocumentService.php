<?php
namespace Intranet\Services;
use Intranet\Componentes\MyMail;
use Intranet\Componentes\Pdf;
use Intranet\Finders\Finder;

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

    public function __get($key){
        return $this->$key??($this->features[$key]??null);
    }

    public function load(){
       return $this->elements;
    }

    public function render(){
        if (isset($this->document->email)) {
            return $this->mail();
        } else {
            return $this->print();
        }
    }


    private function mail(){
        $elemento = $this->elements->first();
        if (!$this->document->email['editable']) {
            $contenido['view'] = view($this->document->template, compact('elemento'));
            $contenido['template'] = $this->document->template;
        } else {
            $contenido = view($this->document->template, compact('elemento'));
        }
        $mail = new MyMail($this->elements, $contenido,$this->document->email,null,$this->document->email['editable'] );
        return $mail->render('misColaboraciones');
    }

    private function print(){
        return Pdf::hazPdf($this->document->view, $this->elements,
            $this->document->pdf, $this->document->pdf['orientacion'])->stream();

    }



}