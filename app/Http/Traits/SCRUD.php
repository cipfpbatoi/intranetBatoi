<?php

namespace Intranet\Http\Traits;

use Intranet\Services\UI\FormBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait SCRUD
{
    // Helper molt lleuger per resoldre la classe del model
    protected function modelClass(): string
    {
        if (!empty($this->class) && class_exists($this->class)) {
            return $this->class;
        }

        if (empty($this->model)) {
            abort(500, 'SCRUD misconfigured: $model not set in '.static::class);
        }

        // Accepta FQN o nom curt + namespace del controlador (si el té) o per defecte Intranet\Entities
        $candidate = ltrim($this->model, '\\');
        if (!class_exists($candidate)) {
            $ns = property_exists($this, 'namespace') ? $this->namespace : 'Intranet\\Entities\\';
            $candidate = $ns.$this->model;
        }
        if (!class_exists($candidate)) {
            abort(500, 'Model class not found: '.$this->model);
        }

        return $this->class = $candidate;
    }

    public function show($id)
    {
        $class = $this->modelClass();
        try {
            $elemento = $class::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return back()->with('warning', "No s'ha trobat {$class} #{$id}");
        }

        $modelo = $this->model;
        return view($this->chooseView('show'), compact('elemento', 'modelo'));
    }

    public function create($default = [])
    {
        $class = $this->modelClass();
        $formulario = new FormBuilder(new $class($default), $this->formFields);
        $modelo = $this->model;
        return view($this->chooseView('create'), compact('formulario', 'modelo'));
    }

    public function edit($id=null)
    {
        $class = $this->modelClass();
        try {
            $record = $class::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return back()->with('warning', "No s'ha trobat {$class} #{$id}");
        }

        $formulario = new FormBuilder($record, $this->formFields);
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('formulario', 'modelo'));
    }

    protected function createWithDefaultValues($default = [])
    {
        $class = $this->modelClass();
        return new $class($default);
    }

    protected function chooseView($view)
    {
        if (isset($this->vista[$view])){
            $vista = $this->vista[$view].".$view";
            if (view()->exists($vista)) {
                 return $vista;
            }
        }
        return 'intranet.'.$view;
    }
}
