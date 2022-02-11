<?php

namespace Intranet\Services;
use Intranet\Entities\Alumno;
use Intranet\Entities\Profesor;
use Intranet\Notifications\mensajePanel;

class AdviseService
{

    protected $element;
    protected $explanation;
    protected $link;

    private static function receptor($id){
        if (strlen($id) == 8) {
            return Alumno::find($id);
        }
        return Profesor::find($id);
    }

    public static function avisa($id, $mensaje, $enlace = '#', $emisor = null)
    {
        $emisor = $emisor??AuthUser()->shortName;
        $receptor = self::receptor($id);
        $fecha = FechaString();
        if ($emisor && $receptor) {
            $receptor->notify(new mensajePanel(
                ['motiu' => $mensaje,
                    'emissor' => $emisor,
                    'data' => $fecha,
                    'enlace' => $enlace]));
        } else {
            AuthUser()->notify(new mensajePanel(
                ['motiu' => "No trobe usuari $id",
                    'emissor' => $emisor,
                    'data' => $fecha,
                    'enlace' => $enlace]));
        }
    }


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
                self::avisa($dni, $this->explanation, $this->link);
            }
        }
        else {
            self::avisa($dnis, $this->explanation, $this->link);
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
                    case 'Creador': $this->advise($this->element->Creador());break;
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