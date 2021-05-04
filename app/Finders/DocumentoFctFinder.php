<?php
namespace Intranet\Finders;

use Intranet\Entities\AlumnoFct;

class DocumentoFctFinder extends Finder
{
    const CONTROLSEVEI = 1;
    const OTHERS = 2;

    public function exec(){
        if ($this->document['tipo'] == self::CONTROLSEVEI) {
            return AlumnoFct::misFcts()->where('pg0301',0)->orderBy('idFct')->orderBy('desde')->get();
        }
        if ($this->document['tipo'] == self::OTHERS) {
            return AlumnoFct::misFcts()->where('desde','<=',$this->document['hasta'])->where('hasta','>=',$this->document['desde'])->orderBy('idAlumno')->orderBy('desde')->get();
        }
    }
}