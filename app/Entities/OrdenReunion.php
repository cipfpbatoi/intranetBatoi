<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Model d'ordres de reunió.
 */
class OrdenReunion extends Model
{

    /**
     * @var string
     */
    protected $table = 'ordenes_reuniones';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'descripcion',
        'resumen',
        'idReunion',
        'orden'
    ];

    /**
     * @var array<string, string>
     */
    protected $rules = [
        'orden' => 'required|integer|between:1,127',
        'descripcion' => 'required|max:120',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Reunion()
    {
        return $this->belongsTo(Reunion::class, 'idReunion', 'id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string $idReunion
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForReunion($query, $idReunion)
    {
        return $query->where('idReunion', $idReunion);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $orden
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderNumber($query, int $orden)
    {
        return $query->where('orden', $orden);
    }

    /**
     * @param int|string $idReunion
     * @param int $orden
     * @return self|null
     */
    public static function firstByReunionAndOrder($idReunion, int $orden): ?self
    {
        return static::query()
            ->forReunion($idReunion)
            ->orderNumber($orden)
            ->first();
    }

    /**
     * @param int|string $idReunion
     * @param int $orden
     * @return string
     */
    public static function resumenByReunionAndOrder($idReunion, int $orden): string
    {
        return (string) (static::firstByReunionAndOrder($idReunion, $orden)?->resumen ?? '');
    }
}
