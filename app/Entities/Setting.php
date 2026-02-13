<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;
    public $timestamps = false;

    protected $fillable = [
        'collection',
        'key',
        'value',
    ];
    protected $inputTypes = [
        'value' => ['type' => 'textarea'],
    ];
    protected $rules = [
        'collection' => 'required',
        'key' => 'required',
        'value' => 'required',
    ];
}
