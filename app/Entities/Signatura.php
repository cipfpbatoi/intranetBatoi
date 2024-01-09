<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Signatura extends Model
{

    use BatoiModels;

    protected $table = 'signatures';
    protected $fillable = [
        'tipus',
        'idProfesor',
        'idSao',
        'sendTo',
        'signed'
    ];

    public function Fct()
    {
        return $this->belongsTo(AlumnoFct::class, 'idSao', 'idSao');
    }

    public function Teacher()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function deleteFile()
    {
        if (file_exists($this->routeFile)) {
            unlink($this->routeFile);
        }
    }

    public static function saveIfNotExists($anexe, $idSao , $signat = 0)
    {
        $anexo = 'A'.$anexe;
        $sig = Signatura::where('tipus', $anexo)->where('idSao', $idSao)->get()->first();
        if (!$sig) {
            $sig = new Signatura([
                'tipus' => $anexo,
                'idProfesor' => authUser()->dni,
                'idSao' => $idSao,
                'sendTo' => false,
                'signed' => $signat
            ]);
            $sig->save();
        } else {
            $sig->signed = $signat;
            $sig->sendTo = false;
            $sig->save();
        }
        return $sig;
    }

    public function getProfesorAttribute()
    {
        return $this->Teacher->shortName;
    }

    public function getAlumneAttribute()
    {
        return $this->Fct->Alumno->shortName;
    }

    public function getCentreAttribute()
    {
        return $this->Fct->Fct->Colaboracion->Centro->nombre;
    }

    public function getRouteFileAttribute()
    {
        return storage_path('app/annexes/')."{$this->tipus}_{$this->idSao}.pdf";
    }
     public function getSimpleRouteFileAttribute()
     {
         return 'app/annexes/'."{$this->tipus}_{$this->idSao}.pdf";
     }

    public function getEmailAttribute()
    {
        return $this->Fct->Fct->Colaboracion->email;
    }
    public function getContactoAttribute()
    {
        return $this->Fct->Fct->Colaboracion->contacto;
    }

    public function getSignAttribute()
    {
        return $this->signed ? 'Sí' : 'No';
    }

    public function getSendAttribute()
    {
        return $this->sendTo ? 'Sí' : 'No';
    }
}
