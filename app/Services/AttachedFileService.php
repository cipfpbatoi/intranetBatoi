<?php

namespace Intranet\Services;

use Intranet\Entities\Adjunto;

class AttachedFileService
{

    private static function safeFile($file, $route, $dni, $title)
    {
        $nameFile = $file->getClientOriginalName();
        $adjunto = Adjunto::findByName($route, $nameFile)->first();
        if (!$adjunto) {
            $attached = new Adjunto();
            $attached->route = $route;
            $attached->name = $nameFile;
            $attached->title = $title ?? str_shuffle('abcdefgh123456');
            $attached->extension = pathinfo($file->getClientOriginalName())['extension'];
            $attached->size = $file->getSize();
            $attached->owner = $dni;


            if ($file->move($attached->directory, $attached->title.'.'.$attached->extension)) {
                $attached->save();
                return 1;
            }
        }
        return 0;
    }

    public static function saveLink($nameFile, $referencesTo, $title, $extension, $route, $dni=null)
    {
        $adjunto = Adjunto::findByName($route, $nameFile)->first();
        if (!$adjunto) {
            $adjunto = new Adjunto([
                'name' => $nameFile,
                'owner' => $dni??authUser()->dni,
                'referencesTo' => $referencesTo,
                'title' => $title,
                'extension' => $extension,
                'size' => 1024,
                'route' => $route
            ]);
            $adjunto->save();
        }
        return 0;
    }


    public static function save($files, $route, $dni=null, $title=null)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                return self::safeFile($file, $route, $dni, $title);
            }
        }
        return self::safeFile($files, $route, $dni, $title);
    }

    public static function delete($attached)
    {
        if (is_file($attached->path)) {
            unlink($attached->path);
            $attached->delete();
            return 1;
        }
        $attached->delete();
        return 1;
    }
    public static function saveExistingFile($filePath, $route, $dni, $title=null)
    {

        // Comprova si el fitxer existeix
        if (!file_exists($filePath)) {
            return 0; // Si el fitxer no existeix, retorna 0
        }

        // Obtenim la informació del fitxer existent
        $nameFile = basename($filePath);
        $fileSize = filesize($filePath);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        // Comprova si el fitxer ja existeix a la base de dades
        $adjunto = Adjunto::findByName($route, $nameFile)->first();
        if (!$adjunto) {
            // Crea un nou registre Adjunto
            $attached = new Adjunto();
            $attached->route = $route;
            $attached->name = $nameFile;
            $attached->title = $title ?? str_shuffle('abcdefgh123456');
            $attached->extension = $fileExtension;
            $attached->size = $fileSize;
            $attached->owner = $dni;

            // Defineix el directori on es guardarà el fitxer
            $relativeDirectory = $attached->directory;
            $destinationDirectory = $relativeDirectory;

            // Crea el directori si no existeix
            if (!file_exists($attached->directory)) {
                mkdir($attached->directory, 0755, true);
            }

            // Defineix el camí de destí per al fitxer
            $destinationPath = $attached->directory . '/' . $attached->title . '.' . $attached->extension;

            // Mou el fitxer a la nova ubicació
            if (rename($filePath, $destinationPath)) {
                // Guarda el registre a la base de dades
                $attached->save();
                return 1;
            }
        }
        return 0;
    }

}
