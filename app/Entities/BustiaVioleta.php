<?php
namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
 

class BustiaVioleta extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

    protected $table = 'bustia_violeta';

    protected $fillable = [
        'dni','rol','anonimo','autor_nombre','categoria','mensaje',
        'estado','finalitat','dni_hash', 'tipus'
    ];

    protected $casts = [
        'anonimo' => 'bool',
    ];

    // Nom a mostrar
    public function getAutorDisplayNameAttribute()
    {
        return $this->anonimo ? 'Anònim' : ($this->autor_nombre ?: '—');
    }

    // Scopes útils
    public function scopePendents($q){ return $q->where('estado','nou'); }
    public function scopeAmbCategoria($q,$c){ return $q->where('categoria',$c); }
    public function scopeDeTipus($q, string $tipus) { return $q->where('tipus', $tipus); }

}
