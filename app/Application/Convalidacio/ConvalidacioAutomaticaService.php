<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use Illuminate\Support\Facades\DB;
use Intranet\Entities\Convalidacio;
use Intranet\Entities\Departamento;
use Intranet\Entities\Profesor;

/** Avalua i aplica les regles del catàleg sobre sol·licituds ja presentades. */
class ConvalidacioAutomaticaService
{
    public function __construct(
        private readonly ConvalidacioReglesManager $regles,
        private readonly ConvalidacioAnglesCorrespondenciesService $correspondenciesAngles
    ) {
    }

    /** Retorna les regles i les peticions presentades que coincidixen amb elles. */
    public function previsualitzar(): array
    {
        $catalog = $this->regles->actual();
        $regles = $catalog['regles'];
        $resum = [];
        $preparades = [];

        foreach ($regles as $index => $regla) {
            $motiu = $this->motiuNoAplicable($regla);
            $resum[$index] = [
                'regla' => $regla,
                'habilitada' => ($regla['enabled'] ?? true) === true,
                'casos' => 0,
                'motiu' => $motiu,
                'correspondencies_angles' => [],
            ];
            if ($motiu === null) {
                $preparades[$regla['id']] = $index;
            }
        }

        $peticions = Convalidacio::query()
            ->with('sollicitud')
            ->whereNull('regla_automatica_id')
            ->whereNotIn('estat', [Convalidacio::ESTAT_REALITZADA, Convalidacio::ESTAT_RESOLTA, Convalidacio::ESTAT_DENEGADA])
            ->whereHas('sollicitud', static fn ($query) => $query->whereNotNull('submitted_at'))
            ->get();

        $perSollicitud = [];
        foreach ($peticions as $peticio) {
            $sollicitudId = (int) $peticio->sollicitud_convalidacio_id;
            $perSollicitud[$sollicitudId] ??= ['pendents' => 0, 'elegibles' => 0];
            $perSollicitud[$sollicitudId]['pendents']++;
        }

        if ($preparades === []) {
            return [
                'catalog' => $catalog,
                'regles' => array_values($resum),
                'casos' => [],
                'bloquejats' => [],
                'per_sollicitud' => $perSollicitud,
            ];
        }

        $coincidencies = [];
        $bloquejats = [];
        foreach ($peticions as $peticio) {
            foreach ($regles as $index => $regla) {
                if (!isset($preparades[$regla['id']])) {
                    continue;
                }

                $resultatRegla = $this->resultatRegla($regla, $peticio);
                if ($resultatRegla['motiu'] !== null) {
                    if ($resultatRegla['motiu'] !== 'sense_coincidencia') {
                        $bloquejats[$regla['id']] = $resultatRegla['motiu'];
                    }
                    continue;
                }

                $coincidencies[$peticio->id][] = [
                    'peticio_id' => (int) $peticio->id,
                    'nia' => (string) $peticio->sollicitud->alumno_id,
                    'peticio' => $peticio,
                    'resultat_origen' => $resultatRegla['resultat_origen'],
                    'correspondencia_angles' => $resultatRegla['correspondencia_angles'] ?? null,
                    'regla' => $regla,
                    'index_regla' => $index,
                ];
            }
        }

        $casos = [];
        foreach ($coincidencies as $coincidents) {
            $unics = collect($coincidents)->unique(fn (array $item): string => $item['regla']['id'])->values();
            $resultatsEquivalents = $unics->map(fn (array $item): string => json_encode([
                $item['regla']['result'] ?? null,
                $item['regla']['legal_basis'] ?? null,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->unique()->count() === 1;
            if ($unics->count() > 1 && !$resultatsEquivalents) {
                foreach ($unics as $item) {
                    $bloquejats[$item['regla']['id']] = 'Hi ha més d’una regla amb resultats o bases normatives diferents per a la mateixa petició.';
                }
                continue;
            }

            $cas = $unics->first();
            $cas['regles_coincidents'] = $unics->map(fn (array $item): array => $item['regla'])->all();
            $casos[] = $cas;
            $sollicitudId = (int) $cas['peticio']->sollicitud_convalidacio_id;
            $perSollicitud[$sollicitudId]['elegibles']++;
            foreach ($unics as $coincident) {
                $resum[$coincident['index_regla']]['casos']++;
                if ($coincident['correspondencia_angles'] !== null) {
                    $correspondencia = $coincident['correspondencia_angles'];
                    $clauCorrespondencia = $correspondencia['codi_cicle_angles'] . '|' . $correspondencia['codi_cicle_contenidor'];
                    $resum[$coincident['index_regla']]['correspondencies_angles'][$clauCorrespondencia] = $correspondencia;
                }
            }
        }

        foreach ($resum as $index => $item) {
            if ($item['motiu'] === null && $item['casos'] === 0 && isset($bloquejats[$item['regla']['id']])) {
                $resum[$index]['motiu'] = $bloquejats[$item['regla']['id']];
            } elseif ($item['motiu'] === null && $item['casos'] === 0) {
                $resum[$index]['motiu'] = 'No s’ha trobat cap petició presentada pendent que complisca esta regla.';
            }
        }

        return [
            'catalog' => $catalog,
            'regles' => array_values($resum),
            'casos' => $casos,
            'bloquejats' => $bloquejats,
            'per_sollicitud' => $perSollicitud,
        ];
    }

    /** Resol les peticions ja presentades i conserva la seua traçabilitat original. */
    public function aplicar(Profesor $responsable): array
    {
        $preview = $this->previsualitzar();
        $resultat = ['aplicats' => 0, 'ja_existien' => 0, 'errors' => []];

        foreach ($preview['casos'] as $cas) {
            try {
                $estat = DB::transaction(function () use ($cas, $preview, $responsable): string {
                    $peticio = Convalidacio::query()
                        ->with('sollicitud')
                        ->lockForUpdate()
                        ->findOrFail($cas['peticio_id']);
                    if (
                        $peticio->regla_automatica_id !== null
                        || $peticio->esTerminal()
                        || !$peticio->sollicitud?->submitted_at
                    ) {
                        return 'ja_existent';
                    }

                    $regla = $cas['regla'];
                    $instantaniaRegla = [
                        'catalog_version' => $preview['catalog']['version'],
                        'catalog_sha256' => $preview['catalog']['sha256'],
                        'catalog_metadata' => $preview['catalog']['metadata'],
                        'rule' => $regla,
                        'equivalent_matching_rules' => $cas['regles_coincidents'],
                    ];
                    $evidencia = [
                        'sollicitud_id' => $peticio->sollicitud_convalidacio_id,
                        'peticio_id' => $peticio->id,
                        'origen' => $peticio->origen,
                        'resultat_origen' => [
                            'modul' => $peticio->modulo_origen_codigo,
                            'coincidencia_codi_origen' => $cas['resultat_origen']['coincidencia_codi_origen'] ?? null,
                            'nom_modul_val' => $peticio->modulo_origen_nombre_val,
                            'nom_modul_cas' => $peticio->modulo_origen_nombre_cas,
                            'cicle' => $peticio->ciclo_origen_codigo,
                            'nom_cicle_val' => $peticio->ciclo_origen_nombre_val,
                            'nom_cicle_cas' => $peticio->ciclo_origen_nombre_cas,
                            'familia_professional' => $peticio->familia_professional_codigo,
                            'nivell_formatiu_origen' => $peticio->nivel_origen_codigo,
                            'any' => $peticio->any_origen,
                            'nota' => $peticio->nota_origen,
                            'convocatoria' => $peticio->convocatoria_origen,
                        ],
                        'correspondencia_angles' => $cas['correspondencia_angles'] ?? null,
                        'matricula_destinacio' => [
                            'modulo_destino_id' => $peticio->modulo_destino_id,
                            'ciclo_matricula_id' => $peticio->ciclo_matricula_id,
                            'ciclo_matricula_codigo' => $peticio->ciclo_matricula_codigo,
                            'ciclo_matricula_nombre_val' => $peticio->ciclo_matricula_nombre_val,
                            'ciclo_matricula_nombre_cas' => $peticio->ciclo_matricula_nombre_cas,
                            'ciclo_matricula_tipo' => $peticio->ciclo_matricula_tipo,
                            'ciclo_matricula_normativa' => $peticio->ciclo_matricula_normativa,
                            'familia_matricula_codigo_xml' => $peticio->familia_matricula_codigo_xml,
                            'familia_matricula_nombre_val' => $peticio->familia_matricula_nombre_val,
                            'familia_matricula_nombre_cas' => $peticio->familia_matricula_nombre_cas,
                        ],
                    ];
                    $nota = ($regla['result']['grade']['mode'] ?? null) === 'preserve'
                        ? $peticio->nota_origen
                        : null;

                    $peticio->forceFill([
                        'estat' => Convalidacio::ESTAT_RESOLTA,
                        'observacions' => 'Convalidació resolta automàticament segons la regla ' . $regla['id'] . '.',
                        'revisat_per' => $responsable->dni,
                        'revisat_at' => now(),
                        'regla_automatica_id' => $regla['id'],
                        'regla_automatica_version' => $preview['catalog']['version'],
                        'regla_automatica_hash' => $preview['catalog']['sha256'],
                        'regla_automatica_snapshot' => $instantaniaRegla,
                        'evidencia_automatica_snapshot' => $evidencia,
                        'base_normativa_automatica' => $regla['legal_basis'],
                        'resultat_automatic' => $regla['result']['status'],
                        'mode_nota_automatic' => $regla['result']['grade']['mode'],
                        'nota_resultat_automatic' => $nota,
                    ])->save();

                    return 'aplicat';
                }, 3);

                $resultat[$estat === 'aplicat' ? 'aplicats' : 'ja_existien']++;
            } catch (\Throwable $exception) {
                report($exception);
                $resultat['errors'][] = [
                    'nia' => $cas['nia'],
                    'modul' => $cas['peticio']->modulo_destino_id,
                    'missatge' => 'No s’ha pogut aplicar la regla ' . $cas['regla']['id'] . '.',
                ];
            }
        }

        return $resultat;
    }

    /** Comprova una regla amb les dades que l'alumne ja va guardar en presentar la petició. */
    private function resultatRegla(array $regla, Convalidacio $peticio): array
    {
        if ((string) ($regla['target']['code'] ?? '') !== (string) $peticio->modulo_destino_id) {
            return ['resultat_origen' => null, 'motiu' => 'sense_coincidencia'];
        }
        if (!$this->nivellDestinacioCoincideix((string) ($regla['target']['level'] ?? ''), (int) $peticio->ciclo_matricula_tipo)) {
            return ['resultat_origen' => null, 'motiu' => 'sense_coincidencia'];
        }

        $source = $regla['source'];
        if (($source['type'] ?? null) !== 'module') {
            return ['resultat_origen' => null, 'motiu' => 'L’origen requerix documentació que no es pot verificar automàticament.'];
        }
        if ($peticio->origen !== Convalidacio::ORIGEN_PROPI_CENTRE || blank($peticio->modulo_origen_codigo)) {
            return ['resultat_origen' => null, 'motiu' => 'La petició no conté una identificació verificable del mòdul d’origen.'];
        }

        $condicions = $source['conditions'] ?? [];
        if (array_diff(array_keys($condicions), ['same_professional_family', 'minimum_weekly_hours']) !== []) {
            return ['resultat_origen' => null, 'motiu' => 'La regla inclou una condició encara no implementada.'];
        }

        $coincidenciaCodiOrigen = isset($source['code'])
            ? $this->coincidenciaCodiOrigen((string) $source['code'], $peticio)
            : null;
        $coincideix = isset($source['code'])
            ? $coincidenciaCodiOrigen !== null
            : $this->nomCoincideixExacte((string) ($source['name'] ?? ''), $peticio);
        if (!$coincideix) {
            return ['resultat_origen' => null, 'motiu' => 'sense_coincidencia'];
        }

        $correspondenciaAngles = null;
        if (array_key_exists('minimum_weekly_hours', $condicions)) {
            $horesMinimes = filter_var($condicions['minimum_weekly_hours'], FILTER_VALIDATE_INT);
            if ($horesMinimes === false || $horesMinimes < 0 || $horesMinimes > 5) {
                return ['resultat_origen' => null, 'motiu' => 'La correspondència només acredita un mínim de 5 hores setmanals.'];
            }

            $correspondencia = $this->correspondenciesAngles->trobarPerCicleAngles((string) $peticio->ciclo_origen_codigo);
            if ($correspondencia === null) {
                return ['resultat_origen' => null, 'motiu' => 'No hi ha una correspondència validada per al cicle d’anglés d’origen.'];
            }

            $correspondenciaAngles = [
                'codi_cicle_angles' => $correspondencia->codi_cicle_angles,
                'nom_cicle_angles_val' => $correspondencia->nom_cicle_angles_val,
                'nom_cicle_angles_cas' => $correspondencia->nom_cicle_angles_cas,
                'codi_cicle_contenidor' => $correspondencia->codi_cicle_contenidor,
                'nom_cicle_contenidor_val' => $correspondencia->nom_cicle_contenidor_val,
                'nom_cicle_contenidor_cas' => $correspondencia->nom_cicle_contenidor_cas,
                'es_grau_superior' => (bool) $correspondencia->es_grau_superior,
            ];
        }

        if (($condicions['same_professional_family'] ?? false) === true) {
            $familiaOrigen = (string) ($peticio->familia_professional_codigo ?? '');
            $familiaDestinacio = (string) ($peticio->familia_matricula_codigo_xml ?? '');
            if ($familiaOrigen === '' || $familiaDestinacio === '' || $familiaOrigen !== $familiaDestinacio) {
                return ['resultat_origen' => null, 'motiu' => 'sense_coincidencia'];
            }
        }

        return [
            'resultat_origen' => [
                'modul' => $peticio->modulo_origen_codigo,
                'coincidencia_codi_origen' => $coincidenciaCodiOrigen,
                'nom_modul_val' => $peticio->modulo_origen_nombre_val,
                'nom_modul_cas' => $peticio->modulo_origen_nombre_cas,
                'cicle' => $peticio->ciclo_origen_codigo,
                'nom_cicle_val' => $peticio->ciclo_origen_nombre_val,
                'nom_cicle_cas' => $peticio->ciclo_origen_nombre_cas,
                'familia_professional' => $peticio->familia_professional_codigo,
                'nivell_formatiu_origen' => $peticio->nivel_origen_codigo,
                'any' => $peticio->any_origen,
                'nota' => $peticio->nota_origen,
            ],
            'correspondencia_angles' => $correspondenciaAngles,
            'motiu' => null,
        ];
    }

    /**
     * Comprova el codi literal o la forma base + abreviatura XML de la família d'origen.
     *
     * @return array{tipus:string,codi_regla:string,codi_origen:string,abreviatura_familia:?string}|null
     */
    private function coincidenciaCodiOrigen(string $codiRegla, Convalidacio $peticio): ?array
    {
        $codiOrigen = (string) $peticio->modulo_origen_codigo;
        if ($codiOrigen === $codiRegla) {
            return [
                'tipus' => 'exacta',
                'codi_regla' => $codiRegla,
                'codi_origen' => $codiOrigen,
                'abreviatura_familia' => null,
            ];
        }

        $codigoFamiliaOrigen = (string) ($peticio->familia_professional_codigo ?? '');
        if ($codigoFamiliaOrigen === '') {
            return null;
        }

        $abreviaturaFamilia = Departamento::query()
            ->where('codigo_xml', $codigoFamiliaOrigen)
            ->value('abreviatura_xml');
        if (!is_string($abreviaturaFamilia) || $abreviaturaFamilia === ''
            || $codiOrigen !== $codiRegla . $abreviaturaFamilia) {
            return null;
        }

        return [
            'tipus' => 'codi_base_i_abreviatura_familia',
            'codi_regla' => $codiRegla,
            'codi_origen' => $codiOrigen,
            'abreviatura_familia' => $abreviaturaFamilia,
        ];
    }

    /** Retorna el motiu estructural pel qual una regla no es pot aplicar. */
    private function motiuNoAplicable(array $regla): ?string
    {
        if (($regla['enabled'] ?? true) !== true) {
            return 'Regla deshabilitada al YAML.';
        }
        if (($regla['proposal']['action'] ?? null) !== 'convalidate') {
            return 'La regla no proposa una convalidació.';
        }
        if (($regla['resolution']['authority'] ?? null) !== 'centre') {
            return 'La regla no és competència del centre.';
        }
        if (!is_array($regla['result'] ?? null) || !in_array($regla['result']['status'] ?? null, ['AA', 'CO'], true)) {
            return 'La regla no definix un resultat AA o CO.';
        }
        if (!in_array($regla['result']['grade']['mode'] ?? null, ['preserve', 'none'], true)) {
            return 'La regla no definix com tractar la qualificació.';
        }
        if (!is_array($regla['legal_basis'] ?? null) || $regla['legal_basis'] === []) {
            return 'La regla no indica la base normativa.';
        }
        if (!isset($regla['target']['code'], $regla['source']['type'])) {
            return 'La regla no identifica el mòdul destí o l’origen.';
        }

        return null;
    }

    /** Compara el nivell del destí amb el tipus del cicle guardat en la petició. */
    private function nivellDestinacioCoincideix(string $nivell, int $tipus): bool
    {
        return match ($nivell) {
            'GM' => $tipus === 1,
            'GS' => $tipus === 2,
            'GM_GS' => in_array($tipus, [1, 2], true),
            default => false,
        };
    }

    /** Compara noms de mòdul sense aproximacions. */
    private function nomCoincideixExacte(string $nom, Convalidacio $peticio): bool
    {
        $normalitza = static fn (string $valor): string => mb_strtolower(trim(preg_replace('/\s+/u', ' ', $valor) ?? $valor));
        $nom = $normalitza($nom);

        return $nom !== '' && in_array($nom, [
            $normalitza((string) $peticio->modulo_origen_nombre_val),
            $normalitza((string) $peticio->modulo_origen_nombre_cas),
        ], true);
    }
}
