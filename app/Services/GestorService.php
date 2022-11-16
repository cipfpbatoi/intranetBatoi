<?php


namespace Intranet\Services;

use Intranet\Entities\Documento;
use Styde\Html\Facades\Alert;

class GestorService
{
    private $elemento;
    private $document;
    private $link;
    private $isFile;

    public function __construct($elemento=null, $documento=null)
    {
        $this->elemento = $elemento;
        $this->document = $documento??$this->findDocument();
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

    private function findDocument()
    {
        if (isset($this->elemento)) {
            if ($this->elemento->idDocumento) {
                return Documento::find($this->elemento->idDocumento);
            } else {
                return isset($this->elemento->fichero)
                    ? Documento::where('fichero', $this->elemento->fichero)->first()
                    : null;
            }
        }
        return null;
    }

    public function save($parametres = null)
    {
        if (isset($this->document)) {
            if ($parametres) {
                $this->update($parametres);
            }
        } else {
            $this->document = new Documento($parametres);
            $this->document->curso = curso();
            if (empty($this->document->supervisor)) {
                $this->document->supervisor = authUser()->FullName;
            }
            if (empty($this->document->descripcion)) {
                $this->document->descripcion = 'Registre dia ' . hoy('d-m-Y');
            }
            if ($el = $this->elemento) {
                if (empty($this->document->idDocumento)) {
                    $primaryKey = $el->primaryKey??null;
                    $this->document->idDocumento = $el->id ?? $el->$primaryKey ?? $this->document->tipoDocumento;
                }
                if (empty($this->document->propietario)) {
                    $this->document->propietario = $el->Profesor->FullName ?? '';
                }
                if (empty($this->document->fichero)) {
                    $this->document->fichero = $el->fichero;
                }
            } else {
                if (empty($this->document->propietario)) {
                    $this->document->propietario = authUser()->FullName;
                }
                if (empty($this->document->tags)) {
                    $this->document->tags = 'listado llistat autorizacion autorizacio';
                }
                if (empty($this->document->rol)) {
                    $this->document->rol = 2;
                }
            }
        }
        $this->document->save();
        return $this->document->id;
    }




    public function render()
    {
        if ($this->isAllowed()) {
            if ($this->isFile && file_exists($this->link)) {
                return response()->file($this->link);
            }
            if ($this->isFile === false) {
                return redirect($this->link);
            }
            Alert::warning(trans("messages.generic.nodocument"));

        } else {
            Alert::warning('Sense Permisos');
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
        } else {
            $this->isFile = false;
            $this->link = null;
        }
    }

    private function isAllowed()
    {
        if ($this->document && !in_array($this->document->rol, rolesUser(authUser()->rol))) {
            return false;
        }
        return true;
    }
}
