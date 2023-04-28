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

    ];

    public function Fct()
    {
        return $this->belongsTo(AlumnoFct::class, 'idSao', 'idSao');
    }

    public function Teacher()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor','dni');
    }

    public static function saveIfNotExists($anexe, $idSao, $sendTo=true)
    {
        $anexo = 'A'.$anexe;
        $sig = Signatura::where('tipus', $anexo)->where('idSao', $idSao)->get()->first();
        if (!$sig) {
            $sig = new Signatura([
                'tipus' => $anexo,
                'idProfesor' => authUser()->dni,
                'idSao' => $idSao,
                'sendTo' => $sendTo,
                'signed' => $anexo === 'A3' ? true : false
            ]);
            $sig->save();
        } else {
            $sig->signed = $anexo === 'A3' ? true : false;
            $sig->sendTo = $sendTo;
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
}
