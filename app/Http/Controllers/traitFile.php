<?php

namespace Intranet\Http\Controllers;

use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Response;


trait traitFile{
    
    protected function borrarFichero($fichero){
        /**
        if (!isset($fichero) || strlen($fichero)<3) {
            return null;
        }
        if (file_exists($fichero)) {
            unlink($fichero);
        }
        if (file_exists(storage_path('app/' . $fichero))) {
            unlink(storage_path('app/' . $fichero));
        }**/

        if (Storage::disk('public')->exists($fichero)) {
            Storage::disk('public')->delete($fichero);
        }
    }

    /*
     * document ($id)
     * torna el fitxer de un model
     */
    /**
    public function document($id)
    {
        $elemento = $this->class::findOrFail($id);
        if ($elemento->link) {
            return response()->file(storage_path('app/' . $elemento->fichero));
        }
        Alert::danger(trans("messages.generic.nodocument"));
        return back();
    }
    
    public function gestor($id)
    {
        $documento = $this->class::findOrFail($id)->idDocumento;
        if ($documento) {
            return redirect("/documento/$documento/show");
        }
        
        Alert::danger(trans("messages.generic.nodocument"));
        return back();
    }
    */
    
    
}
