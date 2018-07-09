<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\ActividadCreated;
use Intranet\Events\ActivityReport;
use Intranet\Events\PreventAction;
use Intranet\Entities\Profesor;
use Intranet\Entities\TipoDocumento;
use Intranet\Entities\Programacion;

class Documento extends Model
{

    use BatoiModels;

    protected $table = 'documentos';
    protected $fillable = ['tipoDocumento', 'rol', 'curso', 'propietario', 'supervisor', 'descripcion'
        , 'ciclo', 'grupo', 'detalle','enlace', 'fichero', 'tags'];
    protected $rules = [
        'tipoDocumento' => 'required',
        'descripcion' => 'required',
        'fichero' => 'sometimes|mimes:pdf,zip',
    ];
    protected $inputTypes = [
        'tipoDocumento' => ['type' => 'select'],
        'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']],
        'fichero' => ['type' => 'file'],
        'rol' => ['type' => 'hidden'],
        'propietario' => ['disabled' => 'disabled'],
        'grupo' => ['type' => 'select'],
        'supervisor' => ['type' => 'hidden'],
        'ciclo' => ['type' => 'hidden'],
        'detalle' => ['type' => 'textarea']
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    public function getCreatedAtAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function __construct()
    {
        $this->tipoDocumento = 'Fichero';
        $this->curso = Curso();
        $this->propietario = 'CIP FP BATOI';
    }


    public function getGrupoOptions()
    {
        return config('auxiliares.actasEnabled');
    }

    public function getTipoDocumentoOptions()
    {
        return TipoDocumento::allPestana();
    }

    

    public function getExistAttribute()
    {
        if (!$this->idDocumento) return false;
        $tD = ($this->tipoDocumento == 'Acta')?'Reunion':$this->tipoDocumento;
        $clase = 'Intranet\Entities\\'.$tD;
        return $clase::find($this->idDocumento)?true:false;
    }

    public function getSituacionAttribute()
    {
        if ($this->link)
            if ($this->exist) return 'All';
            else return 'Linked';
        else
            if ($this->exist) return 'NoLink';
            else return 'Nothing';
    }
    
    public static function crea($elemento, $parametres = null)
    {
        if (isset($elemento->fichero) && $doc = Documento::where('fichero', $elemento->fichero)->first()) {
            $doc->llena($parametres);
        } else {
            $doc = new Documento();
            $doc->llena($parametres);
            $doc->curso = Curso();
            $doc->supervisor = $doc->supervisor == '' ? AuthUser()->FullName : $doc->supervisor;
            //$doc->tipoDocumento = $doc->tipoDocumento == '' ? $this->model : $doc->tipoDocumento;
            if ($elemento) {
                $doc->idDocumento = $doc->idDocumento == '' ? isset($elemento->id) ? $elemento->id : $elemento->$primaryKey : $doc->tipoDocumento;
                $doc->propietario = $doc->propietario == '' ? $elemento->Profesor->FullName : $doc->propietario;
                $doc->fichero = $doc->fichero == '' ? $elemento->fichero : $doc->fichero;
                $doc->descripcion = $doc->descripcion == '' ? 'Registre dia ' . Hoy('d-m-Y') : $doc->descripcion;
            } else {
                $doc->propietario = $doc->propietario == '' ? AuthUser()->FullName : $doc->propietario;
                $doc->descripcion = $doc->descripcion == '' ? 'Registre dia ' . Hoy('d-m-Y') : $doc->descripcion;
                $doc->tags = $doc->tags == '' ? 'listado llistat autorizacion autorizacio' : $doc->tags;
                $doc->rol = $doc->rol == '' ? '2' : $doc->rol;
            }
        }
        $doc->save();
    }

}
