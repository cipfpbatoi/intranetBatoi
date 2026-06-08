<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Services\School\SignaturaStatusService;

/**
 * Model de documents d'annexos pendents de signatura.
 */
class Signatura extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    public $mail = null;
    public $contact = null;

    protected $table = 'signatures';
    protected $fillable = [
        'tipus',
        'idProfesor',
        'idSao',
        'sendTo',
        'signed'
    ];
    protected $casts = [
        'sendTo' => 'integer',
        'signed' => 'integer',
    ];

    public function Fct()
    {
        return $this->belongsTo(AlumnoFct::class, 'idSao', 'idSao');
    }

    public function Teacher()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function Alumno(){
        return $this->hasOneThrough(Alumno::class,AlumnoFct::class,'idSao','nia','idSao','idAlumno');
    }

    public function deleteFile()
    {
        if (file_exists($this->routeFile)) {
            unlink($this->routeFile);
        }
    }

    public static function saveIfNotExists($anexe, $idSao , $signat = 0)
    {
        return static::query()->updateOrCreate(
            [
                'tipus' => $anexe,
                'idSao' => $idSao,
            ],
            [
                'idProfesor' => authUser()->dni,
                'sendTo' => false,
                'signed' => $signat,
            ]
        );
    }

    /**
     * Normalitza el tipus d'annex segons la modalitat real de l'FCT.
     *
     * Les pujades manuals només exposen `A1/A2/A3/A5`, però els fluxos SAO
     * guarden `...DUAL` quan correspon. Si no resolem ací el tipus real,
     * direcció acaba firmant amb coordenades del model incorrecte.
     */
    public static function normalizeTipusForAlumnoFct(string $tipus, ?AlumnoFct $alumnoFct): string
    {
        $normalized = strtoupper(trim($tipus));
        if ($normalized === '' || str_ends_with($normalized, 'DUAL') || !$alumnoFct || !$alumnoFct->Fct) {
            return $normalized;
        }

        if ($normalized === 'A1') {
            return $alumnoFct->Fct->dual ? 'A1DUAL' : 'A1';
        }

        if (in_array($normalized, ['A2', 'A3', 'A5'], true)) {
            return (int) ($alumnoFct->Fct->asociacion ?? 0) >= 3
                ? $normalized . 'DUAL'
                : $normalized;
        }

        return $normalized;
    }

    public function getProfesorAttribute()
    {
        return $this->Teacher->shortName ?? '';
    }

    public function getAlumneAttribute()
    {
        return $this->Fct->Alumno->shortName ?? '';
    }

    public function getCentreAttribute()
    {
        return $this->Fct->Fct->Colaboracion->Centro->nombre ?? '';
    }
    public function getPathAttribute()
    {
        return storage_path('app/annexes/');
    }

    public function getFileNameAttribute()
    {
        return "{$this->tipus}_{$this->idSao}.pdf";
    }

    public function getRouteFileAttribute()
    {
        return $this->path.$this->fileName;
    }
     public function getSimpleRouteFileAttribute()
     {
         return 'app/annexes/'."{$this->tipus}_{$this->idSao}.pdf";
     }

    public function getEmailAttribute()
    {
        return $this->Fct->Fct->Instructor->email ?? '';
    }
    public function getContactoAttribute()
    {
        return $this->Fct->Fct->Instructor->contacto ?? '';
    }

    public function getSignAttribute()
    {
        return $this->statusService()->yesNo((int) $this->signed > 0);
    }

    public function getSendAttribute()
    {
        return $this->statusService()->yesNo((int) $this->sendTo > 0);
    }

    public function getEstatAttribute()
    {
        return $this->statusService()->estat($this);
    }

    public function getClassAttribute()
    {
        return $this->statusService()->cssClass($this);
    }

    public function getFctOptions()
    {
        $user = AuthUser();
        if (!$user) {
            return [];
        }

        return AlumnoFct::misFcts($user->dni)
            ->get()
            ->filter(fn ($fct) => $fct->Alumno !== null)
            ->mapWithKeys(fn ($fct) => [$fct->idSao => $fct->Alumno->fullName])
            ->all();
    }

    public function getTipusOptions()
    {
        return [
            'A1' => 'A1',
            'A2' => 'A2',
            'A3' => 'A3',
            'A5' => 'A5',
        ];
    }

    private function statusService(): SignaturaStatusService
    {
        return app(SignaturaStatusService::class);
    }
}
