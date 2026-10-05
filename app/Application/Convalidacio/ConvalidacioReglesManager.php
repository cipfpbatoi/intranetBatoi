<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/** Llig, valida i guarda de manera privada el catàleg YAML de convalidacions. */
class ConvalidacioReglesManager
{
    private const PATH = 'regles-automatiques/convalidacions.yaml';
    private const MAX_BYTES = 2_097_152;

    /** Retorna les metadades i regles del catàleg actiu, si n'hi ha. */
    public function actual(): array
    {
        $disk = Storage::disk('convalidacions');
        $esCarregat = $disk->exists(self::PATH);
        $rutaInicial = resource_path('convalidacions/regles-lfp.yaml');
        if (!$esCarregat && !is_file($rutaInicial)) {
            return ['version' => null, 'titol' => null, 'metadata' => [], 'sha256' => null, 'actualitzat' => null, 'regles' => []];
        }

        $contingut = $esCarregat ? $disk->get(self::PATH) : file_get_contents($rutaInicial);
        if ($contingut === false) {
            throw new ConvalidacioException('No s\'ha pogut llegir el catàleg de regles.');
        }
        $catalog = $this->parse($contingut);

        return [
            'version' => (string) $catalog['version'],
            'titol' => (string) ($catalog['metadata']['title'] ?? 'Regles de convalidació'),
            'metadata' => $catalog['metadata'],
            'sha256' => hash('sha256', $contingut),
            'actualitzat' => $esCarregat ? $disk->lastModified(self::PATH) : filemtime($rutaInicial),
            'origen' => $esCarregat ? 'carregat' : 'inclòs amb l’aplicació',
            'regles' => array_values($catalog['convalidations']),
        ];
    }

    /** Retorna els bytes exactes del catàleg actiu o null si no n'hi ha cap. */
    public function contingutActual(): ?string
    {
        $disk = Storage::disk('convalidacions');
        if ($disk->exists(self::PATH)) {
            $contingut = $disk->get(self::PATH);

            return trim($contingut) === '' ? null : $contingut;
        }

        $rutaInicial = resource_path('convalidacions/regles-lfp.yaml');
        if (!is_file($rutaInicial)) {
            return null;
        }

        $contingut = file_get_contents($rutaInicial);

        return $contingut === false || trim($contingut) === '' ? null : $contingut;
    }

    /** Valida el contingut abans de substituir el catàleg actiu. */
    public function guardar(UploadedFile $fitxer): array
    {
        if ($fitxer->getSize() > self::MAX_BYTES) {
            throw new ConvalidacioException('El fitxer de regles no pot superar els 2 MB.');
        }

        $contingut = file_get_contents($fitxer->getRealPath());
        if ($contingut === false || trim($contingut) === '') {
            throw new ConvalidacioException('El fitxer de regles està buit o no es pot llegir.');
        }

        $this->parse($contingut);
        if (!Storage::disk('convalidacions')->put(self::PATH, $contingut)) {
            throw new ConvalidacioException('No s\'ha pogut guardar el fitxer de regles.');
        }

        return $this->actual();
    }

    /** Analitza YAML segur i valida l'estructura general sense descartar regles incompletes. */
    private function parse(string $contingut): array
    {
        try {
            $catalog = Yaml::parse($contingut, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
        } catch (ParseException $exception) {
            throw new ConvalidacioException('El fitxer no conté un YAML vàlid: ' . $exception->getMessage());
        }

        if (
            !is_array($catalog)
            || !isset($catalog['version'])
            || !is_array($catalog['metadata'] ?? null)
            || !is_array($catalog['convalidations'] ?? null)
        ) {
            throw new ConvalidacioException("El YAML ha d'incloure version, metadata i convalidations.");
        }

        $ids = [];
        foreach ($catalog['convalidations'] as $rule) {
            if (!is_array($rule) || !is_string($rule['id'] ?? null) || !is_array($rule['target'] ?? null) || !is_array($rule['source'] ?? null)) {
                throw new ConvalidacioException("Cada regla ha d'identificar-se i incloure target i source.");
            }
            if (in_array($rule['id'], $ids, true)) {
                throw new ConvalidacioException('El YAML conté identificadors de regla duplicats.');
            }
            $ids[] = $rule['id'];

            if (array_key_exists('enabled', $rule) && !is_bool($rule['enabled'])) {
                throw new ConvalidacioException("El camp enabled d'una regla ha de ser true o false.");
            }
            if (array_key_exists('comment', $rule) && !is_string($rule['comment'])) {
                throw new ConvalidacioException("El camp comment d'una regla ha de ser text.");
            }
            if (isset($rule['source']['conditions']) && !is_array($rule['source']['conditions'])) {
                throw new ConvalidacioException('Les condicions de la regla han de ser un conjunt de camps YAML.');
            }
        }

        return $catalog;
    }
}
