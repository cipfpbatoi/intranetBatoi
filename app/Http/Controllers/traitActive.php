<?php

namespace Intranet\Http\Controllers;

use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Response;


trait traitActive{

    /*
    * active ($id)
    * canvia la variable activo del elemento (alumnocurso,curso,menu)
    */
    public function active($id)
    {
        $elemento = $this->class::findOrFail($id);
        if ($elemento->activo) {
            $elemento->activo = false;
        } else {
            $elemento->activo = true;
        }
        $elemento->save();
        return $this->redirect();
    }
    
}
