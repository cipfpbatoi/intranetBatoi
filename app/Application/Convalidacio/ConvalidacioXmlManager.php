<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/** Gestiona les exportacions XML privades sense oferir-ne descàrrega. */
class ConvalidacioXmlManager
{
    public function __construct(private readonly ResultatsAcademicsXmlService $reader)
    {
    }

    /** @return list<array{id:string, nom:string, any:int, mida:int, modificat:int}> */
    public function all(): array
    {
        $disk = Storage::disk('convalidacions_xml');

        return collect($disk->files())
            ->filter(static fn (string $path): bool => strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'xml')
            ->map(fn (string $path): array => [
                'id' => $this->id($path),
                'nom' => basename($path),
                'any' => $this->year($disk, $path),
                'mida' => $disk->size($path),
                'modificat' => $disk->lastModified($path),
            ])
            ->sort(static fn (array $a, array $b): int => [$b['any'], $b['modificat'], $a['nom']] <=> [$a['any'], $a['modificat'], $b['nom']])
            ->values()
            ->all();
    }

    /** Guarda un XML nou després de validar-lo completament. */
    public function store(UploadedFile $file): void
    {
        $contents = $this->contents($file);
        $this->reader->validarContingut($contents);
        $name = $this->availableName($file->getClientOriginalName());
        $this->atomicWrite($name, $contents);
    }

    /** Elimina una font perquè deixe de participar en consultes futures. */
    public function delete(string $id): void
    {
        $path = $this->resolve($id);
        if (!Storage::disk('convalidacions_xml')->delete($path)) {
            throw new ConvalidacioException('No s\'ha pogut eliminar el fitxer.');
        }
    }

    /** Retorna un nom opac estable per a una ruta privada. */
    private function id(string $path): string
    {
        return hash_hmac('sha256', $path, (string) config('app.key'));
    }

    /** Resol un identificador sense acceptar noms de fitxer des del navegador. */
    private function resolve(string $id): string
    {
        foreach (Storage::disk('convalidacions_xml')->files() as $path) {
            if (hash_equals($this->id($path), $id)) {
                return $path;
            }
        }

        throw new ConvalidacioException('No s\'ha trobat el fitxer indicat.');
    }

    /** Llig el fitxer temporal sense conservar-lo si és invàlid. */
    private function contents(UploadedFile $file): string
    {
        $contents = file_get_contents($file->getRealPath());
        if ($contents === false || $contents === '') {
            throw new ConvalidacioException('El fitxer XML està buit o no es pot llegir.');
        }

        return $contents;
    }

    /** Genera un nom segur i no destructiu per a una càrrega nova. */
    private function availableName(string $original): string
    {
        $base = Str::slug(pathinfo($original, PATHINFO_FILENAME)) ?: 'avaluacio';
        $name = $base . '.xml';
        $suffix = 2;
        while (Storage::disk('convalidacions_xml')->exists($name)) {
            $name = $base . '-' . $suffix++ . '.xml';
        }

        return $name;
    }

    /** Llig l'any acadèmic de l'XML; la metadada desapareix en eliminar la font. */
    private function year(Filesystem $disk, string $path): int
    {
        try {
            return $this->reader->validarContingut($disk->get($path));
        } catch (ConvalidacioException) {
            return 0;
        }
    }

    /** Escriu en el disc local privat i reemplaça el destí amb rename atòmic. */
    private function atomicWrite(string $path, string $contents): void
    {
        $disk = Storage::disk('convalidacions_xml');
        $target = $disk->path($path);
        $directory = dirname($target);
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new ConvalidacioException('No s\'ha pogut preparar l\'emmagatzematge privat.');
        }

        $temporary = tempnam($directory, '.xml-');
        if ($temporary === false) {
            throw new ConvalidacioException('No s\'ha pogut preparar el fitxer temporal.');
        }

        try {
            if (file_put_contents($temporary, $contents, LOCK_EX) === false) {
                throw new RuntimeException('No s\'ha pogut escriure el fitxer temporal.');
            }
            chmod($temporary, 0600);
            if (!rename($temporary, $target)) {
                throw new RuntimeException('No s\'ha pogut reemplaçar el fitxer.');
            }
        } catch (RuntimeException $exception) {
            @unlink($temporary);
            throw new ConvalidacioException($exception->getMessage());
        }
    }
}
