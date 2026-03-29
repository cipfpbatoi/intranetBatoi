<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Registre específic de seguiment/contacte de domini.
 *
 * Manté convivència temporal amb `activities` mentre es migra la lectura
 * dels panells i fluxos legacy.
 *
 * @property int $id
 * @property string $domain_type
 * @property string $domain_id
 * @property string $contact_type
 * @property string $title
 * @property string|null $comment
 * @property string|null $author_id
 * @property string $contacted_at
 * @property array<string, mixed>|null $meta
 */
class Seguimiento extends Model
{
    protected $table = 'seguimientos';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'domain_type',
        'domain_id',
        'contact_type',
        'title',
        'comment',
        'author_id',
        'contacted_at',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'meta' => 'array',
        'contacted_at' => 'datetime',
    ];

    /**
     * Filtra per agregat de domini.
     */
    public function scopeDomain($query, string $domainType, string|int $domainId)
    {
        return $query->where('domain_type', $domainType)
            ->where('domain_id', (string) $domainId);
    }

    /**
     * Autor del seguiment, si existix.
     */
    public function Author()
    {
        return $this->belongsTo(Profesor::class, 'author_id', 'dni');
    }
}
