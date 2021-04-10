<?php

namespace Intranet\Http\Controllers;

use Intranet\Services\Gestor;
use Response;


trait traitGestor{

    /*
    * active ($id)
    * canvia la variable activo del elemento (alumnocurso,curso,menu)
    */
    public function gestor($id)
    {
        $gestor = new Gestor($this->class::findOrFail($id));
        return $gestor->render();
    }
    
}
