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
     * @return list<array{id:string, modul:string, nom_modul:string, cicle:string, nom_cicle:string, any:int, nota:float, convocatoria:string}>
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
     * @return array{id:string, modul:string, nom_modul:string, cicle:string, nom_cicle:string, any:int, nota:float, convocatoria:string}|null
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
     * @return list<array{id:string, modul:string, nom_modul:string, cicle:string, nom_cicle:string, any:int, nota:float, convocatoria:string, source:string}>
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

            $cicle = $this->cicleDelCurs($curs, $cursos);
            $identity = implode('|', [$source, (string) $any, $curs, $modul, $convocatoria, (string) $nota]);
            $resultats[] = [
                'id' => hash_hmac('sha256', $identity, (string) config('app.key')),
                'modul' => $modul,
                'nom_modul' => $moduls[$clau] ?? '',
                'cicle' => $cicle['codi'],
                'nom_cicle' => $cicle['nom'],
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

    /** @return array<string, array{pare:string, nom:string}> */
    private function indexCursos(DOMXPath $xpath): array
    {
        $cursos = [];
        foreach ($xpath->query('//curso[@codigo]') as $node) {
            $cursos[$node->getAttribute('codigo')] = [
                'pare' => trim($node->getAttribute('padre')),
                'nom' => trim($node->getAttribute('nombre_val')) ?: trim($node->getAttribute('nombre_cas')),
            ];
        }

        return $cursos;
    }

    /** @return array<string, string> */
    private function indexModuls(DOMXPath $xpath): array
    {
        $moduls = [];
        foreach ($xpath->query('//contenido[@curso][@codigo]') as $node) {
            $clau = $node->getAttribute('curso') . '|' . $node->getAttribute('codigo');
            $moduls[$clau] = trim($node->getAttribute('nombre_val')) ?: trim($node->getAttribute('nombre_cas'));
        }

        return $moduls;
    }

    /**
     * @param array<string, array{pare:string, nom:string}> $cursos
     * @return array{codi:string, nom:string}
     */
    private function cicleDelCurs(string $curs, array $cursos): array
    {
        $cicle = $cursos[$curs]['pare'] ?? '';
        if ($cicle === '' || !isset($cursos[$cicle])) {
            $cicle = $curs;
        }

        return ['codi' => $cicle, 'nom' => $cursos[$cicle]['nom'] ?? ''];
    }
}
