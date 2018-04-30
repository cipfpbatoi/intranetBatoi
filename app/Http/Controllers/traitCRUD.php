<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Intranet\Botones\Panel;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;
use Response;


trait traitCRUD{
    
    
    /* 
     * destroy($id) return redirect
     * busca i esborra en un model
     * si hi ha fitxer associat l'esborra
     */
    public function destroy($id)
    {
        $borrar = $this->class::findOrFail($id);
        if ($borrar) {
            if (isset($borrar->fichero)) {
                if (file_exists($borrar->fichero))
                    unlink($borrar->fichero);
                if (file_exists(storage_path('/app/' . $borrar->fichero)))
                    unlink(storage_path('/app/' . $borrar->fichero));
            }
            $borrar->delete();
        }
        return $this->redirect();
    }
    
    /* 
     * show($id) return vista
     * busca en model de dades i el mostra amb vista show 
     */
    
    public function show($id)
    {
        $elemento = $this->class::findOrFail($id);
        $modelo = $this->model;
        return view($this->chooseView('show'), compact('elemento', 'modelo'));
    }
    /* 
     * create($default=null) return vista create
     * accepta un array de valors per defecte
     */

    public function create($default = null)
    {
        $elemento = new $this->class; //crea un nou element del model
        if ($default) { // l'ompli si hi han valors per defecte
            foreach ($default as $key => $value) {
                $elemento->$key = $value;
            }
        }
        $default = $elemento->fillDefautOptions(); // ompli caracteristiques dels camps
        $modelo = $this->model;
        return view($this->chooseView('create'), compact('elemento', 'default', 'modelo'));
    }

    /* 
     * store (Request) return redirect
     * guarda els valors del formulari
     */
    public function store(Request $request)
    {
        $this->realStore($request);
        return $this->redirect();
    }

    /* 
     * realStore (Request,id=null) return element guardat
     * valida el request
     * guarda l'element
     */
    protected function realStore(Request $request, $id = null)
    {
        $elemento = $id ? $this->class::findOrFail($id) : new $this->class; //busca si hi ha
        $this->validateAll($request, $elemento);    // valida les dades
        return $elemento->fillAll($request);        // ompli i guarda
    }

    /* 
     * edit($id) return vista edit 
     */
    public function edit($id)
    {
        $elemento = $this->class::findOrFail($id);
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }

    /*
     *  update (Request,$id) return redirect
     * guarda els valors del formulari
     */
    public function update(Request $request, $id)
    {
        $this->realStore($request, $id);
        return $this->redirect();
    }

   
    /*
     * active ($id) 
     * canvia la variable activo del elemento (alumnocurso,curso,menu)
     */
    public function active($id)
    {
        $elemento = $this->class::findOrFail($id);
        if ($elemento->activo)
            $elemento->activo = false;
        else
            $elemento->activo = true;
        $elemento->save();
        return $this->redirect();
    }

    /*
     * document ($id)
     * torna el fitxer de un model
     */
    
    public function document($id)
    {
        $elemento = $this->class::findOrFail($id);
        if ($elemento->link) 
            return response()->file(storage_path('app/' . $elemento->fichero));
        else 
           Alert::danger(trans("messages.generic.nodocument"));
        return back();
    }

    //valida extes per controlar els checkbox que pasen blancs
    protected function validateAll($request, $elemento)
    {
        foreach ($elemento->getFillable() as $property) {
            if (isset($elemento->getInputType($property)['type'])){
                if ($elemento->getInputType($property)['type'] == 'checkbox')
                    $request->$property = $request->$property == '' ? '0' : '1';
                if ($elemento->getInputType($property)['type'] == 'file')
                    if (isset($elemento->$property)) $elemento->removeRequired($property);
            }
        }
        return $this->validate($request, $elemento->getRules());
    }
}
