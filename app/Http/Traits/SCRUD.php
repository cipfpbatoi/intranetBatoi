<?php

namespace Intranet\Http\Traits;

use Intranet\Services\FormBuilder;


trait SCRUD
{
    public function show($id)
    {
        $elemento = $this->class::findOrFail($id);
        $modelo = $this->model;
        return view($this->chooseView('show'), compact('elemento', 'modelo'));
    }

    public function create($default = [])
    {
        $formulario = new FormBuilder($this->createWithDefaultValues($default), $this->formFields);
        $modelo = $this->model;
        return view($this->chooseView('create'), compact('formulario', 'modelo'));
    }

    public function edit($id)
    {
        $formulario = new FormBuilder($this->class::findOrFail($id), $this->formFields);
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('formulario', 'modelo'));
    }

    protected function createWithDefaultValues($default = [])
    {
        return new $this->class($default);
    }

    /**
     * Defineix quina vista s'ha d'usar.
     * Pot ser sobreescrita en el controlador.
     */
    protected function chooseView($view)
    {
        return "intranet.$view"; // 🔹 Modifica-ho si cal canviar l'estructura de carpetes
    }
}

