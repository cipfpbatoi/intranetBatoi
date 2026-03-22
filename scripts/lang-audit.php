<?php

declare(strict_types=1);

/**
 * Auditoria bàsica dels catàlegs de traducció.
 *
 * Comprovacions:
 * - existència dels fitxers de catàleg requerits per idioma
 * - paritat de claus entre `ca` i la resta d'idiomes per fitxer
 * - patrons de naming potencialment incoherents en `messages.php`
 * - valors duplicats dins de `messages.php`
 * - agrupació de duplicats coneguts que no s'han de podar a cegues
 *
 * Ús:
 *   php scripts/lang-audit.php
 *   php scripts/lang-audit.php --strict-parity
 */

$root = dirname(__DIR__);
$langRoot = $root.'/resources/lang';
$baseLang = 'ca';
$catalogs = ['auth', 'messages', 'models', 'pagination', 'passwords', 'validation'];
$langs = ['ca', 'es', 'en'];
$strictParity = in_array('--strict-parity', $argv, true);
$parityErrors = 0;

/**
 * @return array<string, mixed>
 */
function loadCatalog(string $path): array
{
    if (!file_exists($path)) {
        return [];
    }

    /** @var array<string, mixed> $data */
    $data = include $path;
    return $data;
}

/**
 * @param array<string, mixed> $data
 * @return array<string, string>
 */
function flattenCatalog(array $data): array
{
    $flat = [];

    $walk = static function (array $node, string $prefix = '') use (&$walk, &$flat): void {
        foreach ($node as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;
            if (is_array($value)) {
                $walk($value, $path);
                continue;
            }

            $flat[$path] = (string) $value;
        }
    };

    $walk($data);
    ksort($flat);

    return $flat;
}

/**
 * @param array<string, string> $flat
 * @return list<string>
 */
function suspiciousMessageKeys(array $flat): array
{
    $suspects = [];

    foreach (array_keys($flat) as $key) {
        if (preg_match('/^buttons\.[A-Z]/', $key) === 1) {
            $suspects[] = $key;
            continue;
        }

        if (preg_match('/^menu\.[A-Z]/', $key) === 1) {
            $suspects[] = $key;
            continue;
        }

        if (preg_match('/[a-z][A-Z]/', $key) === 1) {
            $suspects[] = $key;
        }
    }

    sort($suspects);

    return array_values(array_unique($suspects));
}

/**
 * @param array<string, string> $flat
 * @return array<string, list<string>>
 */
function duplicateValues(array $flat): array
{
    $duplicates = [];

    foreach ($flat as $key => $value) {
        if ($value === '') {
            continue;
        }

        $duplicates[$value][] = $key;
    }

    foreach ($duplicates as $value => $keys) {
        if (count($keys) < 2) {
            unset($duplicates[$value]);
        }
    }

    arsort($duplicates);

    return $duplicates;
}

/**
 * @return array<string, string>
 */
function knownDuplicateNotes(): array
{
    return [
        'Actes' => 'Duplicat transversal entre buttons/generic/menu; probable deute històric de UI.',
        'Enquestes' => 'Pot dependre de noms de menú dinàmics (`Enquestes` vs `Poll`).',
        'Empreses' => 'Pot dependre de noms de menú dinàmics (`Empresa` vs `Empresas`).',
        'Equip directiu' => 'Possible alias funcional entre menú curt i menú visible.',
        'Seguiments' => 'Pot vindre de menú/config i no només d\'ús literal.',
        'Autorització d\'horaris' => 'Claus de menú aparentment redundants, però de risc per noms dinàmics.',
        'Reunions' => 'Hi ha convivència entre generic i menú.',
        'Direcció' => 'Convivència entre generic i rol.',
        'Calendari Escolar' => 'Convivència entre generic i menú.',
        'Activitats' => 'Convivència entre generic i menú.',
        'Horari' => 'Convivència entre button i generic.',
        'Gestor Documental' => 'Convivència entre button i menú.',
        'Accedir com a eixa persona' => 'Convivència entre button i generic.',
        'Avisar' => 'Clau `mensaje` encara viva via accions dinàmiques.',
        'Alumnat' => 'Convivència entre button i menú.',
    ];
}

echo "== Lang Audit ==\n";
echo "Base language: {$baseLang}\n\n";

foreach ($catalogs as $catalog) {
    echo "== {$catalog}.php ==\n";
    $basePath = "{$langRoot}/{$baseLang}/{$catalog}.php";
    $baseExists = file_exists($basePath);
    echo "- {$baseLang}: ".($baseExists ? 'present' : 'missing')."\n";
    if (!$baseExists) {
        $parityErrors++;
        echo "\n";
        continue;
    }

    $base = flattenCatalog(loadCatalog($basePath));

    foreach ($langs as $lang) {
        if ($lang === $baseLang) {
            continue;
        }

        $path = "{$langRoot}/{$lang}/{$catalog}.php";
        if (!file_exists($path)) {
            echo "- {$lang}: file missing\n";
            $parityErrors++;
            continue;
        }

        $current = flattenCatalog(loadCatalog($path));
        $extra = array_values(array_diff(array_keys($current), array_keys($base)));
        $missing = array_values(array_diff(array_keys($base), array_keys($current)));

        echo "- {$lang}: extra=".count($extra).", missing=".count($missing)."\n";
        if ($extra !== [] || $missing !== []) {
            $parityErrors++;
        }
        if ($extra !== []) {
            echo "  extra sample: ".implode(', ', array_slice($extra, 0, 8))."\n";
        }
        if ($missing !== []) {
            echo "  missing sample: ".implode(', ', array_slice($missing, 0, 8))."\n";
        }
    }

    echo "\n";
}

$messages = flattenCatalog(loadCatalog("{$langRoot}/{$baseLang}/messages.php"));
$suspects = suspiciousMessageKeys($messages);

echo "== Naming suspects in ca/messages.php ==\n";
echo count($suspects)." keys\n";
if ($suspects !== []) {
    echo implode("\n", array_slice($suspects, 0, 80))."\n";
}
echo "\n";

$duplicates = duplicateValues($messages);
echo "== Duplicate values in ca/messages.php ==\n";
$shown = 0;
$notes = knownDuplicateNotes();
foreach ($duplicates as $value => $keys) {
    echo count($keys)."x\t{$value}\t".implode(', ', $keys)."\n";
    if (isset($notes[$value])) {
        echo "  note: {$notes[$value]}\n";
    }
    $shown++;
    if ($shown >= 20) {
        break;
    }
}

if ($strictParity && $parityErrors > 0) {
    fwrite(STDERR, "\nParity audit failed with {$parityErrors} issue(s).\n");
    exit(1);
}
