<?php


namespace Intranet\Services;

use Intranet\Entities\Adjunto;
use Illuminate\Support\Facades\Storage;

class AttachedFileService
{
    private static function safeFile($file, string $route, ?string $dni, ?string $title): int
    {
        $nameFile = $file->getClientOriginalName();
        $adjunto = Adjunto::findByName($route, $nameFile)->first();

        if (!$adjunto) {
            $attached = new Adjunto();
            $attached->route = $route;
            $attached->name = $nameFile;
            $attached->title = $title ?? str_shuffle('abcdefgh123456');
            $attached->extension = $file->getClientOriginalExtension();
            $attached->size = $file->getSize();
            $attached->owner = $dni;

            // Crea el directori si no existeix dins de "public/adjuntos"
            Storage::makeDirectory("public/adjuntos/$route");

            $destinationPath = "public/adjuntos/{$route}/{$attached->title}.{$attached->extension}";

            if (Storage::putFileAs("public/adjuntos/$route", $file, "{$attached->title}.{$attached->extension}")) {
                $attached->save();
                return 1;
            }
        }
        return 0;
    }

    public static function saveLink(string $nameFile, string $referencesTo, string $title, string $extension, string $route, ?string $dni = null): int
    {
        $adjunto = Adjunto::findByName($route, $nameFile)->first();

        if (!$adjunto) {
            $adjunto = new Adjunto([
                'name' => $nameFile,
                'owner' => $dni ?? authUser()->dni,
                'referencesTo' => $referencesTo,
                'title' => $title,
                'extension' => $extension,
                'size' => 1024,
                'route' => $route,
            ]);
            $adjunto->save();
        }
        return 0;
    }

    public static function save($files, string $route, ?string $dni = null, ?string $title = null): array
    {
        return array_map(fn($file) => self::safeFile($file, $route, $dni, $title), is_array($files) ? $files : [$files]);
    }

    public static function delete(Adjunto $attached): int
    {
        $filePath = "public/adjuntos/{$attached->route}/{$attached->title}.{$attached->extension}";
        $directory = "public/adjuntos/{$attached->route}";

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        $attached->delete();

        // Comprovar si el directori està buit abans d'eliminar-lo
        if (Storage::exists($directory) && empty(Storage::files($directory))) {
            Storage::deleteDirectory($directory);
        }

        return 1;
    }

    public static function saveExistingFile(string $filePath, string $route, string $dni, ?string $title = null): int
    {
        if (!file_exists($filePath)) {
            return 0;
        }

        $nameFile = basename($filePath);
        $fileSize = filesize($filePath);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        $adjunto = Adjunto::findByName($route, $nameFile)->first();
        if (!$adjunto) {
            $attached = new Adjunto();
            $attached->route = $route;
            $attached->name = $nameFile;
            $attached->title = $title ?? str_shuffle('abcdefgh123456');
            $attached->extension = $fileExtension;
            $attached->size = $fileSize;
            $attached->owner = $dni;

            // Crea el directori dins de "public/adjuntos"
            Storage::makeDirectory("public/adjuntos/$route");

            $destinationPath = "public/adjuntos/{$route}/{$attached->title}.{$attached->extension}";

            if (Storage::move($filePath, $destinationPath)) {
                $attached->save();
                return 1;
            }
        }
        return 0;
    }
}
