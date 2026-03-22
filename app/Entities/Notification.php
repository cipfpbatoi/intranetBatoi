<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Model de notificacions d'usuari.
 */
class Notification extends Model
{

    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $visible = [
        'id',
        'data',
        'read_at',
        'created_at',
    ];
    protected $fillable = [
        'id',
        'read_at',
    ];

    /**
     * Obté el motiu de la notificació (retallat).
     *
     * @return string
     */
    public function getMotivoAttribute()
    {
        $json = $this->decodedData();
        return substr((string) ($json['motiu'] ?? ''), 0, 80);
    }

    /**
     * Obté el nom de l'emissor.
     *
     * @return string
     */
    public function getEmisorAttribute()
    {
        $json = $this->decodedData();
        $emissor = $json['emissor'] ?? '';
        if (is_array($emissor)) {
            $emissor = implode(', ', array_map('strval', $emissor));
        }

        return (string) $emissor;
    }

    /**
     * Obté la data en format Y-m-d.
     *
     * @return string
     */
    public function getFechaAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : '';
    }

    /**
     * Indica si la notificació està llegida.
     *
     * @return int
     */
    public function getLeidoAttribute()
    {
        return is_null($this->read_at)?0:1;
    }

    /**
     * Decodifica el camp data de la notificació.
     *
     * @return array<string, mixed>
     */
    private function decodedData(): array
    {
        $decoded = json_decode((string) $this->data, true);
        return is_array($decoded) ? $decoded : [];
    }
}
