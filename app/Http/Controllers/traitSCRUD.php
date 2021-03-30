<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;
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
        $elemento = $this->createWithDefaultValues();
        $default = $elemento->fillDefautOptions(); // ompli caracteristiques dels camps
        $modelo = $this->model;
        return view('intranet.create', compact('elemento', 'default', 'modelo'));
    }

    /*
     * edit($id) return vista edit 
     */
    public function edit($id)
    {
        $elemento = $this->class::findOrFail($id);
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        return view('intranet.edit', compact('elemento', 'default', 'modelo'));
    }

}
