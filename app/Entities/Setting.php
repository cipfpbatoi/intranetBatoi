<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;


class Setting extends Model
{
    protected $fillable = ['key', 'value'];
    protected $rules = [
        'key' => 'required',
        'value' => 'required',
    ];
    public $timestamps = false;
    protected $inputTypes = [
        'value' => ['type' => 'textarea'],
    ];
}
