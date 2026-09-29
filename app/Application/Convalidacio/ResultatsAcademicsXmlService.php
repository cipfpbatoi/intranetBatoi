<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use DOMDocument;
use DOMXPath;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/** Consulta de manera segura els resultats acadèmics guardats en XML privats. */
class ResultatsAcademicsXmlService
{
    /**
     * Retorna cada resultat aprovat com una opció independent.
     *
     * @return list<array{id:string, modul:string, nom_modul:string, nom_modul_val:string, nom_modul_cas:string, cicle:string, nom_cicle:string, nom_cicle_val:string, nom_cicle_cas:string, familia_professional:string|null, familia_professional_val:string|null, familia_professional_cas:string|null, nivell_formatiu_origen:string|null, nivell_formatiu_origen_val:string|null, nivell_formatiu_origen_cas:string|null, any:int, nota:float, convocatoria:string}>
     */
    public function aprovats(string $nia): array
    {
        if (!preg_match('/^\d+$/', $nia)) {
            throw new ConvalidacioException('El NIA de l\'alumne no és vàlid.');
        }

        $resultats = [];
        $disk = $this->disk();
        foreach ($this->xmlFiles($disk) as $path) {
            try {
                $contingut = $disk->get($path);
                foreach ($this->parse($contingut, $nia, $path) as $resultat) {
                    unset($resultat['source']);
                    $resultats[] = $resultat;
                }
            } catch (RuntimeException) {
                // Una font corrupta no pot exposar detalls ni bloquejar la resta d'anys.
                continue;
            }
        }

        usort($resultats, static fn (array $a, array $b): int => [
            $a['nom_cicle'], $a['nom_modul'], $a['any'], $a['convocatoria'], $a['id'],
        ] <=> [
            $b['nom_cicle'], $b['nom_modul'], $b['any'], $b['convocatoria'], $b['id'],
        ]);

        return $resultats;
    }

    /**
     * Resol una selecció opaca i la torna a validar contra els XML actuals.
     *
     * @return array{id:string, modul:string, nom_modul:string, nom_modul_val:string, nom_modul_cas:string, cicle:string, nom_cicle:string, nom_cicle_val:string, nom_cicle_cas:string, familia_professional:string|null, familia_professional_val:string|null, familia_professional_cas:string|null, nivell_formatiu_origen:string|null, nivell_formatiu_origen_val:string|null, nivell_formatiu_origen_cas:string|null, any:int, nota:float, convocatoria:string}|null
     */
    public function trobarAprovat(string $nia, string $id): ?array
    {
        foreach ($this->aprovats($nia) as $resultat) {
            if (hash_equals($resultat['id'], $id)) {
                return $resultat;
            }
        }

        return null;
    }

    /**
     * Valida que el contingut siga un XML d'avaluació processable i en retorna l'any.
     *
     * @return int Any acadèmic declarat al node arrel.
     */
    public function validarContingut(string $contingut): int
    {
        try {
            $xpath = $this->xpath($contingut);
        } catch (RuntimeException) {
            throw new ConvalidacioException('El fitxer no és un XML d\'avaluació vàlid.');
        }

        try {
            $any = $this->anyAvaluacio($xpath);
        } catch (RuntimeException) {
            throw new ConvalidacioException('L\'avaluació no conté un curs acadèmic vàlid.');
        }

        if (
            $xpath->query('//curso[@codigo]')->length === 0
            || $xpath->query('//contenido[@curso][@codigo]')->length === 0
            || $xpath->query('//calificacion[@alumno][@curso][@contenido][@evaluacion]')->length === 0
        ) {
            throw new ConvalidacioException('El fitxer no té l\'estructura d\'avaluació esperada.');
        }

        return $any;
    }

    /** @return Filesystem */
    private function disk(): Filesystem
    {
        return Storage::disk('convalidacions_xml');
    }

    /** @return list<string> */
    private function xmlFiles(Filesystem $disk): array
    {
        return collect($disk->files())
            ->filter(static fn (string $path): bool => strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'xml')
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return list<array{id:string, modul:string, nom_modul:string, nom_modul_val:string, nom_modul_cas:string, cicle:string, nom_cicle:string, nom_cicle_val:string, nom_cicle_cas:string, familia_professional:string|null, familia_professional_val:string|null, familia_professional_cas:string|null, nivell_formatiu_origen:string|null, nivell_formatiu_origen_val:string|null, nivell_formatiu_origen_cas:string|null, any:int, nota:float, convocatoria:string, source:string}>
     */
    private function parse(string $contingut, string $nia, string $source): array
    {
        $xpath = $this->xpath($contingut);
        $any = $this->anyAvaluacio($xpath);
        $cursos = $this->indexCursos($xpath);
        $moduls = $this->indexModuls($xpath);
        $perModul = [];

        $nodes = $xpath->query(sprintf(
            "//calificacion[@alumno='%s' and (@evaluacion='FI' or @evaluacion='EX')]",
            $nia
        ));
        foreach ($nodes as $node) {
            $curs = $node->getAttribute('curso');
            $modul = $node->getAttribute('contenido');
            $perModul[$curs . '|' . $modul][$node->getAttribute('evaluacion')] =
                (float) $node->getAttribute('nota_numerica');
        }

        $resultats = [];
        foreach ($perModul as $clau => $notes) {
            [$curs, $modul] = explode('|', $clau, 2);
            if (($notes['FI'] ?? -1) >= 5) {
                $nota = $notes['FI'];
                $convocatoria = 'ordinària (FI)';
            } elseif (($notes['EX'] ?? -1) >= 5) {
                $nota = $notes['EX'];
                $convocatoria = 'extraordinària (EX)';
            } else {
                continue;
            }

            $modulDades = $moduls[$clau] ?? ['nom_val' => '', 'nom_cas' => '', 'curs' => $curs];
            $cursContingut = $modulDades['curs'] ?: $curs;
            $cicle = $this->cicleDelCurs($cursContingut, $cursos);
            $familia = $this->familiaProfessionalDelCurs($cursContingut, $cursos);
            $nivellFormatiu = $this->nivellFormatiuDelCurs($cursContingut, $cursos);
            $identity = implode('|', [$source, (string) $any, $curs, $modul, $convocatoria, (string) $nota]);
            $resultats[] = [
                'id' => hash_hmac('sha256', $identity, (string) config('app.key')),
                'modul' => $modul,
                'nom_modul' => trim($modulDades['nom_val']) ?: trim($modulDades['nom_cas']),
                'nom_modul_val' => trim($modulDades['nom_val']),
                'nom_modul_cas' => trim($modulDades['nom_cas']),
                'cicle' => $cicle['codi'],
                'nom_cicle' => $cicle['nom'],
                'nom_cicle_val' => $cicle['nom_val'],
                'nom_cicle_cas' => $cicle['nom_cas'],
                'familia_professional' => $familia['codi'] ?? null,
                'familia_professional_val' => $familia['nom_val'] ?? null,
                'familia_professional_cas' => $familia['nom_cas'] ?? null,
                'nivell_formatiu_origen' => $nivellFormatiu['codi'] ?? null,
                'nivell_formatiu_origen_val' => $nivellFormatiu['nom_val'] ?? null,
                'nivell_formatiu_origen_cas' => $nivellFormatiu['nom_cas'] ?? null,
                'any' => $any,
                'nota' => $nota,
                'convocatoria' => $convocatoria,
                'source' => $source,
            ];
        }

        return $resultats;
    }

    /** Retorna l'any acadèmic declarat per l'exportació d'ITACA. */
    private function anyAvaluacio(DOMXPath $xpath): int
    {
        $any = trim((string) $xpath->evaluate('string(/centro/@curso)'));
        if (preg_match('/^\d{4}$/', $any) !== 1) {
            throw new RuntimeException('Curs acadèmic invàlid.');
        }

        return (int) $any;
    }

    /** Crea un XPath sense xarxa ni declaracions d'entitats. */
    private function xpath(string $contingut): DOMXPath
    {
        if ($contingut === '' || preg_match('/<!DOCTYPE|<!ENTITY/i', $contingut)) {
            throw new RuntimeException('XML no segur.');
        }

        $xml = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $xml->loadXML($contingut, LIBXML_NONET | LIBXML_NOBLANKS);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        if (!$loaded) {
            throw new RuntimeException('XML invàlid.');
        }

        return new DOMXPath($xml);
    }

    /** @return array<string, array{pare:string, nom_val:string, nom_cas:string}> */
    private function indexCursos(DOMXPath $xpath): array
    {
        $cursos = [];
        foreach ($xpath->query('//curso[@codigo]') as $node) {
            $cursos[$node->getAttribute('codigo')] = [
                'pare' => trim($node->getAttribute('padre')),
                'nom_val' => trim($node->getAttribute('nombre_val')),
                'nom_cas' => trim($node->getAttribute('nombre_cas')),
            ];
        }

        return $cursos;
    }

    /** @return array<string, array{curs:string, nom_val:string, nom_cas:string}> */
    private function indexModuls(DOMXPath $xpath): array
    {
        $moduls = [];
        foreach ($xpath->query('//contenido[@curso][@codigo]') as $node) {
            $clau = $node->getAttribute('curso') . '|' . $node->getAttribute('codigo');
            $moduls[$clau] = [
                'curs' => $node->getAttribute('curso'),
                'nom_val' => trim($node->getAttribute('nombre_val')),
                'nom_cas' => trim($node->getAttribute('nombre_cas')),
            ];
        }

        return $moduls;
    }

    /**
     * @param array<string, array{pare:string, nom_val:string, nom_cas:string}> $cursos
     * @return array{codi:string, nom:string, nom_val:string, nom_cas:string}
     */
    private function cicleDelCurs(string $curs, array $cursos): array
    {
        $cicle = $cursos[$curs]['pare'] ?? '';
        if ($cicle === '' || !isset($cursos[$cicle])) {
            $cicle = $curs;
        }

        $nomVal = $cursos[$cicle]['nom_val'] ?? '';
        $nomCas = $cursos[$cicle]['nom_cas'] ?? '';

        return [
            'codi' => $cicle,
            'nom' => $nomVal ?: $nomCas,
            'nom_val' => $nomVal,
            'nom_cas' => $nomCas,
        ];
    }

    /**
     * Puja per la jerarquia de cursos fins al node família sense pare.
     *
     * @param array<string, array{pare:string, nom_val:string, nom_cas:string}> $cursos
     * @return array{codi:string, nom_val:string, nom_cas:string}|null
     */
    private function familiaProfessionalDelCurs(string $curs, array $cursos): ?array
    {
        $actual = $curs;
        $visitats = [];

        while (isset($cursos[$actual])) {
            if (isset($visitats[$actual])) {
                return null;
            }
            $visitats[$actual] = true;

            $pare = $cursos[$actual]['pare'];
            if ($pare === '') {
                return [
                    'codi' => $actual,
                    'nom_val' => $cursos[$actual]['nom_val'],
                    'nom_cas' => $cursos[$actual]['nom_cas'],
                ];
            }
            if (!isset($cursos[$pare])) {
                return null;
            }

            $actual = $pare;
        }

        return null;
    }

    /**
     * Resol el node `curso` immediatament inferior a la família professional.
     *
     * @param array<string, array{pare:string, nom_val:string, nom_cas:string}> $cursos
     * @return array{codi:string, nom_val:string, nom_cas:string}|null
     */
    private function nivellFormatiuDelCurs(string $curs, array $cursos): ?array
    {
        $actual = $curs;
        $visitats = [];

        while (isset($cursos[$actual])) {
            if (isset($visitats[$actual])) {
                return null;
            }
            $visitats[$actual] = true;

            $pare = $cursos[$actual]['pare'];
            if ($pare === '' || !isset($cursos[$pare])) {
                return null;
            }

            if ($cursos[$pare]['pare'] === '') {
                return [
                    'codi' => $actual,
                    'nom_val' => $cursos[$actual]['nom_val'],
                    'nom_cas' => $cursos[$actual]['nom_cas'],
                ];
            }

            $actual = $pare;
        }

        return null;
    }
}
