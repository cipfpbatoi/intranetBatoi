<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

/** Mòduls FOL LOGSE identificats pel codi oficial. */
class ModulFolLogse extends Model
{
    protected $table = 'convalidacions_moduls_fol_logse';

    protected $primaryKey = 'codigo';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['codigo', 'modul', 'cicle', 'nivell', 'sistema'];

    public $timestamps = false;
}
