<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Intranet\Entities\Concerns\BatoiModels;

/**
 * Metadades comunes d'un certificat de mòdul optatiu per a un mòdul-grup.
 *
 * Les notes de l'alumnat viuen en `alumno_resultados`.
 *
 * @property int $id
 * @property int $idModuloGrupo
 * @property string $denominacio
 * @property string $idProfesor
 */
class ModulOptatiuCertificat extends Model
{
    use BatoiModels;

    protected $table = 'modul_optatiu_certificats';

    protected $fillable = [
        'idModuloGrupo',
        'denominacio',
        'idProfesor',
    ];

    protected $rules = [
        'idModuloGrupo' => 'required|integer',
        'denominacio' => 'required|string|max:200',
        'idProfesor' => 'required|string|max:10',
    ];

    /**
     * Mòdul-grup al qual pertany el certificat.
     */
    public function ModuloGrupo(): BelongsTo
    {
        return $this->belongsTo(Modulo_grupo::class, 'idModuloGrupo', 'id');
    }

    /**
     * Professor responsable del certificat.
     */
    public function Profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    /**
     * Estat d'emissió per alumne.
     */
    public function AlumnosCertificado(): HasMany
    {
        return $this->hasMany(ModulOptatiuCertificatAlumne::class, 'idCertificat', 'id');
    }
}
