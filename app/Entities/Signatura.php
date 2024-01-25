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
            $sig->signed += $signat;
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

    public function getEstatAttribute()
    {
        $nameFunction = 'getEstat'.$this->tipus;
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
        if ($sig->signed) {
            return 'Pendent Signatura Direcció';
        }
        return 'Pendent de Signatura';
    }

    private static function getEstatA3(Signatura $sig)
    {
        if ($sig->sendTo > 1) {
            if ($sig->signed == 3) {
                return "Enviat a l'instructor";
            } else {
                return "Enviat a l'instructor sense la signatura de l'alumne";
            }
        }
        if ($sig->sendTo == 1){
            if ($sig->signed == 3){
                return 'Signes del centre completades';
            } else {
                return "Enviat a l'alumne";
            }
        }
        return "Pendent d'enviar a l'alumne";
    }
}
