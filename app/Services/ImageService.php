<?php

namespace Intranet\Services;

use Illuminate\Http\UploadedFile;
use Styde\Html\Str;

class ImageService
{
    const WIDTH  = 68;
    const HEIGHT = 90;

    /**
     * Obri una imatge GD des d'un UploadedFile o path, detectant el tipus real.
     */
    private static function openGdImage($source)
    {
        // Obtén path real
        if ($source instanceof UploadedFile) {
            $path = $source->getRealPath();
        } else {
            $path = (string) $source;
        }

        if (!$path || !is_readable($path) || filesize($path) === 0) {
            throw new \RuntimeException('El fitxer d\'origen és inexistent, il·legible o buit.');
        }

        // Detecció segura del tipus real
        $info = @getimagesize($path);
        if (!$info || !isset($info[2])) {
            throw new \RuntimeException('No s\'ha pogut detectar el tipus d\'imatge.');
        }

        $type = $info[2]; // una de les constants IMAGETYPE_*

        switch ($type) {
            case IMAGETYPE_JPEG:
                $img = @imagecreatefromjpeg($path);
                // Opcional: corregir orientació EXIF
                if (function_exists('exif_read_data')) {
                    $exif = @exif_read_data($path);
                    if (!empty($exif['Orientation'])) {
                        $orientation = (int) $exif['Orientation'];
                        if ($orientation === 3)      { $img = imagerotate($img, 180, 0); }
                        elseif ($orientation === 6) { $img = imagerotate($img, -90, 0); }
                        elseif ($orientation === 8) { $img = imagerotate($img, 90, 0); }
                    }
                }
                return $img;

            case IMAGETYPE_PNG:
                $img = @imagecreatefrompng($path);
                // Assegura canal alfa
                imagesavealpha($img, true);
                imagealphablending($img, false);
                return $img;

            case IMAGETYPE_GIF:
                return @imagecreatefromgif($path);

            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    return @imagecreatefromwebp($path);
                }
                throw new \RuntimeException('WebP detectat però GD no té suport per WebP.');

            default:
                throw new \RuntimeException('Tipus d\'imatge no suportat (només JPEG/PNG/GIF/WebP).');
        }
    }

    /**
     * Redimensiona a 68x90 mantenint proporció i farcint amb transparent (PNG).
     */
    private static function transform($fitxerOriginal)
    {
        $src = self::openGdImage($fitxerOriginal);

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        // Canvas final amb transparència
        $dst = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, self::WIDTH, self::HEIGHT, $transparent);

        // Escalat "cover" amb centrejat
        $scale = max(self::WIDTH / $srcW, self::HEIGHT / $srcH);
        $newW  = (int) round($srcW * $scale);
        $newH  = (int) round($srcH * $scale);
        $offX  = (int) floor((self::WIDTH  - $newW) / 2);
        $offY  = (int) floor((self::HEIGHT - $newH) / 2);

        imagecopyresampled($dst, $src, $offX, $offY, 0, 0, $newW, $newH, $srcW, $srcH);
        imagedestroy($src);

        return $dst; // **no** la destruïm ací
    }

    public static function updatePhotoCarnet($fitxerOriginal, $fitxerDesti)
    {
        $dst = self::transform($fitxerOriginal);
        $ok  = imagepng($dst, $fitxerDesti);
        imagedestroy($dst);
        if (!$ok) {
            throw new \RuntimeException('No s\'ha pogut guardar la imatge PNG de destí.');
        }
        return true;
    }

    public static function newPhotoCarnet($fitxerOriginal, $directoriDesti): string
    {
        $dst = self::transform($fitxerOriginal);

        $nomFitxer  = Str::random(40) . '.png';
        $fitxerDesti = rtrim($directoriDesti, '/').'/'.$nomFitxer;

        $ok = imagepng($dst, $fitxerDesti);
        imagedestroy($dst);
        if (!$ok) {
            throw new \RuntimeException('No s\'ha pogut guardar la imatge PNG de destí.');
        }

        return $nomFitxer;
    }

    public static function toPng($fitxerOriginal, $fitxerDesti)
    {
        // Si és PNG real, simplement mou
        try {
            $img = self::openGdImage($fitxerOriginal);
        } catch (\RuntimeException $e) {
            throw $e;
        }

        // Guardar com a PNG i tancar
        $ok = imagepng($img, $fitxerDesti);
        imagedestroy($img);
        if (!$ok) {
            throw new \RuntimeException('No s\'ha pogut guardar el PNG de destí.');
        }
    }
}
