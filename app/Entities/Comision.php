<?php

namespace Intranet\Entities;

use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;
use Illuminate\Database\Eloquent\Model;

class Comision extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

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
    ];
    protected $visible = [
        'idProfesor',
        'servicio',
        'desde',
        'fct',
        'hasta',
        'alojamiento',
        'comida',
        'gastos',
        'kilometraje',
        'medio',
        'marca',
        'matricula',
        'itinerario',
    ];

    protected $inputTypes = [
        'idProfesor' => ['type' => 'hidden'],
        'medio' => ['type' => 'select'],
        'desde' => ['type' => 'datetime'],
        'hasta' => ['type' => 'datetime'],
        'fct' => ['type' => 'checkbox'],
        'servicio' => ['type' => 'textarea'],
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,

    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $descriptionField = 'servicio';

    protected $attributes = [
        'gastos' => 0.00,
        'comida' => 0.00,
        'alojamiento' => 0.00,
        'kilometraje' => 0 ,
        'medio' => 0
    ];


    public function Creador()
    {
        return $this->dni;
    }

    public function scopeActual($query)
    {
        return $query->where('idProfesor', '=', authUser()->dni);
    }

    public function scopeNext($query)
    {
        $fecHoy = time();
        $ahora = date("Y-m-d H:i:s", $fecHoy);
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
    public function Fcts()
    {
        return $this->belongsToMany(Fct::class, 'comision_fcts', 'idComision', 'idFct', 'id', 'id')
            ->withPivot(['hora_ini','aviso']);
    }

    public function getMedioOptions()
    {
        return config('auxiliares.tipoVehiculo');
    }

    public function getEstadoOptions()
    {
        return config('auxiliares.estadoDocumento');
    }

    public function getIdProfesorOptions()
    {
        return hazArray(
            Profesor::where('activo', 1)->orderBy('apellido1')->orderBy('apellido2')->get(),
            'dni',
            'nameFull'
        );
    }

    public function scopeDia($query, $dia)
    {
        $antes = $dia . " 23:59:59";
        $despues = $dia . " 00:00:00";
        return $query->where('desde', '<=', $antes)
                        ->where('hasta', '>=', $despues)
                        ->where('estado', '>=', 0);
    }
    public function getnombreAttribute()
    {
        return $this->Profesor->ShortName;
    }
    public function getsituacionAttribute()
    {
        return isblankTrans('models.Comision.' . $this->estado)
            ? trans('messages.situations.' . $this->estado)
            : trans('models.Comision.' . $this->estado);
    }
    public function getTotalAttribute()
    {
        $precioKilometro = config('auxiliares.precioKilometro');

        $kilometraje = isset($this->medio, $precioKilometro[$this->medio])
            ? $this->kilometraje * $precioKilometro[$this->medio]
            : 0;

        return $this->comida + $this->gastos + $this->alojamiento + $kilometraje;
    }

    public function getDescripcionAttribute()
    {
        $descripcion = $this->servicio." ";
        foreach ($this->Fcts as $fct) {
            $descripcion .= $fct->centro.",";
        }
        return trim($descripcion, ',');
    }

    public function getTipoVehiculoAttribute()
    {
        return config('auxiliares.tipoVehiculo')[$this->medio] ?? 'Desconocido';
    }

    public function showConfirm()
    {
        $falta = [
            'profesor' => $this->Profesor->fullName,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'servicio' => $this->servicio,
            'kilometraje' => $this->kilometraje,
        ];
        if ($this->itinerario) {
            $falta['itinerario'] = $this->itinerario;
        }

        if (count($this->Fcts)) {
            foreach ($this->Fcts as $fct) {
                $sobre = $fct->pivot->aviso?' <i class="fa fa-envelope"></i>':'';
                $falta["visita"][$fct->pivot->hora_ini] = $fct->Centro.$sobre;
            }
        }

        return $falta;
    }

}
