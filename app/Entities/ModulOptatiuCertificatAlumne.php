<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Intranet\Entities\Concerns\BatoiModels;

/**
 * Estat d'emissió del certificat de mòdul optatiu per alumne.
 *
 * @property int $id
 * @property int $idCertificat
 * @property string $idAlumno
 * @property string|null $enviat_at
 * @property string|null $registrat_at
 * @property string|null $fitxer
 */
class ModulOptatiuCertificatAlumne extends Model
{
    use BatoiModels;

    protected $table = 'modul_optatiu_certificat_alumnes';

    protected $fillable = [
        'idCertificat',
        'idAlumno',
        'enviat_at',
        'registrat_at',
        'fitxer',
    ];

    protected $rules = [
        'idCertificat' => 'required|integer',
        'idAlumno' => 'required|string|max:8',
    ];

    /**
     * Certificat al qual pertany l'estat.
     */
    public function Certificat(): BelongsTo
    {
        return $this->belongsTo(ModulOptatiuCertificat::class, 'idCertificat', 'id');
    }

    /**
     * Alumne associat a l'estat.
     */
    public function Alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
}
