<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;


class Empresa extends Model
{

    use BatoiModels;

    protected $table = 'empresas';
    protected $fillable = [ 'europa','sao','concierto','cif', 'nombre', 'email', 'direccion', 'localidad', 'telefono',
        'dual', 'actividad', 'delitos', 'menores','copia_anexe1','observaciones',
        'gerente', 'fichero', 'creador', 'idSao','data_signatura'];
    protected $rules = [
        'cif' => 'required|alpha_num',
        'nombre' => 'required|between:0,100',
        'email' => 'email',
        'concierto' => 'sometimes|required|unique:empresas,concierto',
        'direccion' => 'required|between:0,100',
        'localidad' => 'required|between:0,30',
        'telefono' => 'required|max:20'
    ];
    protected $inputTypes = [
        'europa' => ['type' => 'checkbox'],
        'dual' => ['type' => 'checkbox'],
        'delitos' => ['type' => 'checkbox'],
        'menores' => ['type' => 'checkbox'],
        'email' => ['type' => 'email'],
        'telefono' => ['type'=>'number'],
        'sao' => ['type' => 'checkbox'],
        'copia_anexe1' => ['type'=>'checkbox'],
        'observaciones' => ['type' => 'textarea'],
        'fichero' => ['type' => 'file'],
        'creador' => ['type' => 'hidden'],
        'idSao' => ['type' => 'hidden'],
        'data_signatura' => ['type' => 'date']
    ];
    protected $hidden = ['created_at', 'updated_at','creador'];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    protected $fileField = 'cif';

    protected $attributes = ['europa'=>0,'sao'=>1,'copia_anexe1'=>1];

    public function centros()
    {
        return $this->hasMany(Centro::class, 'idEmpresa', 'id');
    }
    public function colaboraciones()
    {
        return $this->hasManyThrough(Colaboracion::class, Centro::class, 'idEmpresa', 'idCentro', 'id');
    }
    
    public function scopeCiclo($query, $tutor)
    {
        $ciclo = Grupo::QTutor($tutor)->first()->idCiclo;
        $centros = Colaboracion::select('idCentro')->Ciclo($ciclo)->get()->toArray();
        $empreses = Centro::select('idEmpresa')->distinct()->whereIn('id', $centros)->get()->toArray();
        return $query->whereIn('id', $empreses);
    }

    public function scopeMenor($query, $fecha = null)
    {
        $hoy = $fecha ? new Date($fecha) : new Date();
        $hace18 = $hoy->subYears(18)->toDateString();
        return $query->where('fecha_nac', '>', $hace18);
    }


    public function getConveniNouAttribute()
    {
        $file = storage_path('app/' . $this->fichero);
        if (!$this->fichero || !file_exists($file)) {
            return false;
        } else {
            return  date("Y-m-d", filemtime($file)) > "2023-08-31";
        }
    }

    public function getConveniRenovatAttribute()
    {
        if (!$this->fichero) {
            return false;
        }
        $file = storage_path('app/' . $this->fichero);

        $date_file = date("Y-m-d", filemtime($file));

        $date1 = new Date($date_file);
        $date2 = new Date();

        $diferencia = $date2->diff($date1);
        return $diferencia->days < 120;

    }

    public function getRenovatConveniAttribute()
    {
        $file = storage_path('app/' . $this->fichero);
        $date_intranet = date("Y-m-d", filemtime($file));
        $date_sao = $this->data_signatura;

        $date1 = new Date($date_intranet);
        $date2 = new Date($date_sao);

        $diferencia = $date1->diff($date2);
        return $diferencia->days > 90;

    }
    public function getConveniCaducatAttribute()
    {
        $file = storage_path('app/' . $this->fichero);
        if (!$this->fichero || !file_exists($file)) {
            return true;
        }
        return  date("Y-m-d", filemtime($file)) < "2023-08-31";
     }

    public function getDataSignaturaAttribute($entrada)
    {
        if (!$entrada) {
            return '';
        }
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getCiclesAttribute()
    {
        $cicles = '';
        foreach ($this->centros as $centro) {
            foreach ($centro->colaboraciones as $colaboracion) {
                $cicles .= $colaboracion->XCiclo . ' ';
            }
        }
        return $cicles;
    }

}
