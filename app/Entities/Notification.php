<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $datatable = "notifications";
    protected $primaryKey = 'id';
    protected $keyType = 'string';
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
        $json = json_decode($this->data, true);
        return substr($json['motiu'], 0, 80);
    }
    public function getEmisorAttribute()
    {
        $json = json_decode($this->data, true);
        return $json['emissor'];
    }
    public function getFechaAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }
    public function getLeidoAttribute()
    {
        return is_null($this->read_at)?0:1;
    }
}
