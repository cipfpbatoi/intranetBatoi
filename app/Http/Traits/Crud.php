<?php

namespace Intranet\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;
use Intranet\Services\Document\DocumentPathService;

trait Crud
{
    use SCRUD;

    protected $redirect = null;

    protected function redirect()
    {
        if (Session::get('redirect')) {
            $this->redirect = Session::get('redirect');
        }

        if ($this->redirect) {
            return isset($this->search)
                ? redirect()->action($this->redirect, $this->search)
                : redirect()->action($this->redirect);
        }

        return redirect()->action($this->model . 'Controller@index');
    }

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

    protected function borrarFichero($fichero)
    {
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

    public function store(Request $request)
    {
        $this->realStore($request);
        return $this->redirect();
    }

    protected function realStore(Request $request, $id = null)
    {
        $elemento = $id ? $this->class::findOrFail($id) : new $this->class;
        $this->validateAll($request, $elemento);
        return $elemento->fillAll($request);
    }

    public function update(Request $request, $id)
    {
        $this->realStore($request, $id);
        return $this->redirect();
    }

    public function active($id)
    {
        $elemento = $this->class::findOrFail($id);
        $elemento->update(['activo' => !$elemento->activo]);
        return $this->redirect();
    }

    public function document($id)
    {
        $elemento = $this->class::findOrFail($id);
        $pathService = new DocumentPathService();
        $path = $elemento->fichero ? storage_path('app/' . $elemento->fichero) : null;

        if ($path && $response = $pathService->responseFromPath($path)) {
            return $response;
        }

        Alert::danger(trans("messages.generic.nodocument"));
        return back();
    }

    public function gestor($id)
    {
        $documento = $this->class::findOrFail($id)->idDocumento;
        return $documento
            ? redirect("/documento/$documento/show")
            : back()->with('error', trans("messages.generic.nodocument"));
    }

    protected function validateAll($request, $elemento)
    {
        $rules = method_exists($this->class, 'getRules') ? $elemento->getRules() : [];
        return $this->validate($this->manageCheckBox($request, $elemento), $rules);
    }

    protected function manageCheckBox($request, $elemento)
    {
        foreach ($elemento->getFillable() as $property) {
            if (isset($elemento->getInputType($property)['type']) &&
                ($elemento->getInputType($property)['type'] === 'checkbox')) {
                $request->$property = $request->has($property);
            }
        }
        return $request;
    }
}
