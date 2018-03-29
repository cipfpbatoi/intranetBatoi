<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intranet\Events\FichaCreated;
use Intranet\Events\FichaSaved;

class Falta_profesor extends Model
{
    protected $table = 'faltas_profesores';
    protected $fillable = [
        'idProfesor',
        'dia',
        'entrada',
        'salida'];
    protected $dispatchesEvents = [
        'created' => FichaCreated::class,
    ];

    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }
    public function scopeHoy($query, $profesor)
    {
        return Falta_profesor::where('dia', '=', date("Y-m-d", time()))
                        ->where('idProfesor', $profesor)
                        ->get();
    }
    
    public function scopehaFichado($query, $dia, $profesor)
    {
        return Falta_profesor::where('dia', '=', $dia)
                        ->where('idProfesor', $profesor);
    }

    public static function fichar($profesor = null)
    {
        $profesor = ($profesor == null ? AuthUser()->dni : $profesor);
        $ultimo = Falta_profesor::Hoy($profesor)
                ->last();

        if (($ultimo == null) || ($ultimo->salida != null)) {
            $ultimo = new Falta_profesor;
            $ultimo->idProfesor = $profesor;
            $ultimo->dia = date("Y-m-d", time());
            $ultimo->entrada = date("H:i:s", time());
        } else{
            $ultimo->salida = date("H:i:s", time());
        }
        $ultimo->save();
        return $ultimo;
    }
    
    public static function fichaDia($profesor,$dia)
    {
        $fic = new Falta_profesor();
        $fic->idProfesor = $profesor;
        $fic->dia = $dia;
        $fic->entrada = "12:00:00";
        $fic->save();
    }
    

}
