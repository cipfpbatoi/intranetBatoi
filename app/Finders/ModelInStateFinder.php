<?php
namespace Intranet\Finders;

class ModelInStateFinder extends Finder
{
    public function exec($estado=null)
    {
        $estado = $estado??$this->document->print - 1 ;
        $modelo = $this->document->modelo;
        return $modelo::where('estado', '=', $estado)->get();
    }
}