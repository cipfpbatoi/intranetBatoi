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
            $attached->title = $title ?? pathinfo($file->getClientOriginalName())['filename'];
            $attached->extension = pathinfo($file->getClientOriginalName())['extension'];
            $attached->size = $file->getSize();
            $attached->owner = $dni;

            if ($file->move($attached->directory, $nameFile)) {
                $attached->save();
                return 1;
            }
        }
        return 0;
    }

    public static function saveLink($nameFile, $referencesTo, $title, $extension, $route)
    {
        $adjunto = Adjunto::findByName($route, $nameFile)->first();
        if (!$adjunto) {
            $adjunto = new Adjunto([
                'name' => $nameFile,
                'owner' => authUser()->dni,
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
}
