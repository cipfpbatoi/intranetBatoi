<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Intranet\Application\Documento\DocumentoLifecycleService;
use Intranet\Events\ActivityReport;
use Intranet\Presentation\Crud\DocumentoCrudSchema;
use Intranet\Services\Document\DocumentPathService;
use Intranet\Services\Document\TipoDocumentoService;


class Documento extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    protected $table = 'documentos';
    protected $fillable = ['tipoDocumento', 'rol', 'curso', 'propietario', 'supervisor', 'descripcion'
        , 'ciclo', 'grupo', 'detalle','enlace', 'fichero', 'tags', 'activo'];
    protected $rules = DocumentoCrudSchema::RULES;
    protected $inputTypes = DocumentoCrudSchema::INPUT_TYPES;
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    protected $attributes = ['tipoDocumento'=>'Fichero'];

    public function getCreatedAtAttribute($entrada)
    {
        $fecha = new Carbon($entrada);
        return $fecha->format('d-m-Y');
    }


    public function getGrupoOptions()
    {
        return config('auxiliares.actasEnabled');
    }

    public function getTipoDocumentoOptions()
    {
        return TipoDocumentoService::allPestana();
    }

    public function getExistAttribute()
    {
        if (!$this->idDocumento) {
            return false;
        }
        $tD = ($this->tipoDocumento == 'Acta')?'Reunion':$this->tipoDocumento;
        $clase = 'Intranet\Entities\\'.$tD;
        if (!class_exists($clase)) {
            return false;
        }
        return $clase::find($this->idDocumento)?true:false;
    }

    public function getSituacionAttribute()
    {
        if ($this->link) {
            if ($this->exist) {
                return 'All';
            }
            return 'Linked';
        }
        if ($this->exist) {
            return 'NoLink';
        }
        return 'Nothing';
    }
    
    public function getLinkAttribute()
    {
        $path = isset($this->fichero) ? storage_path('app/' . $this->fichero) : null;
        if (!$path) {
            return false;
        }

        return (new DocumentPathService())->existsPath($path);
    }


    public function deleteDoc()
    {
        return app(DocumentoLifecycleService::class)->delete($this);
    }



}
