<?php

namespace Intranet\Services;
use Illuminate\Support\Facades\Session;
use Intranet\Componentes\Mensaje;

class AdviseService
{

    protected $element;
    protected $explanation;
    protected $link;


    public static function exec($element,$message=null){
        $service = new AdviseService($element,$message);
        $service->send();
    }




    public function __construct($element,$message=null){
        $this->element = $element;
        $this->setExplanation($message);
        $this->setLink();
    }

    private static function file(){
        return is_file(base_path().'/config/avisos.php')?'avisos.':'contacto.';
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
        $description = '';
        if (isset($this->element->estado)){
            $description = getClase($this->element) . ' ' . primryKey($this->element) . ' ' . trans('models.' . getClase($this->element) . '.' . $this->element->estado);
        }
        if (isset($this->element->descriptionField)) {
            $descripcion = $this->element->descriptionField;
            $description =  mb_substr(str_replace(array("\r\n", "\n", "\r"),' ',$this->element->$descripcion),0,50) . ". ";
        }
        if (isset($this->element->estado)){
            $description .= blankTrans("models." . $this->element->estado . "." . getClase($this->element));
        }
        return $description;
    }

    private function advise($dnis){
        if (is_array($dnis)) {
            foreach ($dnis as $dni) {
                Mensaje::send($dni, $this->explanation, $this->link);
            }
        }
        else {
                Mensaje::send($dnis, $this->explanation, $this->link);
        }
    }

    private function setExplanation($message){
        $this->explanation = $message.". ";
        $this->explanation .= $this->addDescriptionToMessage();
    }

    private function setLink(){
        $this->link = "/" . strtolower(getClase($this->element)) . "/" . $this->element->id;
        $this->link .= $this->element->estado < 2  ? "/edit" : "/show";
    }

    public function send(){
        foreach ($this->getAdvises() as $people) {
                switch ($people){
                    case 'Creador': $this->advise($this->element->Creador());break;
                    case 'director':
                    case 'jefeEstudios':
                    case 'secretario' :
                    case 'orientador' :
                    case 'vicedirector':
                        $this->advise(config(self::file().$people));
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