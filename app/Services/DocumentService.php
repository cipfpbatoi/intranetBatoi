<?php
namespace Intranet\Services;
use Intranet\Botones\MyMail;
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
        return isset($this->$key)?$this->$key:(isset($this->features[$key])?$this->features[$key]:null);
    }

    public function load(){
       return $this->elements;
    }

    public function exec(){
        if ($this->document->redirect){
            $this->render();
        } else{
            $this->send();
        }
    }

    public function render(){
        $elemento = $this->elements->first();
        $contenido = view($this->document->template, compact('elemento'));
        if (!$this->document->email['editable']) {
            $contingut['view'] = $contenido;
            $contingut['template'] = $this->document->template;
        }

        $mail = new MyMail($this->elements, $contingut,$this->document->email );
        return $mail->render('misColaboraciones');
    }

    public function send(){

        $mail = new MyMail( $this->elements,$this->document->receiver, $this->document->subject, $this->document->view);
        $mail->send();
        return back();
    }


}