<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendariEscolar extends Model
{
    use HasFactory;

    protected $table = 'calendari_escolar';

    protected $fillable = ['data', 'tipus', 'esdeveniment'];
}
