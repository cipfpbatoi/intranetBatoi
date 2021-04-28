<?php


namespace Intranet\Services;


use Intranet\Botones\Mail as myMail;
use Intranet\Finders\Finder;
use Intranet\Filters\Filter;



class DocumentFctService
{
    private $elements;
    private $document;



    /**
     * DocumentFctService constructor.
     * @param $elements
     */
    public function __construct(Finder $finder,Filter $filter)
    {
        $this->elements = $finder->exec();
        $this->document = $filter->getDocument();
        $filter->exec($this->elements);
    }

    public function __get($key){
        return isset($this->$key)?$this->$key:(isset($this->features[$key])?$this->features[$key]:null);
    }

    public function getElements(){
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
        $mail = new myMail($this->elements,$this->document->receiver, $this->document->subject, view($this->document->viewContent,compact('elemento')) );
        return $mail->render($this->redirect);
    }

    public function send(){
        $mail = new myMail( $this->elements,$this->document->receiver, $this->document->subject, $this->document->view);
        $mail->send();
    }


}