<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\FichaCreated;
use Intranet\Events\FichaSaved;
use Jenssegers\Date\Date;

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
        return $query->where('dia', '=', date("Y-m-d", time()))
            ->where('idProfesor', $profesor);

    }
    
    public function scopehaFichado($query, $dia, $profesor)
    {
        return $query->where('dia', '=', $dia)
                        ->where('idProfesor', $profesor);
    }

    public static function fichar($profesor = null)
    {

        $ultimo = Falta_profesor::Hoy($profesor ?? AuthUser()->dni)
                ->get()->last();

        if ($ultimo != null){
            $now = new Date();
            if ($ultimo->salida != null)
                $last = new Date($ultimo->salida);
            else
                $last = new Date($ultimo->entrada);
            $diff = $now->diffInMinutes($last);
            if ($diff < 10) return null;
       }

        if (($ultimo == null) || ($ultimo->salida != null)) {
            $ultimo = new Falta_profesor;
            $ultimo->idProfesor = $profesor ?? AuthUser()->dni;
            $ultimo->dia = date("Y-m-d", time());
            $ultimo->entrada = date("H:i:s", time());
            $ultimo->save();
            return $ultimo;
        } else{
            $ultimo->salida = date("H:i:s", time());
            $ultimo->save();
            return $ultimo;
        }

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
