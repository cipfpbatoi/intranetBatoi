<?php

namespace Intranet\Services;

use Intranet\Entities\Adjunto;


class AttachedFileService
{
    private static function safeFile($file,$model,$id,$dni,$title){
        $nameFile = $file->getClientOriginalName();
        $adjunto = Adjunto::findByName($model,$id,$nameFile)->first();
        if (!$adjunto) {
            $attached = new Adjunto();
            $attached->model_id = $id;
            $attached->model = $model;
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


    public static function save($files,$modelo,$id,$dni=null,$title=null)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                return self::safeFile($file,$modelo,$id,$dni,$title);
            }
        }
        return self::safeFile($files,$modelo,$id,$dni,$title);
    }

    public static function delete($attached){
        if (is_file($attached->path)) {
            unlink($attached->path);
            $attached->delete();
            return 1;
        }
        return 0;
    }

}