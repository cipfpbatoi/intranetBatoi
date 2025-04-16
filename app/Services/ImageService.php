<?php
namespace Intranet\Services;

use Styde\Html\Str;

class ImageService
{
    const WIDTH = 68;
    const HEIGHT = 90;

    private static function transform($fitxerOriginal)
    {
        // Creem la imatge segons l’extensió
        if ($fitxerOriginal->extension() !== 'png') {
            $imatgeOriginal = imagecreatefromjpeg($fitxerOriginal);
        } else {
            $imatgeOriginal = imagecreatefrompng($fitxerOriginal);
        }

        $ampladaOriginal = imagesx($imatgeOriginal);
        $alcadaOriginal = imagesy($imatgeOriginal);

        // Creem la imatge redimensionada
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

        // Només destruïm la imatge original, no la redimensionada
        imagedestroy($imatgeOriginal);

        // Retornem la imatge redimensionada i no la toquem fins que la guardem
        return $imatgeRedimensionada;
    }

    public static function updatePhotoCarnet($fitxerOriginal, $fitxerDesti)
    {
        $imatgeRedimensionada = self::transform($fitxerOriginal);
        $result = imagepng($imatgeRedimensionada, $fitxerDesti);
        imagedestroy($imatgeRedimensionada); // Aquí sí, després de guardar

        return $result;
    }

    public static function newPhotoCarnet($fitxerOriginal, $directoriDesti) : string
    {
        $imatgeRedimensionada = self::transform($fitxerOriginal);

        $nomFitxer = Str::random(40) . '.png';
        $fitxerDesti = $directoriDesti . '/' . $nomFitxer;

        imagepng($imatgeRedimensionada, $fitxerDesti);
        imagedestroy($imatgeRedimensionada);

        return $nomFitxer;
    }

    public static function toPng($fitxerOriginal, $fitxerDesti)
    {
        if ($fitxerOriginal->extension() !== 'png') {
            $imatgeOriginal = imagecreatefromjpeg($fitxerOriginal);
            imagepng($imatgeOriginal, $fitxerDesti);
            imagedestroy($imatgeOriginal);
        } else {
            // Si ja és PNG, simplement el movem
            $fitxerOriginal->move(
                pathinfo($fitxerDesti, PATHINFO_DIRNAME),
                pathinfo($fitxerDesti, PATHINFO_BASENAME)
            );
        }
    }
}
