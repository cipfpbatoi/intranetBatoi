<?php

namespace Intranet\Http\Traits;

use Intranet\Services\UI\FormBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Trait de suport per a operacions bàsiques de tipus SCRUD en controllers.
 *
 * Responsabilitats:
 * - resoldre la classe de model a partir de `$class` o `$model`,
 * - oferir fluxos bàsics de `show/create/edit`,
 * - resoldre la vista adequada segons configuració de `$vista`.
 */
trait SCRUD
{
    /**
     * Resol la FQCN del model i la guarda en `$this->class`.
     *
     * @return string
     */
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

    /**
     * Mostra el detall d'un registre.
     *
     * @param int|string $id Identificador del registre.
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
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

    /**
     * Mostra el formulari de creació.
     *
     * @param array $default Valors inicials del model.
     * @return \Illuminate\Contracts\View\View
     */
    public function create($default = [])
    {
        $class = $this->modelClass();
        $formulario = new FormBuilder(new $class($default), $this->formFields);
        $modelo = $this->model;
        return view($this->chooseView('create'), compact('formulario', 'modelo'));
    }

    /**
     * Mostra el formulari d'edició.
     *
     * @param int|string|null $id Identificador del registre.
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
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

    /**
     * Crea una nova instància del model amb valors per defecte.
     *
     * @param array $default
     * @return mixed
     */
    protected function createWithDefaultValues($default = [])
    {
        $class = $this->modelClass();
        return new $class($default);
    }

    /**
     * Retorna la vista per a una acció CRUD concreta.
     *
     * Ordre de resolució:
     * 1) Si no hi ha `$vista`, usa `intranet.<view>`.
     * 2) Si `$vista[$view]` és una ruta completa existent (conté `.`), la retorna.
     * 3) Si existeix `<configured>.<view>`, la retorna.
     * 4) Fallback a `intranet.<view>`.
     *
     * @param string $view Acció (`show`, `create`, `edit`, ...).
     * @return string
     */
    protected function chooseView($view)
    {
        if (!isset($this->vista)) {
            return 'intranet.'.$view;
        }

        if (is_array($this->vista) && isset($this->vista[$view])) {
            $configured = strtolower($this->vista[$view]);

            if (strpos($configured, '.') !== false && view()->exists($configured)) {
                return $configured;
            }

            $candidate = $configured.'.'.$view;
            if (view()->exists($candidate)) {
                return $candidate;
            }
        }

        return 'intranet.'.$view;
    }
}
