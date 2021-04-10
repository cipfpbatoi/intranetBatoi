<?php

namespace Intranet\Http\Controllers;

use Intranet\Services\FormBuilder;
use Response;


trait traitSCRUD{
    

    /* 
     * show($id) return vista
     * busca en model de dades i el mostra amb vista show 
     */
    
    public function show($id)
    {
        $elemento = $this->class::findOrFail($id);
        $modelo = $this->model;
        return view('intranet.show', compact('elemento', 'modelo'));
    }
    /* 
     * create($default=null) return vista create
     * accepta un array de valors per defecte
     */

    public function create($default = [])
    {
        $formulario = new FormBuilder($this->createWithDefaultValues(),$this->formFields);
        $modelo = $this->model;
        return view('intranet.create', compact('formulario', 'modelo'));
    }

    public function edit($id)
    {
        $formulario = new FormBuilder($this->class::findOrFail($id),$this->formFields);
        $modelo = $this->model;
        return view('intranet.edit', compact('formulario', 'modelo'));
    }

}
