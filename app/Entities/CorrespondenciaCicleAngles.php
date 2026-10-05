<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

/** Correspondència d'un cicle d'anglés qualificat amb el seu cicle contenidor. */
class CorrespondenciaCicleAngles extends Model
{
    protected $table = 'convalidacions_correspondencies_angles';

    /** @var list<string> Camps editables del catàleg de correspondències. */
    protected $fillable = [
        'codi_cicle_angles',
        'nom_cicle_angles_val',
        'nom_cicle_angles_cas',
        'codi_cicle_contenidor',
        'nom_cicle_contenidor_val',
        'nom_cicle_contenidor_cas',
        'es_grau_superior',
    ];

    protected $casts = [
        'es_grau_superior' => 'boolean',
    ];
}
