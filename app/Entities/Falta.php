<?php

namespace Intranet\Entities;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intranet\Events\ActivityReport;
use Intranet\Presentation\Crud\FaltaCrudSchema;

/**
 * Model de faltes.
 */
class Falta extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

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
    
    protected $rules = FaltaCrudSchema::RULES;
    protected $inputTypes = FaltaCrudSchema::INPUT_TYPES;
    /**
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
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
        $fecha = new Carbon($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getHastaAttribute($entrada)
    {
        return $this->getDesdeAttribute($entrada);
    }

    public function getHorainiAttribute($salida)
    {
        $fecha = new Carbon($salida);
        return $fecha->format('H:i');
    }

    public function getHorafinAttribute($salida)
    {
        return $this->getHorainiAttribute($salida);
    }

    public function getDesdeHoraAttribute()
    {
        if (!$this->horaIni) {
            return $this->desde;
        }
        return $this->desde.substr($this->horaini, 0, 5);
    }


    public static function getMotivosOptions()
    {
        return config('auxiliares.motivoAusencia');
    }
    public function getIdProfesorOptions()
    {
        return hazArray(
            Profesor::orderBy('apellido1')->orderBy('apellido2')->orderBy('nombre')->Activo()->get(),
            'dni',
            ['apellido1','apellido2','nombre']
        );
    }

    public function scopeDia($query, $dia)
    {
        return $query->where('desde', '<=', $dia)
                        ->where('hasta', '>=', $dia)
                        ->where('estado', '>=', 0);
    }
    
    public function getNombreAttribute()
    {
        return $this->Profesor->FullName;
    }
    public function getSituacionAttribute()
    {
        return isblankTrans('models.Falta.' . $this->estado) ?
            trans('messages.situations.' . $this->estado) :
            trans('models.Falta.' . $this->estado);
    }
    public function getMotivoAttribute()
    {
        if (fechaInglesa($this->desde) <= '2024-02-29' && $this->motivos < 8) {
            return config('auxiliares.motivoAusenciaOld')[$this->motivos];
        }
        return config('auxiliares.motivoAusencia')[$this->motivos];
    }

    public function showConfirm()
    {
        $falta = [
            'profesor' => $this->Profesor->fullName,
            'dia_completo' => $this->dia_completo?'SI':'NO',
            'motivos' => config('auxiliares.motivoAusencia')[$this->motivos],
            'observaciones' => $this->observaciones,
            'fichero' => $this->fichero?'SI':'NO',
            'desde' => $this->desde,
        ];
        if ($this->desde != $this->hasta) {
            $falta['hasta'] = $this->hasta;
        }
        if (!$this->dia_completo) {
            $falta['hora_ini'] = $this->hora_ini;
            $falta['hora_fin'] = $this->hora_fin;
        }

        return $falta;
    }

    public function getHoraIniOptions()
    {
        $horas = Hora::all();
        $arrayHoras = [];
        foreach ($horas as $hora) {
            $stringHora = $hora->hora_ini.':00';
            $arrayHoras[$stringHora] = $hora->hora_ini;
        }
        return $arrayHoras;
    }

    public function getHoraFinOptions()
    {
        $horas = Hora::all();
        $arrayHoras = [];
        foreach ($horas as $hora) {
            $stringHora = $hora->hora_fin.':00';
            $arrayHoras[$stringHora] = $hora->hora_fin;
        }
        return $arrayHoras;
    }

}
