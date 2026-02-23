<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Services\HR\FitxatgeService;
use Intranet\Events\FichaCreated;

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
        return $query->where('dia', '=', date("Y-m-d", time()))->where('idProfesor', $profesor);

    }
    
    public function scopehaFichado($query, $dia, $profesor)
    {
        return $query->where('dia', '=', $dia)->where('idProfesor', $profesor);
    }

    /**
     * @deprecated Usa Intranet\Services\HR\FitxatgeService::fitxar().
     */
    public static function fichar($profesor = null)
    {
        return app(FitxatgeService::class)->fitxar($profesor ?? authUser()->dni);
    }
    
    /**
     * @deprecated Usa Intranet\Services\HR\FitxatgeService::fitxaDiaManual().
     */
    public static function fichaDia($profesor,$dia)
    {
        app(FitxatgeService::class)->fitxaDiaManual((string) $profesor, (string) $dia);
    }
    

}
