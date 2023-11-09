<?php

namespace Intranet\Services;

use Styde\Html\Str;

class ImageService
{
    const WIDTH = 68;
    const HEIGHT = 90;

    private static function transform($fitxerOriginal)
    {
        if ($fitxerOriginal->extension() !== 'png') {
            $imatgeOriginal = imagecreatefromjpeg($fitxerOriginal);
        } else {
            $imatgeOriginal = imagecreatefrompng($fitxerOriginal);
        }

        $ampladaOriginal = imagesx($imatgeOriginal);
        $alcadaOriginal = imagesy($imatgeOriginal);
        $imatgeRedimensionada = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagecopyresampled(
            $imatgeRedimensionada,
            $imatgeOriginal,
            0,
            0,
            0,
            0,
            self::WIDTH,
            self::HEIGHT,
            $ampladaOriginal,
            $alcadaOriginal
        );
        imagedestroy($imatgeOriginal);
        imagedestroy($imatgeRedimensionada);

        return $imatgeRedimensionada;
    }

    public static function updatePhotoCarnet($fitxerOriginal,$fitxerDesti)
    {
        $imatgeRedimensionada = self::transform($fitxerOriginal);
        imagepng($imatgeRedimensionada, $fitxerDesti);
    }
    public static function newPhotoCarnet($fitxerOriginal,$directoriDesti) : String
    {
        $imatgeRedimensionada = self::transform($fitxerOriginal);

        $nomFitxer = Str::random(40) . '.png';
        $fitxerDesti = $directoriDesti . '/' . $nomFitxer;
        imagepng($imatgeRedimensionada, $fitxerDesti);

        return $nomFitxer;
    }

    public static function toPng($fitxerOriginal,$fitxerDesti)
    {
        if ($fitxerOriginal->extension() !== 'png') {
            $imatgeOriginal = imagecreatefromjpeg($fitxerOriginal);
            imagepng($imatgeOriginal, $fitxerDesti);
        } else {
            $fitxerOriginal->storeAs($fitxerDesti);
        }
    }
}
