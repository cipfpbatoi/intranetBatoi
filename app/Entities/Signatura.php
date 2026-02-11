<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Signatura extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    public $mail = null;
    public $contact = null;

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

    public function Alumno(){
        return $this->hasOneThrough(Alumno::class,AlumnoFct::class,'idSao','nia','idSao','idAlumno');
    }

    public function deleteFile()
    {
        if (file_exists($this->routeFile)) {
            unlink($this->routeFile);
        }
    }

    public static function saveIfNotExists($anexe, $idSao , $signat = 0)
    {
        $sig = Signatura::where('tipus', $anexe)->where('idSao', $idSao)->get()->first();
        if (!$sig) {
            $sig = new Signatura([
                'tipus' => $anexe,
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
    public function getPathAttribute()
    {
        return storage_path('app/annexes/');
    }

    public function getFileNameAttribute()
    {
        return "{$this->tipus}_{$this->idSao}.pdf";
    }

    public function getRouteFileAttribute()
    {
        return $this->path.$this->fileName;
    }
     public function getSimpleRouteFileAttribute()
     {
         return 'app/annexes/'."{$this->tipus}_{$this->idSao}.pdf";
     }

    public function getEmailAttribute()
    {
        return $this->Fct->Fct->Instructor->email;
    }
    public function getContactoAttribute()
    {
        return $this->Fct->Fct->Instructor->contacto;
    }

    public function getSignAttribute()
    {
        return $this->signed ? 'Sí' : 'No';
    }

    public function getSendAttribute()
    {
        return $this->sendTo ? 'Sí' : 'No';
    }

    public function getEstatAttribute()
    {
        $tipus = substr($this->tipus, 0,2);
        $nameFunction = 'getEstat'.$tipus;
        return self::$nameFunction($this);
    }

    private static function getEstatA1(Signatura $sig)
    {
        if ($sig->sendTo){
            return "Enviat a l'instructor";
        }
        if ($sig->signed == 3) {
            return 'Signatura Direcció completada';
        }
        return 'Pendent Signatura Direcció';
    }

    private static function getEstatA2(Signatura $sig)
    {
        if ($sig->sendTo) {
            return 'Enviat a l\'instructor';
        }
        if ($sig->signed > 2 ){
            return 'Signatura Direcció completada';
        }
        return 'Pendent de Signatura Direcció';
    }

    private static function getEstatA3(Signatura $sig)
    {
        if ($sig->sendTo > 0) {
            if ($sig->signed == 3) {
                return "Enviat a l'instructor";
            }
            if ($sig->signed == 2 && $sig->sendTo == 2) {
                return "Enviat a l'instructor sense la signatura de l'alumne";
            }
            return "Enviat a l'alumne";
        } else {
            if ($sig->signed == 2) {
                return "Pendent enviar a l'alumne";
            } else {
                return "Pendent enviar a l'instructor";
            }
        }
    }

    private function getEstatA5()
    {
        return 'Complet';
    }

    public function getClassAttribute()
    {
        $tipus = substr($this->tipus, 0,2);
        if ($tipus == 'A3' && $this->sendTo == 1 && $this->signed == 2){
            return 'bg-orange';
        }
        if ($this->signed >= 3) {
            if ($this->sendTo >= 1) {
                return 'bg-blue-sky';
            } else {
                return 'bg-green';
            }
        }
        return ($this->signed >= 3) ? 'bg-blue-sky':'bg-red';
    }

    public function getFctOptions()
    {
        $user = AuthUser();
        $alumnos  = AlumnoFct::misFcts($user->dni)->get()->map(function ($fct) {
             return [  $fct->idSao  => $fct->Alumno->fullName];
        });
        $array = array();

        foreach ($alumnos->toArray() as $tmp) {
            foreach ($tmp as $key => $value) {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    public function getTipusOptions()
    {
        return [
            'A1' => 'A1',
            'A2' => 'A2',
            'A3' => 'A3',
            'A5' => 'A5',
        ];
    }
}
