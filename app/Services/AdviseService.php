<?php

namespace Intranet\Services;

class AdviseService
{

    protected $element;
    protected $explanation;
    protected $link;


    public function __construct($element,$message=null){
        $this->element = $element;
        $this->setExplanation($message);
        $this->setLink();
    }

    private function getAdvises(){
        $advises = [];

        foreach (config("modelos.".getClase($this->element).".avisos") as $people => $adviseState){
            if (in_array($this->element->estado, $adviseState)){
                $advises[] = $people;
            }
        }
        return $advises;
    }

    private function addDescriptionToMessage()
    {
        if (isset($this->element->descriptionField)) {
            $descripcion = $this->element->descriptionField;
            return mb_substr(str_replace(array("\r\n", "\n", "\r"),' ',$this->element->$descripcion),0,50) . ". ";
        }
        return '';
    }

    private function advise($dnis){
        if (is_array($dnis)) {
            foreach ($dnis as $dni) {
                avisa($dni, $this->explanation, $this->link);
            }
        }
        else {
            avisa($dnis, $this->explanation, $this->link);
        }
    }

    private function setExplanation($message){
        $this->explanation = getClase($this->element) . ' ' . primryKey($this->element) . ' ' . trans('models.' . getClase($this->element) . '.' . $this->element->estado) . ": ";
        $this->explanation .= $this->addDescriptionToMessage();
        $this->explanation .= isset($message) ? $message : '';
        $this->explanation .= blankTrans("models." . $this->element->estado . "." . getClase($this->element));
    }

    private function setLink(){
        $this->link = "/" . strtolower(getClase($this->element)) . "/" . $this->element->id;
        $this->link .= $this->element->estado < 2  ? "/edit" : "/show";
    }

    public function send(){
        foreach ($this->getAdvises() as $people) {
                switch ($people){
                    case 'Creador': $this->advise($this->Creador());break;
                    case 'director':
                    case 'jefeEstudios':
                    case 'secretario' :
                    case 'orientador' :
                    case 'vicedirector':
                        $this->advise(config('contacto.'.$people));
                        break;
                    case 'jefeDepartamento' :
                        $this->advise(isset($this->element->Profesor->dni)?$this->element->Profesor->miJefe:AuthUser()->miJefe);break;
                    default :
                        if (isset($this->element->$people) && $this->element->$people != ''){
                            $this->advise($this->element->$people);
                        }
                }
        }
    }
}