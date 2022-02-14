<?php

namespace Intranet\Entities;

use Jenssegers\Date\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intranet\Events\ActivityReport;
use Intranet\Events\PreventAction;

class Falta extends Model
{

    use BatoiModels;

    public $primaryKey = 'id';
    public $descriptionField = 'desde';
    
    protected $fillable = [
        'idProfesor',
        'baja',
        'dia_completo',
        'desde',
        'hasta',
        'hora_ini',
        'hora_fin',
        'motivos',
        'observaciones',
        'fichero',
        'estado'
    ];
    protected $rules = [
        'desde' => 'required|date',
        'hasta' => 'date',
        'motivos' => 'required',
        'observaciones' => 'max:200',
        'hora_ini' => 'required_if:dia_completo,0',
        'hora_fin' => 'required_if:dia_completo,0',
        'fichero' => 'mimes:pdf,jpg,jpeg,png'
    ];
    protected $inputTypes = [
        'idProfesor' => ['type' => 'hidden'],
        'estado' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'baja' => ['type' => 'hidden'],
        'dia_completo' => ['type' => 'checkbox'],
        'hora_ini' => ['type' => 'time'],
        'hora_fin' => ['type' => 'time'],
        'motivos' => ['type' => 'select'],
        'fecha' => ['type' => 'date'],
        'fichero' => ['type' => 'file']
    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'updating' => PreventAction::class,
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $attributes = ['dia_completo' => 1,'baja' => 0,'estado' => 0];

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getHastaAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getHorainiAttribute($salida)
    {
        $fecha = new Date($salida);
        return $fecha->format('H:i');
    }

    public function getHorafinAttribute($salida)
    {
        $fecha = new Date($salida);
        return $fecha->format('H:i');
    }

    public static function getMotivosOptions()
    {
        return config('auxiliares.motivoAusencia');
    }
    public function getIdProfesorOptions()
    {
        return hazArray(Profesor::orderBy('apellido1')->orderBy('apellido2')->orderBy('nombre')->Activo()->get(), 'dni', ['apellido1','apellido2','nombre']);
    }

    public function scopeDia($query, $dia)
    {
        return $query->where('desde', '<=', $dia)
                        ->where('hasta', '>=', $dia)
                        ->where('estado', '>=', 0);
    }
    
    public function getNombreAttribute(){
        return $this->Profesor->FullName;
    }
    public function getSituacionAttribute(){
        return isblankTrans('models.Falta.' . $this->estado) ? trans('messages.situations.' . $this->estado) : trans('models.Falta.' . $this->estado);
    }
    public function getMotivoAttribute(){
        return config('auxiliares.motivoAusencia')[$this->motivos];
    }
    
}
