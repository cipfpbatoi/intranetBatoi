<?php
/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */
namespace Intranet\Http\Controllers\Core;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Http\Traits\Core\SCRUD;
use Intranet\Services\Document\DocumentPathService;
use Styde\Html\Facades\Alert;

/**
 * Controlador base per a recursos intranet amb CRUD estàndard.
 *
 * Inclou:
 * - flux CRUD comú (store/update/destroy/active),
 * - utilitats de document (`document`, `gestor`),
 * - validació de formularis i normalització de checkboxes,
 * - resolució de model a través de `SCRUD`.
 */
abstract class IntranetController extends BaseController
{
    use SCRUD;

    /**
     * Acció de redirecció preferida després d'operacions de persistència.
     *
     * @var string|null
     */
    protected $redirect = null;

    /**
     * Calcula la redirecció de retorn després de store/update/destroy.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Elimina un registre i fitxers associats si escau.
     *
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $class = $this->modelClass();
        $elemento = $class::find($id);

        if (!$elemento) {
            return redirect()->back()->with('error', 'Element no trobat');
        }

        if ($elemento->fichero) {
            $this->borrarFichero($elemento->fichero);
        }

        if (method_exists($this, 'deleteAttached')) {
            $this->deleteAttached($id);
        }

        $elemento->delete();
        return $this->redirect();
    }

    /**
     * Esborra un fitxer del `public/` o `storage/app/` si la ruta és segura.
     *
     * @param string|null $fichero
     * @return void
     */
    protected function borrarFichero($fichero)
    {
        if (!$fichero || strlen($fichero) < 3) {
            return;
        }

        $paths = [
            public_path($fichero),
            storage_path('app/' . $fichero),
        ];

        $allowedRoots = array_filter([
            realpath(public_path()),
            realpath(storage_path('app')),
        ]);

        foreach ($paths as $path) {
            $directory = realpath(dirname($path));
            if ($directory === false) {
                continue;
            }

            $isAllowedPath = false;
            foreach ($allowedRoots as $root) {
                if (str_starts_with($directory, $root)) {
                    $isAllowedPath = true;
                    break;
                }
            }

            if (!$isAllowedPath) {
                continue;
            }

            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Guarda un nou registre.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->realStore($request);
        return $this->redirect();
    }

    /**
     * Crea o actualitza un registre i retorna la clau primària resultant.
     *
     * @param Request $request
     * @param int|string|null $id
     * @return mixed
     */
    protected function realStore(Request $request, $id = null)
    {
        $class = $this->modelClass();
        $elemento = $id ? $class::findOrFail($id) : new $class;
        $this->validateAll($request, $elemento);
        return $elemento->fillAll($request);
    }

    /**
     * Actualitza un registre existent.
     *
     * @param Request $request
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->realStore($request, $id);
        return $this->redirect();
    }

    /**
     * Alterna l'estat `activo` d'un registre.
     *
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function active($id)
    {
        $class = $this->modelClass();
        $elemento = $class::findOrFail($id);
        $elemento->update(['activo' => !$elemento->activo]);
        return $this->redirect();
    }

    /**
     * Retorna el document físic associat al registre.
     *
     * @param int|string $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function document($id)
    {
        $class = $this->modelClass();
        $elemento = $class::findOrFail($id);
        $pathService = new DocumentPathService();
        $path = $elemento->fichero ? storage_path('app/' . $elemento->fichero) : null;

        if ($path && $response = $pathService->responseFromPath($path)) {
            return $response;
        }

        Alert::danger(trans("messages.generic.nodocument"));
        return back();
    }

    /**
     * Redirigeix al gestor documental del registre si està enllaçat.
     *
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function gestor($id)
    {
        $class = $this->modelClass();
        $documento = $class::findOrFail($id)->idDocumento;
        return $documento
            ? redirect("/documento/$documento/show")
            : back()->with('error', trans("messages.generic.nodocument"));
    }

    /**
     * Valida el request segons les regles del model.
     *
     * @param Request $request
     * @param mixed $elemento
     * @return array
     */
    protected function validateAll($request, $elemento)
    {
        $rules = method_exists($elemento, 'getRules') ? $elemento->getRules() : [];
        return $this->validate($this->manageCheckBox($request, $elemento), $rules);
    }

    /**
     * Normalitza camps checkbox en el request abans de validar/guardar.
     *
     * @param Request $request
     * @param mixed $elemento
     * @return Request
     */
    protected function manageCheckBox($request, $elemento)
    {
        $checkboxData = [];

        foreach ($elemento->getFillable() as $property) {
            if (isset($elemento->getInputType($property)['type']) &&
                $elemento->getInputType($property)['type'] === 'checkbox') {
                $checkboxData[$property] = $request->has($property);
            }
        }

        if (!empty($checkboxData)) {
            $request->merge($checkboxData);
        }

        return $request;
    }
}
