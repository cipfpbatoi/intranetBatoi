<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

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

    public function getMotivoAttribute()
    {
        $json = $this->decodedData();
        return substr((string) ($json['motiu'] ?? ''), 0, 80);
    }
    public function getEmisorAttribute()
    {
        $json = $this->decodedData();
        return (string) ($json['emissor'] ?? '');
    }
    public function getFechaAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : '';
    }
    public function getLeidoAttribute()
    {
        return is_null($this->read_at)?0:1;
    }

    private function decodedData(): array
    {
        $decoded = json_decode((string) $this->data, true);
        return is_array($decoded) ? $decoded : [];
    }
}
