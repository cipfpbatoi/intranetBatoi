<?php

namespace Intranet\Services;

use Intranet\Entities\Profesor;

class SignaturaService
{
    public static function exec($dni, $style='', $ratio=1, $notFound=null)
    {
        $profesor = Profesor::find($dni);
        $name = $profesor->foto;
        $x = $ratio * 260;
        $y = $ratio * 220;
        if ($name && file_exists(storage_path('/app/public/signatures/'.$name))) {
            $ruta = public_path('/storage/signatures/'.$name);
            return "<div style='".$style."'><img style='width:".(int)$x."px;heigth:"
                .(int)$y."px' src='".$ruta."' alt='Signatura:'/></div>";
        } else {
            return $notFound;
        }
    }

    public static function peu($dni)
    {
        $profesor = Profesor::find($dni);
        $name = $profesor->foto;
        if ($name && file_exists(storage_path().'/app/public/peus/'.$name)) {
            $ruta = public_path('/storage/peus/'.$name);
            $imgBase64 = chunk_split(base64_encode(file_get_contents($ruta)));
            return "<img alt='' style='max-witdh:75%;display:block;margin:auto' src=".'"data:image/png;base64,'
                .$imgBase64.'"  />';
        } else {
            return '';
        }
    }


    public static function exists($dni)
    {
        $profesor = Profesor::find($dni);
        $name = $profesor->foto;
        return file_exists(storage_path().'/app/public/peus/'.$name);
    }
    /*

    public static function getFile($dni)
    {
        return "/Users/igomis/code/intranetBatoi/storage/app/public/signatures/$dni.png";
        //return storage_path().'/app/public/signatures/'.$dni.'.png';
    }
     */
}
