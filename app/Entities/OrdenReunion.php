<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Model d'ordres de reunió.
 */
class OrdenReunion extends Model
{
    public const CODE_PREVIOUS_MINUTES_READING = 'previous_minutes_reading';
    public const CODE_OPEN_FLOOR = 'open_floor';
    public const CODE_DIRECTION_REPORT = 'direction_report';
    public const CODE_HEAD_STUDIES_REPORT = 'head_studies_report';
    public const CODE_PREVIOUS_AGREEMENTS_REVIEW = 'previous_agreements_review';
    public const CODE_STUDENT_OPINION = 'student_opinion';
    public const CODE_GROUP_PROBLEMS = 'group_problems';
    public const CODE_NESE_FOLLOW_UP = 'nese_follow_up';
    public const CODE_AGREEMENTS = 'agreements';
    public const CODE_OBSERVATIONS = 'observations';
    public const CODE_STUDENT_COUNT = 'student_count';
    public const CODE_VOTER_COUNT = 'voter_count';
    public const CODE_CANDIDATES = 'candidates';
    public const CODE_VOTES = 'votes';
    public const CODE_DELEGATE = 'delegate';
    public const CODE_DEPUTY_DELEGATE = 'deputy_delegate';
    public const CODE_SECRETARY = 'secretary';
    public const CODE_MEMBER = 'member';
    public const CODE_GRADES_REVIEW = 'grades_review';
    public const CODE_RESULTS_ASSESSMENT = 'results_assessment';
    public const CODE_PROJECT_PROPOSAL_STUDENT = 'project_proposal_student';
    public const CODE_PROJECT_DEFENSE_STUDENT = 'project_defense_student';

    /**
     * @var string
     */
    protected $table = 'ordenes_reuniones';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'descripcion',
        'resumen',
        'idReunion',
        'orden'
    ];

    /**
     * @var array<string, string>
     */
    protected $rules = [
        'orden' => 'required|integer|between:1,127',
        'descripcion' => 'required|max:120',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Reunion()
    {
        return $this->belongsTo(Reunion::class, 'idReunion', 'id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string $idReunion
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForReunion($query, $idReunion)
    {
        return $query->where('idReunion', $idReunion);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $orden
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderNumber($query, int $orden)
    {
        return $query->where('orden', $orden);
    }

    /**
     * @param int|string $idReunion
     * @param int $orden
     * @return self|null
     */
    public static function firstByReunionAndOrder($idReunion, int $orden): ?self
    {
        return static::query()
            ->forReunion($idReunion)
            ->orderNumber($orden)
            ->first();
    }

    /**
     * @param int|string $idReunion
     * @param int $orden
     * @return string
     */
    public static function resumenByReunionAndOrder($idReunion, int $orden): string
    {
        return (string) (static::firstByReunionAndOrder($idReunion, $orden)?->resumen ?? '');
    }

    /**
     * Inferix un codi només per a descripcions funcionals conegudes exactes.
     */
    public static function codeForDescription(string $description): ?string
    {
        return [
            'Lectura acta anterior' => self::CODE_PREVIOUS_MINUTES_READING,
            'Torn obert de paraula' => self::CODE_OPEN_FLOOR,
            'Informe direcció' => self::CODE_DIRECTION_REPORT,
            'Informe Caporalia' => self::CODE_HEAD_STUDIES_REPORT,
            "Revisió d'acords adoptats a la sessió anterior" => self::CODE_PREVIOUS_AGREEMENTS_REVIEW,
            'Opinió i/o comentaris dels alumnes' => self::CODE_STUDENT_OPINION,
            'Opinió dels alumnes' => self::CODE_STUDENT_OPINION,
            'Problemes detectats al grup i mesures a prendre' => self::CODE_GROUP_PROBLEMS,
            'Problemes detectats al grup i mesures a pendre' => self::CODE_GROUP_PROBLEMS,
            'Alumnes amb dificultats acadèmiques i mesures a adoptar' => self::CODE_NESE_FOLLOW_UP,
            'Acords adoptats' => self::CODE_AGREEMENTS,
            'Observacions' => self::CODE_OBSERVATIONS,
            'Nº Alumnes' => self::CODE_STUDENT_COUNT,
            'Nº Votants' => self::CODE_VOTER_COUNT,
            'Candidats' => self::CODE_CANDIDATES,
            'Vots' => self::CODE_VOTES,
            'Delegat' => self::CODE_DELEGATE,
            'Subdelegat' => self::CODE_DEPUTY_DELEGATE,
            'Secretari' => self::CODE_SECRETARY,
            'Vocal' => self::CODE_MEMBER,
            "Revisió de l'acta de qualificacions" => self::CODE_GRADES_REVIEW,
            'Valoració general dels resultats obtinguts' => self::CODE_RESULTS_ASSESSMENT,
        ][$description] ?? null;
    }
}
