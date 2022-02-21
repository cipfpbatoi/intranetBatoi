<?php

namespace Intranet\Services;


use Intranet\Entities\Hora;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;


class AdviseTeacher
{
    protected $elemento;
    protected $peopleToAdvise;
    protected $emisor;
    protected $name;


    public function __construct($elemento,$peopleToAdvise=null){
        $this->elemento = $elemento;
        $this->emisor = $elemento->idProfesor;
        $profesor = Profesor::find($elemento->idProfesor);
        $this->name = $profesor->shortName;
        if ($peopleToAdvise){
            $this->peopleToAdvise = $peopleToAdvise;
        } else {
            if (count($grupos = self::gruposAfectados($this->elemento, $this->emisor)->toArray()) == 0) {
               $this->peopleToAdvise = [];
            } else {
               $this->peopleToAdvise = self::profesoresAfectados($grupos, $this->emisor);
            }
        }
    }

    public function avisa(){
        $mensaje = "No estaré en el centre des de " . $this->elemento->desde . " fins " . $this->elemento->hasta;
        foreach ($this->peopleToAdvise as $people){
            avisa($people->idProfesor, $mensaje, '#', $this->name);
        }
    }

    public static function exec($elemento){
        $at = new AdviseTeacher($elemento);
        $at->avisa();
    }

/**
    protected function avisaProfesorat($elemento, $mensaje = null, $idEmisor = null, $emisor = null)
    {
        $mensaje = $mensaje ? $mensaje : "No estaré en el centre des de " . $elemento->desde . " fins " . $elemento->hasta;
        $idEmisor = $idEmisor ? $idEmisor : $elemento->idProfesor;

        if (count($grupos = $this->gruposAfectados($elemento, $idEmisor)->toArray()) == 0) {
            return;
        }
        
        foreach ($this->profesoresAfectados($grupos, $idEmisor) as $profesor) {
            avisa($profesor->idProfesor, $mensaje, '#', $emisor);
        }
    }
*/

    protected static function profesoresAfectados($grupos,$emisor){
        return Horario::distinct()
                ->select('idProfesor')
                ->whereIn('idGrupo', $grupos)
                ->where('idProfesor', '<>', $emisor)
                ->get();
    }
    protected static function gruposAfectados($elemento, $idProfesor)
    {
        if (!esMismoDia($elemento->desde, $elemento->hasta)) {
            return (Horario::distinct()
                ->select('idGrupo')
                ->Profesor($idProfesor)
                ->whereNotNull('idGrupo')
                ->get());
        }

        $dia_semana = nameDay($elemento->desde);
        if (count($horas = self::horasAfectadas($elemento))) {
            return (Horario::distinct()
                ->select('idGrupo')
                ->Profesor($idProfesor)
                ->Dia($dia_semana)
                ->whereNotNull('idGrupo')
                ->whereIn('sesion_orden', $horas)
                ->get());
        }
        
        return collect();
    }

    protected static function horasAfectadas($elemento)
    {
        if (!isset($elemento->dia_completo)) {
            return Hora::horasAfectadas(hora($elemento->desde), hora($elemento->hasta));
        }
        if ($elemento->dia_completo) {
            return Hora::horasAfectadas('07:00', '23:00');
        }
        return Hora::horasAfectadas($elemento->hora_ini, $elemento->hora_fin);
        
    }
} 