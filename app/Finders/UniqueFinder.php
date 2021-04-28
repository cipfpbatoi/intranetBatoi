<?php

namespace Intranet\Finders;

class UniqueFinder implements Finder{

    private $modelo;
    private $id;

    /**
     * UniqueFinder constructor.
     * @param $modelo
     * @param $id
     */
    public function __construct($modelo, $id)
    {
        $this->modelo = "Intranet\\Entities\\".$modelo;
        $this->id = $id;
    }

    public function exec(){
        return $this->Modelo::where('id',$this->id)->get();
    }

}

