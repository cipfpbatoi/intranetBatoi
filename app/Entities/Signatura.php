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

    public function getEstatAttribute()
    {
        if ($this->tipus == 'A1') {
            return $this->getEstatA1();
        }
        if ($this->tipus == 'A2') {
            return $this->getEstatA2();
        }
        if ($this->tipus == 'A3') {
            return $this->getEstatA3();
        }
    }

    private function getEstatA1()
    {
        if ($this->signed) {
            if ($this->signed == 1){
                if ($this->sendTo == 1){
                    return 'Enviat a l\'instructor';
                } else {
                    return 'Signatura Direcció completada';
                }
            }
        } else {
            return 'Pendent de Signatura';
        }
    }

    private function getEstatA2()
    {
        if ($this->signed >= 1) {
            if ($this->signed == 2){
                if ($this->sendTo == 1){
                    return 'Enviat a l\'instructor';
                } else {
                    return 'Signatura Direcció completada';
                }
            }
            if ($this->signed == 1){
                if ($this->sendTo == 1){
                    return 'Enviat a l\'instructor';
                } else {
                    if (authUser()->hasCertificate){
                        return 'Pendent Signatura Direcció';
                    } else {
                        return 'Signatura Direcció completada';
                    }
                }
            }
        } else {
            return 'Pendent de Signatura';
        }
    }

    private function getEstatA3()
    {
        if ($this->signed == 2){
            if ($this->sendTo == 1){
                return 'Enviat a l\'instructor';
            } else {
                return 'Signes del centre completades';
            }
        } else {
            if ($this->sendTo == 1){
                return 'Enviat a l\'alumne';
            } else {
                return "Pendent d'enviar a l'alumne";
            }
        }
    }
}
