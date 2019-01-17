<?php

namespace Intranet\Entities;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Auth;
use Intranet\Events\ActivityReport;
use \DB;
use Intranet\Events\PreventAction;
use Illuminate\Support\Str;

class Comision extends Model
{

    use BatoiModels,TraitEstado;

    protected $table = 'comisiones';
    protected $fillable = [
        'idProfesor',
        'desde',
        'hasta',
        'fct',
        'servicio',
        'alojamiento',
        'comida',
        'gastos',
        'kilometraje',
        'medio',
        'marca',
        'matricula',
        'itinerario',
        'otros',
        
    ];
    protected $visible = [
        'idProfesor',
        'servicio',
        'desde',
        'hasta',
        'alojamiento',
        'comida',
        'gastos',
        'kilometraje',
        'medio',
        'marca',
        'matricula',
        'itinerario',
        'otros',
        
    ];
    protected $rules = [
        'servicio' => 'required',
        'kilometraje' => 'required|integer',
        'desde' => 'required|date|after:tomorrow',
        'hasta' => 'required|date|after:desde',
        'alojamiento' => 'required|numeric',
        'comida' => 'required|numeric',
        'gastos' => 'required|numeric',
        'medio' => 'required|max:30',
        'marca' => 'required|max:30',
        'matricula' => 'required|max:10'
    ];
    protected $inputTypes = [
        'idProfesor' => ['type' => 'hidden'],
        'otros' => ['type' => 'select'],
        'servicio' => ['type' => 'textarea'],
        'desde' => ['type' => 'datetime'],
        'hasta' => ['type' => 'datetime'],
        'fct' => ['type' => 'checkbox'],
    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'saving' => PreventAction::class,
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $descriptionField = 'servicio';

    public function __construct()
    {
        if (AuthUser()) {
            $this->idProfesor = AuthUser()->dni;
            $manana = new Date('tomorrow');
            $manana->addHours(8);
            $this->desde = $manana;
            $this->hasta = $manana;
            $this->fct = 1;
            $this->gastos = 0.00;
            $this->comida = 0.00;
            $this->alojamiento = 0.00;
            $this->kilometraje = 0;
        }
    }

    public function scopeActual($query)
    {
        return $query->where('idProfesor', '=', AuthUser()->dni);
    }

    public function scopeNext($query)
    {
        $fec_hoy = time();
        $ahora = date("Y-m-d H:i:s", $fec_hoy);
        return $query->where('desde', '>', $ahora);
    }

    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y H:i');
    }

    public function getHastaAttribute($salida)
    {
        $fecha = new Date($salida);
        return $fecha->format('d-m-Y H:i');
    }

    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function getOtrosOptions()
    {
        return config('auxiliares.tipoVehiculo');
    }

    public function getEstadoOptions()
    {
        return config('auxiliares.estadoDocumento');
    }

    public function scopeDia($query, $dia)
    {
        $antes = $dia . " 23:59:59";
        $despues = $dia . " 00:00:00";
        return $query->where('desde', '<=', $antes)
                        ->where('hasta', '>=', $despues)
                        ->where('estado', '>=', 0);
    }
    public function getnombreAttribute(){
        return $this->Profesor->ShortName;
    }
    public function getsituacionAttribute(){
        return isblankTrans('models.Comision.' . $this->estado) ? trans('messages.situations.' . $this->estado) : trans('models.Comision.' . $this->estado);
    }
    public function getTotalAttribute(){
        return $this->comida + $this->gastos + $this->alojamiento + ($this->kilometraje * config('variables.precioKilometro'));
    }
//    public function setServicioAttribute($value){
//        $this->attributes['servicio'] = $value;
//        $this->attributes['slug'] = Str::slug($value);
//    }
}
