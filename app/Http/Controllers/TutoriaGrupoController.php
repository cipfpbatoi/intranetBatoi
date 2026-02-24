<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\Entities\TutoriaGrupo;
use Intranet\Entities\Tutoria;
use Intranet\Http\Requests\TutoriaGrupoStoreRequest;
use Intranet\Http\Requests\TutoriaGrupoUpdateRequest;
use Intranet\Presentation\Crud\TutoriaGrupoCrudSchema;
use Intranet\Services\UI\FormBuilder;

class TutoriaGrupoController extends ModalController
{

    protected $perfil = 'profesor';
    protected $model = 'TutoriaGrupo';
    protected $gridFields = TutoriaGrupoCrudSchema::GRID_FIELDS;
    protected $formFields = TutoriaGrupoCrudSchema::FORM_FIELDS;
    protected $redirect = 'TutoriaController@index';
    
    public function createfrom($tutoria,$grupo)
    {
        return $this->create(['idTutoria' => $tutoria,'idGrupo'=>$grupo]);
    }

    public function create($default = [])
    {
        $formulario = new FormBuilder($this->createWithDefaultValues($default), $this->formFields);
        $modelo = $this->model;
        return view('intranet.create', compact('formulario', 'modelo'));
    }

    public function edit($id = null)
    {
        try {
            $record = TutoriaGrupo::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return back()->with('warning', "No s'ha trobat {$this->model} #{$id}");
        }

        $formulario = new FormBuilder($record, $this->formFields);
        $modelo = $this->model;
        return view('intranet.edit', compact('formulario', 'modelo'));
    }

    public function store(TutoriaGrupoStoreRequest $request)
    {
        $this->persist($request);
        return $this->redirect();
    }

    public function update(TutoriaGrupoUpdateRequest $request, $id)
    {
        $this->persist($request, $id);
        return $this->redirect();
    }
    
    public function search()
    {
        $tutoria = Tutoria::query()->find($this->search);
        $this->titulo = ['que' => $tutoria->descripcion ?? ''];
        return TutoriaGrupo::where('idTutoria','=',$this->search)->get();
    }

    public function iniBotones()
    {
        $this->panel->setBotonera([], ['show']);
    }

}
