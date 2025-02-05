<?php

namespace Intranet\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Services\FormBuilder;
use Styde\Html\Facades\Alert;


/**
 *
 */
trait Crud
{

    /**
     * @var null
     */
    protected $redirect = null;


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirect()
    {
        if (Session::get('redirect')) {
            $this->redirect = Session::get('redirect');
        } //variable session

        if ($this->redirect) {
            if (!isset($this->search)) {
                return redirect()->action($this->redirect);
            }
            return redirect()->action($this->redirect,$this->search);
        } // variable controlador
        
        return redirect()->action($this->model . 'Controller@index'); //defecto
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $elemento = $this->class::find($id);

        if (!$elemento) {
            return redirect()->back()->with('error', 'Element no trobat');
        }

        if ($elemento->fichero) {
            $this->borrarFichero($elemento->fichero);
        }

        $elemento->delete();
        return $this->redirect();
    }

    /**
     * @param $fichero
     * @return void
     */
    protected function borrarFichero($fichero){
        if (!$fichero || strlen($fichero) < 3) {
            return;
        }

        $paths = [
            public_path($fichero),
            storage_path('app/' . $fichero),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
    
    /* 
     * show($id) return vista
     * busca en model de dades i el mostra amb vista show 
     */

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
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

    /**
     * @param $default
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function create($default = [])
    {
        $formulario = new FormBuilder($this->createWithDefaultValues($default),$this->formFields);
        $modelo = $this->model;
        return view($this->chooseView('create'), compact('formulario', 'modelo'));
    }


    /**
     * @param $default
     * @return mixed
     */
    protected function createWithDefaultValues($default = []){
        return new $this->class($default);
    }

    /* 
     * store (Request) return redirect
     * guarda els valors del formulari
     */
    /**
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->realStore($request);
        return $this->redirect();
    }


    /**
     * @param  Request  $request
     * @param $id
     * @return mixed
     */
    protected function realStore(Request $request, $id = null)
    {
        $elemento = $id ? $this->class::findOrFail($id) : new $this->class; //busca si hi ha
        $this->validateAll($request, $elemento);    // valida les dades
        return $elemento->fillAll($request);        // ompli i guarda
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function edit($id)
    {
        $formulario = new FormBuilder($this->class::findOrFail($id),$this->formFields);
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('formulario', 'modelo'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function editdelete($id)
    {
        $formulario = new FormBuilder($this->class::findOrFail($id),$this->formFields);
        $modelo = $this->model;
        return view($this->chooseView('editdelete'), compact('formulario', 'modelo','id'));
    }


    /**
     * @param  Request  $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->realStore($request, $id);
        return $this->redirect();
    }

   

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function active($id)
    {
        $elemento = $this->class::findOrFail($id);
        $elemento->update(['activo' => !$elemento->activo]); // Toggle
        return $this->redirect();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function document($id)
    {
        $elemento = $this->class::findOrFail($id);
        if ($elemento->link) {
            return response()->file(storage_path('app/' . $elemento->fichero));
        }
        Alert::danger(trans("messages.generic.nodocument"));
        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
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

    //valida extes per controlar els checkbox que pasen blancs

    /**
     * @param $request
     * @param $elemento
     * @return mixed
     */
    protected function validateAll($request, $elemento)
    {
        $rules = method_exists($this->class, 'getRules') ? $this->class::getRules() : [];
        return $this->validate($this->manageCheckBox($request, $elemento), $rules);
    }


    /**
     * @param $request
     * @param $elemento
     * @return mixed
     */
    protected function manageCheckBox($request,$elemento){
        foreach ($elemento->getFillable() as $property) {
            if (isset($elemento->getInputType($property)['type']) && 
               ($elemento->getInputType($property)['type'] == 'checkbox')) {
                $request->$property = $request->has($property);
            }
        }
        return $request;
    }
    
}
