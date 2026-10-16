<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

/** Mòduls FOL LOE identificats pel codi oficial i editables per Direcció. */
class ModulFolLoe extends Model
{
    protected $table = 'convalidacions_moduls_fol_loe';

    /** @var list<string> Camps editables del catàleg. */
    protected $fillable = ['codigo', 'codigo_normalitzat', 'cicle_cas', 'cicle_val', 'nivell'];

    /** Normalitza un codi decimal sense convertir-lo a enter ni perdre precisió. */
    public static function normalitzarCodi(string $codi): string
    {
        $normalitzat = ltrim(trim($codi), '0');

        return $normalitzat === '' ? '0' : $normalitzat;
    }
}
