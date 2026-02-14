<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;

use DB;
use Illuminate\Http\Request;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Falta;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Services\Notifications\AdviseTeacher;
use Intranet\Services\Notifications\ConfirmAndSend;
use Intranet\Services\General\StateService;
use Intranet\Services\School\TeacherSubstitutionService;
use Jenssegers\Date\Date;


/**
 * Class FaltaController
 * @package Intranet\Http\Controllers
 */
class FaltaController extends IntranetController
{

    use Imprimir, Autorizacion;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Falta';
    /**
     * @var array
     */
    protected $gridFields = ['id', 'desde', 'hasta', 'motivo', 'situacion','observaciones'];
    /**
     * @var bool
     */
    protected $modal = true;

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('falta.delete', ['where' => ['estado', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('falta.edit', ['where' => ['estado', '<', '3']]));
        $this->panel->setBoton('grid', new BotonImg('falta.init', ['where' => ['estado', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('falta.document', ['where' => ['fichero', '!=', '']]));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $request->merge(['baja' => $request->boolean('baja') ? 1 : 0]);

        if ($request->baja) {
            DB::transaction(function () use ($request) {
                $request->merge([
                    'hora_ini' => null,
                    'hora_fin' => null,
                    'hasta' => '',
                    'dia_completo' => 1,
                    'estado' => 5,
                ]);
                app(TeacherSubstitutionService::class)->markLeave($request->idProfesor, $request->desde);
                parent::realStore($request);
            });
        } else {
            $diaCompleto = isset($request->dia_completo)  ? 1 : null;
            $request->hora_ini = $diaCompleto ? null : $request->hora_ini;
            $request->hora_fin = $diaCompleto ? null : $request->hora_fin;
            $request->hasta = esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta;
            $id = parent::realStore($request);
            if (UserisAllow(config('roles.rol.direccion'))) {
                $this->init($id);
            } else {
                // si es direcció autoritza
                return ConfirmAndSend::render($this->model, $id);
            }
        }
        return $this->redirect();
    }

    

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {

        $diaCompleto = $request->has('dia_completo') ? 1 : 0;

        $request->merge([
            'hora_ini' => $diaCompleto ? null : $request->hora_ini,
            'hora_fin' => $diaCompleto ? null : $request->hora_fin,
            'hasta' => esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta,
        ]);

        $elemento = Falta::find(parent::realStore($request, $id));

        if ($elemento->estado == 1 && $elemento->fichero) {
            $staSer = new StateService($elemento);
            $staSer->putEstado(2);
        }

        return $this->redirect();
    }

    protected function createWithDefaultValues($default = [])
    {
        $data = new Date('today');
        return new Falta(['desde'=>$data,'hasta'=>$data,'idProfesor'=>AuthUser()->dni]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function init($id)
    {
        $elemento = Falta::findOrFail($id);
        if (esMayor($elemento->desde, Hoy('Y/m/d'))) {
            app(AdviseTeacher::class)->sendTutorEmail($elemento);
        }
        $stSrv = new StateService($elemento);
        if ($elemento->fichero) {
            $stSrv->putEstado(2);
        } else {
            $stSrv->putEstado(1);
        }
        return $this->redirect();
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function alta($id)
    {
        $elemento = Falta::findOrFail($id);
        DB::transaction(function () use ($elemento) {
            $elemento->estado = 3;
            $elemento->hasta = new Date();
            $elemento->baja = 0;
            $elemento->save();
            // LLeva la baixa del professor.
            app(TeacherSubstitutionService::class)->reactivate($elemento->idProfesor);
        });
        return back()->with('pestana', $elemento->estado);
    }
}
