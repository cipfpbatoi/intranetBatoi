<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class OrdenReunion extends Model
{

    protected $table = 'ordenes_reuniones';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
        'resumen',
        'idReunion',
        'orden'
    ];
    protected $rules = [
        'orden' => 'required|integer',
        'descripcion' => 'required|max:120',
    ];

    public function Reunion()
    {
        return $this->belongsTo(Reunion::class, 'idReunion', 'id');
    }

    public function scopeForReunion($query, $idReunion)
    {
        return $query->where('idReunion', $idReunion);
    }

    public function scopeOrderNumber($query, int $orden)
    {
        return $query->where('orden', $orden);
    }

    public static function firstByReunionAndOrder($idReunion, int $orden): ?self
    {
        return static::query()
            ->forReunion($idReunion)
            ->orderNumber($orden)
            ->first();
    }

    public static function resumenByReunionAndOrder($idReunion, int $orden): string
    {
        return (string) (static::firstByReunionAndOrder($idReunion, $orden)?->resumen ?? '');
    }
}
