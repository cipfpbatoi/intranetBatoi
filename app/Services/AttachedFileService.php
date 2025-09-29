<?php


namespace Intranet\Services;

use Illuminate\Support\Facades\File;
use Intranet\Entities\Adjunto;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\AlumnoFct;
use Styde\Html\Facades\Alert;

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


            Storage::disk('local')->makeDirectory("public/adjuntos/$route");

            $destinationPath = "public/adjuntos/{$route}/{$attached->title}.{$attached->extension}";

            if (File::move($filePath, storage_path('app/' . $destinationPath))) {
                $attached->save();
                return 1;
            }
        }
        return 0;
    }

    public static function moveAndPreserveDualFiles()
    {
        $alumnesDual = AlumnoFct::esDual()->get();
        $dualRoutes = [];

        foreach ($alumnesDual as $alumne) {
            if ($alumne->Alumno->Grupo->first() && $alumne->Alumno->Grupo->first()->curso == 1) {
                $route = "alumnofctaval/{$alumne->id}";
                $newRoute = "dual/{$alumne->Alumno->dni}";
                $adjuntos = Adjunto::where('route', $route)->get();

                foreach ($adjuntos as $adjunto) {
                    $file = "{$adjunto->title}.{$adjunto->extension}";
                    $oldPath = "adjuntos/{$route}/{$file}";
                    $newPath = "adjuntos/{$newRoute}/{$file}";

                    if (is_null($adjunto->referencesTo)) {
                        if (!Storage::disk('public')->exists($oldPath)) {
                            Alert::warning("El fitxer no existeix: $oldPath");
                            continue;
                        }

                        if (!Storage::disk('public')->exists(dirname($newPath))) {
                            Storage::disk('public')->makeDirectory(dirname($newPath));
                        }

                        if (Storage::disk('public')->move($oldPath, $newPath)) {
                            $adjunto->route = $newRoute;
                            $dualRoutes[] = $newRoute;
                            $adjunto->save();
                            Alert::info("De $oldPath a $newPath");
                        } else {
                            Alert::warning("No he pogut moure el fitxer de $oldPath a $newPath");
                        }
                    }
                }
            }
        }

        self::deleteNonDualFiles($dualRoutes);
    }

    private static function deleteNonDualFiles($dualRoutes)
    {
        $allAdjuntos = Adjunto::all();
        $directoriesToCheck = [];

        foreach ($allAdjuntos as $adjunto) {
            if (!in_array($adjunto->route, $dualRoutes)) {
                $oldPath = "adjuntos/{$adjunto->route}/{$adjunto->title}.{$adjunto->extension}";
                $directory = dirname($oldPath);
                $directoriesToCheck[] = $directory;

                if (Storage::disk('public')->delete($oldPath)) {
                    $adjunto->delete();
                    Alert::info("Esborrat $oldPath");
                } else {
                    Alert::warning("No esborrat $oldPath");
                }
            }
        }

        $directoriesToCheck = array_unique($directoriesToCheck);
        foreach ($directoriesToCheck as $directory) {
            if (Storage::disk('public')->exists($directory) && empty(Storage::disk('public')->files($directory))) {
                Storage::disk('public')->deleteDirectory($directory);
                Alert::info("Directori esborrat: $directory");
            }
        }
    }
}
