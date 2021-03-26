<?php

namespace Intranet\Http\Controllers;

use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Response;


trait traitGestor{

    /*
    * active ($id)
    * canvia la variable activo del elemento (alumnocurso,curso,menu)
    */
    public function gestor($id)
    {
        $documento = $this->class::findOrFail($id)->idDocumento;
        if ($documento) {
            return redirect("/documento/$documento/show");
        }

        Alert::danger(trans("messages.generic.nodocument"));
        return back();
    }
    
}
