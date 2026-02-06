<?php

namespace Intranet\Services;

use Illuminate\Http\UploadedFile;
use Styde\Html\Facades\Alert;
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
        // 1) Obtén path real
        $mime = null;
        if ($source instanceof UploadedFile) {
            $path = $source->getRealPath();
            $mime = $source->getMimeType();
            $ext  = strtolower($source->getClientOriginalExtension() ?: '');

            // 🔹 Detectar HEIC/HEIF i convertir-lo abans de passar per GD
            if (in_array($mime, ['image/heic','image/heif','image/heic-sequence','image/heif-sequence'])
                || in_array($ext, ['heic','heif'])) {
                $path = self::convertHeicToPng($path); // <- nou mètode
            }
        } else {
            $path = (string) $source;
            $mime = null;
            $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($ext, ['heic','heif'])) {
                $path = self::convertHeicToPng($path);
            }
        }

        if (!$path || !is_readable($path) || filesize($path) === 0) {
            throw new \RuntimeException('El fitxer d\'origen és inexistent, il·legible o buit.');
        }

        // 2) Detecció segura del tipus real (del fitxer ja convertit si cal)
        $info = @getimagesize($path);
        if (!$info || !isset($info[2])) {
            // Pla B: intenta amb MIME detectat
            $mime = $mime ?? (function_exists('mime_content_type') ? @mime_content_type($path) : null);
            $type = self::imagetypeFromMime($mime);
            if (!$type) {
                throw new \RuntimeException(
                    "No s'ha pogut detectar el tipus d'imatge (mime: " . ($mime ?? 'desconegut') . ")."
                );
            }
        } else {
            $type = $info[2]; // una de les constants IMAGETYPE_*
        }

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

    private static function imagetypeFromMime(?string $mime): ?int
    {
        return match ($mime) {
            'image/jpeg', 'image/pjpeg'   => IMAGETYPE_JPEG,
            'image/png'                   => IMAGETYPE_PNG,
            'image/gif'                   => IMAGETYPE_GIF,
            'image/webp'                  => defined('IMAGETYPE_WEBP') ? IMAGETYPE_WEBP : null,
            default                       => null,
        };
    }

    private static function convertHeicToPng(string $inputPath): string
    {
        // 1) Primer intent: Imagick amb suport HEIC
        if (class_exists(\Imagick::class)) {
            try {
                $img = new \Imagick();
                $img->readImage($inputPath);
                $img->setImageFormat('png');
                $img->setImageCompressionQuality(90);

                // Orientació
                if (method_exists($img, 'getImageOrientation')) {
                    switch ($img->getImageOrientation()) {
                        case \Imagick::ORIENTATION_RIGHTTOP:
                            $img->rotateImage("#000", 90);
                            break;
                        case \Imagick::ORIENTATION_BOTTOMRIGHT:
                            $img->rotateImage("#000", 180);
                            break;
                        case \Imagick::ORIENTATION_LEFTBOTTOM:
                            $img->rotateImage("#000", -90);
                            break;
                    }
                    $img->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
                }

                $outputPath = sys_get_temp_dir().'/heic_'.uniqid().'.png';
                if (!$img->writeImage($outputPath)) {
                    throw new \RuntimeException('No s\'ha pogut escriure la conversió HEIC→PNG.');
                }

                $img->clear();
                $img->destroy();

                return $outputPath;
            } catch (\Throwable $e) {
                // continua a pla B
            }
        } else {
            Alert::info('Imagick no està disponible al servidor.');
        }

        // 2) Pla B: utilitat heif-convert si està instal·lada al servidor
        $outputPath = sys_get_temp_dir().'/heic_'.uniqid().'.png';
        $cmd = sprintf(
            'heif-convert %s %s 2>&1',
            escapeshellarg($inputPath),
            escapeshellarg($outputPath)
        );
        exec($cmd, $out, $code);

        if ($code === 0 && file_exists($outputPath) && filesize($outputPath) > 0) {
            return $outputPath;
        }

        throw new \RuntimeException('Imatge HEIC/HEIF no suportada al servidor.');
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
        $directori = dirname($fitxerDesti);
        if (!is_dir($directori)) {
            if (!@mkdir($directori, 0755, true) && !is_dir($directori)) {
                throw new \RuntimeException('No s\'ha pogut crear el directori de destí per a la foto.');
            }
        }

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
        if (!is_dir($directoriDesti)) {
            if (!@mkdir($directoriDesti, 0755, true) && !is_dir($directoriDesti)) {
                throw new \RuntimeException('No s\'ha pogut crear el directori de destí per a la foto.');
            }
        }

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
