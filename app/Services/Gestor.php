<?php


namespace Intranet\Services;

use Intranet\Entities\Documento;
use Styde\Html\Facades\Alert;

class Gestor
{
    private $elemento;
    private $document;
    private $link;
    private $isFile;

    public function __construct($elemento=null,$documento=null){
        $this->elemento = $elemento;
        if (isset($documento)){
            $this->document = $documento;
        } else {
            $this->document = $this->elemento->idDocumento ? Documento::find($this->elemento->idDocumento) : (isset($elemento->fichero) ? Documento::where('fichero', $elemento->fichero)->first() : null);
        }
        if ($this->document) {
            if (isset($this->document->enlace)) {
                $this->link = $this->document->enlace;
                $this->isFile = false;
            }

            if (isset($this->document->fichero)) {
                $this->link = storage_path('app/' . $this->document->fichero);
                $this->isFile = true;
            }
        } else {
            $this->getFileIfExistFromModel();
        }
    }


    public function save($parametres = null)
    {
        if (isset($this->document)){
            if ($parametres){
                $this->update($parametres);
            }
        }
        else {
            $this->document = new Documento($parametres);
            $this->document->curso = Curso();
            $this->document->supervisor = $this->document->supervisor == '' ? AuthUser()->FullName : $this->document->supervisor;
            if ($this->elemento) {
                $this->document->idDocumento = $this->document->idDocumento == '' ? isset($this->elemento->id) ? $this->elemento->id : $this->elemento->$primaryKey : $this->document->tipoDocumento;
                $this->document->propietario = $this->document->propietario == '' ? (isset($this->elemento->Profesor) ? $this->elemento->Profesor->FullName:'') : $this->document->propietario;
                $this->document->fichero = $this->document->fichero == '' ? $this->elemento->fichero : $this->document->fichero;
                $this->document->descripcion = $this->document->descripcion == '' ? 'Registre dia ' . Hoy('d-m-Y') : $this->document->descripcion;
            } else {
                $this->document->propietario = $this->document->propietario == '' ? AuthUser()->FullName : $this->document->propietario;
                $this->document->descripcion = $this->document->descripcion == '' ? 'Registre dia ' . Hoy('d-m-Y') : $this->document->descripcion;
                $this->document->tags = $this->document->tags == '' ? 'listado llistat autorizacion autorizacio' : $this->document->tags;
                $this->document->rol = $this->document->rol == '' ? '2' : $this->document->rol;
            }
        }
        $this->document->save();
        return $this->document->id;
    }




    public function render()
    {
        if ($this->isAllowed()){
            if ($this->isFile && file_exists($this->link)){
                return response()->file($this->link);
            }
            if ($this->isFile === false) {
                return redirect($this->link);
            }
            Alert::danger(trans("messages.generic.nodocument"));

        } else {
            Alert::danger('Sense Permisos');
        }
        return back();
    }

    private function update($parametres)
    {
        foreach ($parametres as $key => $valor) {
            $this->document->$key = $valor;
        }
    }

    private function getFileIfExistFromModel()
    {
        if (isset($this->elemento->fichero)) {
            $this->isFile = true;
            $this->link = storage_path('app/' . $this->elemento->fichero);
        }
        else {
            $this->isFile = false;
            $this->link = null;
        }
    }

    private function isAllowed(){
        if ($this->document){
            if (in_array($this->document->rol, RolesUser(AuthUser()->rol))) {
                return true;
            }
            return false;
        }
        return true;
    }

}