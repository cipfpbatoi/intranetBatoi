<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno;
use Intranet\Entities\Hora;
use Intranet\Entities\Horario;
use Jenssegers\Date\Date;


// A revisar
trait traitNotificar
{

    protected function notify($id)
    {
        $this->avisaProfesorat($this->class::findOrFail($id));
        return back();
    }

    protected function avisaProfesorat($elemento, $mensaje = null, $idEmisor = null, $emisor = null)
    {
        $mensaje = $mensaje ? $mensaje : "No estaré en el centre des de " . $elemento->desde . " fins " . $elemento->hasta;
        $idEmisor = $idEmisor ? $idEmisor : $elemento->idProfesor;

        if (count($grupos = $this->gruposAfectados($elemento, $idEmisor)->toArray()) == 0)
            return;
        
        foreach ($this->profesoresAfectados($grupos, $idEmisor) as $profesor)
            avisa($profesor->idProfesor, $mensaje,'#',$emisor);
    }

    protected function profesoresAfectados($grupos,$emisor){
        return Horario::distinct()
                ->select('idProfesor')
                ->whereIn('idGrupo', $grupos)
                ->where('idProfesor', '<>', $emisor)
                ->get();
    }
    protected function gruposAfectados($elemento, $idProfesor)
    {
        if (!esMismoDia($elemento->desde, $elemento->hasta))
            return(Horario::distinct()
                            ->select('idGrupo')
                            ->Profesor($idProfesor)
                            ->whereNotNull('idGrupo')
                            ->get());

        $dia_semana = nameDay($elemento->desde);
        if (count($horas = $this->horasAfectadas($elemento))) 
            return (Horario::distinct()
                            ->select('idGrupo')
                            ->Profesor($idProfesor)
                            ->Dia($dia_semana)
                            ->whereNotNull('idGrupo')
                            ->whereIn('sesion_orden', $horas)
                            ->get());
        
        return [];
    }

    protected function horasAfectadas($elemento)
    {
        if (!isset($elemento->dia_completo)) 
            return Hora::horasAfectadas(hora($elemento->desde), hora($elemento->hasta));
        if ($elemento->dia_completo) 
            return Hora::horasAfectadas('07:00', '23:00');
        return Hora::horasAfectadas($elemento->hora_ini, $elemento->hora_fin);
        
    }
} 