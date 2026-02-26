<?php

namespace Intranet\Services\Signature;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intranet\Exceptions\CertException;
use Intranet\Exceptions\IntranetException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use Symfony\Component\Process\Process;
use Throwable;

class DigitalSignatureService
{
    /**
     * Llig i valida un certificat PKCS#12 amb OpenSSL.
     *
     * @param string $certificat Ruta al fitxer .p12/.pfx
     * @param string $password Contrasenya del certificat
     * @return array<string, mixed>
     * @throws CertException
     */
    public static function readCertificat($certificat, $password): array
    {
        return (new self())->readCertificate($certificat, $password);
    }

    /**
     * Llig i valida un certificat PKCS#12 amb OpenSSL.
     *
     * @param string $certificat Ruta al fitxer .p12/.pfx
     * @param string $password Contrasenya del certificat
     * @return array<string, mixed>
     * @throws CertException
     */
    public function readCertificate($certificat, $password): array
    {
        try {
            if (!is_string($certificat) || $certificat === '' || !file_exists($certificat)) {
                throw new CertException("No s'ha trobat el fitxer del certificat.");
            }

            $raw = file_get_contents($certificat);
            if ($raw === false) {
                throw new CertException("No s'ha pogut llegir el fitxer del certificat.");
            }

            $certificateStore = [];
            if (!openssl_pkcs12_read($raw, $certificateStore, (string) $password)) {
                throw new CertException("Password del certificat incorrecte.");
            }

            $x509 = $certificateStore['cert'] ?? null;
            if (!is_string($x509) || trim($x509) === '') {
                throw new CertException("No s'ha pogut extreure el certificat X509.");
            }

            $parsed = openssl_x509_parse($x509, false);
            if (!is_array($parsed)) {
                throw new CertException("No s'ha pogut interpretar el certificat.");
            }

            return [
                'cert' => $x509,
                'parsed' => $parsed,
                'subject' => is_array($parsed['subject'] ?? null) ? $parsed['subject'] : [],
            ];
        } catch (Throwable $th) {
            throw new CertException("Password del certificat incorrecte: ".$th->getMessage());
        }
    }

    public static function cryptCertificate($certificat, $fileName, $password): void
    {
        (new self())->encryptCertificate($certificat, $fileName, $password);
    }

    public function encryptCertificate($certificat, $fileName, $password): void
    {
        $file = $this->fileNameCrypt($fileName);
        $encrypter = $this->getEncrypter($password);
        file_put_contents($file, $encrypter->encryptString(base64_encode(file_get_contents($certificat))));

        Log::channel('certificate')->info("S'ha pujat el certificat d'usuari.", [
            'intranetUser' => authUser()->fullName,
            'Path' => $file,
        ]);
    }

    public static function decryptCertificate($fileName, $password): string
    {
        return (new self())->decryptUserCertificate($fileName, $password);
    }

    public function decryptUserCertificate($fileName, $password): string
    {
        $cryptfile = $this->fileNameCrypt($fileName);
        $decryptfile = $this->fileNameDeCrypt($fileName);
        $encrypter = $this->getEncrypter($password);

        File::put($decryptfile, base64_decode($encrypter->decryptString(file_get_contents($cryptfile))));

        Log::channel('certificate')->info("S'ha desxifrat el certificat d'usuari.", [
            'intranetUser' => authUser()->fullName,
        ]);

        return $decryptfile;
    }

    public static function decryptCertificateUser($decrypt, $user): ?string
    {
        return (new self())->decryptUserCertificateInstance($decrypt, $user);
    }

    public function decryptUserCertificateInstance($decrypt, $user): ?string
    {
        if (!Hash::check($decrypt, $user->password)) {
            throw new CertException("Password de la Intranet incorrecte");
        }
        return $this->decryptUserCertificate($user->fileName, $decrypt);
    }

    public static function deleteCertificate($user): void
    {
        (new self())->removeCertificate($user);
    }

    public function removeCertificate($user): void
    {
        unlink($user->pathCertificate);
        Log::channel('certificate')->info("S'ha esborrat el certificat d'usuari.", [
            'intranetUser' => authUser()->fullName,
        ]);
    }

    public static function validateUserSign($file)
    {
        // Flux legacy eliminat (laravel-a1-pdf-sign). Deixem retorn nul controlat.
        return null;
    }

    public function validateUserSignature($file, $dni = null): bool
    {
        // 1) Obté DNI (sense el 0 inicial)
        if (is_null($dni)) {
            $user = authUser();
            if (!$user) return false;
            $dni = substr($user->dni, 1);
        }

        // Normalitza per evitar problemes de majúscules o espais
        $dni = strtoupper(trim($dni));

        // 2) Llig la signatura
        $sig = self::validateUserSign($file);
        if (!is_array($sig)) {
            return false;
        }

        // 3) Busca el DNI en diversos camps del subjecte
        $fieldsToCheck = [
            'CN', 'serialNumber', '2.5.4.5', 'OID.2.5.4.5', 'subject', 'subjectRaw'
        ];

        foreach ($fieldsToCheck as $key) {
            if (!empty($sig[$key])) {
                $val = is_array($sig[$key]) ? implode(' ', $sig[$key]) : $sig[$key];
                if (str_contains(strtoupper($val), $dni)) {
                    return true;
                }
            }
        }

        // 4) Busca també dins del DN complet si el parser l’exposa
        if (!empty($sig['DN'])) {
            if (str_contains(strtoupper((string) $sig['DN']), $dni)) {
                return true;
            }
        }

        return false;
    }


    public static function sign($file, $newFile, $coordx, $coordy, $certPath, $certPassword): void
    {
        (new self())->signDocument($file, $newFile, $coordx, $coordy, $certPath, $certPassword);
    }

    public function signDocument($file, $newFile, $coordx, $coordy, $certPath, $certPassword): void
    {
        try {
            // 1️⃣ Primer intent normal
            $this->signWithJSignPdf($file, $newFile, $coordx, $coordy, $certPath, $certPassword);
            return;

        } catch (Throwable $th) {
            $message = $th->getMessage();

            // 2️⃣ Si l’error és per compressió no suportada (cas Ofelia)
            if (str_contains($message, 'compression technique which is not supported')) {

                Log::channel('certificate')->warning("PDF amb compressió no suportada. Intentant normalitzar-lo.", [
                    'intranetUser' => authUser()->fullName,
                    'pdfPath'      => $file,
                    'error'        => $message,
                ]);

                try {
                    // Normalitzem el PDF i tornem a intentar
                    $normalized = $this->normalizePdf($file);
                    $this->signWithJSignPdf($normalized, $newFile, $coordx, $coordy, $certPath, $certPassword);
                    return;

                } catch (Throwable $e2) {
                    Log::channel('certificate')->alert("Error al signar després de normalitzar.", [
                        'intranetUser' => authUser()->fullName,
                        'pdfPath'      => $file,
                        'error'        => $e2->getMessage(),
                    ]);

                    throw new IntranetException(
                        "No s'ha pogut signar el document: el PDF no és compatible ni després d'adaptar-lo automàticament."
                    );
                }
            }

            // 3️⃣ Altres errors → com abans
            Log::channel('certificate')->alert("Error al signar el document.", [
                'intranetUser' => authUser()->fullName,
                'pdfPath'      => $file,
                'error'        => $message,
            ]);

            throw new IntranetException("Error al signar el document.: " . $message);
        }
    }

    private function signWithJSignPdf($file, $newFile, $coordx, $coordy, $certPath, $certPassword): void
    {
        if (!is_string($file) || $file === '' || !file_exists($file)) {
            throw new IntranetException("No s'ha trobat el PDF d'entrada per a signar.");
        }
        if (!is_string($newFile) || $newFile === '') {
            throw new IntranetException("No s'ha indicat un fitxer d'eixida vàlid per a la signatura.");
        }

        $config = config('signatures.jsignpdf');
        $java = $config['java'] ?? 'java';
        $jar = $config['jar'] ?? null;

        if (!$jar || !file_exists($jar)) {
            throw new IntranetException("No s'ha trobat JSignPdf.jar. Configura JSIGNPDF_JAR i comprova el fitxer.");
        }
        if (!file_exists($certPath)) {
            throw new IntranetException("No s'ha trobat el certificat per a signar: $certPath");
        }

        $coordx = (float) ($coordx ?? 50);
        $coordy = (float) ($coordy ?? 50);
        $width = (float) ($config['width'] ?? 200);
        $height = (float) ($config['height'] ?? 70);
        $page = $this->getLastPageNumber($file, (int) ($config['page'] ?? 1));
        $append = (bool) ($config['append'] ?? true);
        $timeout = (int) ($config['timeout'] ?? 60);
        $visibleText = $this->buildVisibleSignatureText($certPath, $certPassword);
        $bgPath = $config['bg_path'] ?? null;
        $bgScale = $config['bg_scale'] ?? null;
        $imgPath = $config['img_path'] ?? null;
        $preparedBgPath = null;
        $bgCompose = (bool) ($config['bg_compose'] ?? env('JSIGNPDF_BG_COMPOSE', false));
        $logoScale = (float) ($config['logo_scale'] ?? env('JSIGNPDF_LOGO_SCALE', 0.6));
        $logoTop = (int) ($config['logo_top'] ?? env('JSIGNPDF_LOGO_TOP', 5));
        $logoMaxHeightRatio = (float) ($config['logo_max_height_ratio'] ?? env('JSIGNPDF_LOGO_MAX_HEIGHT_RATIO', 0.45));

        $outputDir = storage_path('tmp/jsignpdf_' . Str::orderedUuid());
        File::ensureDirectoryExists($outputDir);

        try {
            if (!empty($bgPath)) {
                if (!empty($config['bg_transparent'])) {
                    $preparedBgPath = $this->prepareBackgroundImage($bgPath, (int) ($config['bg_threshold'] ?? 245));
                    if ($preparedBgPath) {
                        $bgPath = $preparedBgPath;
                    }
                }
                if ($bgCompose) {
                    $composed = $this->composeLogoBackground(
                        $bgPath,
                        (int) round($width),
                        (int) round($height),
                        $logoScale,
                        $logoTop,
                        $logoMaxHeightRatio
                    );
                    if ($composed) {
                        $preparedBgPath = $composed;
                        $bgPath = $composed;
                        $bgScale = '1';
                    }
                }
            }

            $command = $this->buildJSignPdfCommand(
                $java,
                $jar,
                $file,
                $outputDir,
                $coordx,
                $coordy,
                $width,
                $height,
                $page,
                $certPath,
                $certPassword,
                $append,
                $visibleText,
                $bgPath,
                $bgScale,
                $imgPath
            );

            Log::channel('certificate')->info('JSignPdf background settings', [
                'bgCompose' => $bgCompose,
                'bgScale' => $bgScale,
                'logoScale' => $logoScale,
                'logoTop' => $logoTop,
                'logoMaxHeightRatio' => $logoMaxHeightRatio,
                'bgPath' => $bgPath,
            ]);

            Log::channel('certificate')->info('JSignPdf command', [
                'command' => $this->stringifyCommand($command),
            ]);

            $process = new Process($command);
            $process->setTimeout($timeout);
            $process->run();

            if (!$process->isSuccessful()) {
                $errorOutput = trim($process->getErrorOutput() ?: $process->getOutput());
                throw new IntranetException("Error al signar amb JSignPdf: ".$errorOutput);
            }

            $signedPath = $this->resolveJSignPdfOutputFile($outputDir, $file);
            if (!$signedPath || !file_exists($signedPath)) {
                throw new IntranetException("JSignPdf no ha generat el PDF signat.");
            }

            File::ensureDirectoryExists(dirname($newFile));
            if (!@rename($signedPath, $newFile)) {
                File::copy($signedPath, $newFile);
                File::delete($signedPath);
            }
        } finally {
            if (is_dir($outputDir)) {
                File::deleteDirectory($outputDir);
            }
            if ($preparedBgPath && file_exists($preparedBgPath)) {
                @unlink($preparedBgPath);
            }
        }

        Log::channel('certificate')->info("S'ha signat el document amb JSignPdf.", [
            'intranetUser' => authUser()->fullName,
            'pdfPath' => $file,
            'signedPdfPath'=> $newFile,
            'coordx' => $coordx,
            'coordy' => $coordy,
            'width' => $width,
            'height' => $height,
            'page' => $page,
            'append' => $append,
        ]);
    }

    private function buildJSignPdfCommand(
        string $java,
        string $jar,
        string $inputFile,
        string $outputDir,
        float $coordx,
        float $coordy,
        float $width,
        float $height,
        int $page,
        string $certPath,
        string $certPassword,
        bool $append,
        string $visibleText,
        ?string $bgPath,
        ?string $bgScale,
        ?string $imgPath
    ): array {
        $llx = (int) round($coordx);
        $lly = (int) round($coordy);
        $urx = (int) round($coordx + $width);
        $ury = (int) round($coordy + $height);

        $command = [
            $java,
            '-jar',
            $jar,
            $inputFile,
        ];

        if ($append) {
            $command[] = '-a';
        }

        $command = array_merge($command, [
            '-kst', 'PKCS12',
            '-ksf', $certPath,
            '-ksp', (string) $certPassword,
            '-pg', (string) $page,
            '-llx', (string) $llx,
            '-lly', (string) $lly,
            '-urx', (string) $urx,
            '-ury', (string) $ury,
            '--l2-text', $visibleText,
            '-V',
            '-d', $outputDir,
        ]);

        if (!empty($bgPath)) {
            $command[] = '--bg-path';
            $command[] = $bgPath;
        }
        if (!empty($bgScale)) {
            $command[] = '--bg-scale';
            $command[] = (string) $bgScale;
        }
        if (!empty($imgPath) && empty($bgPath)) {
            $command[] = '--img-path';
            $command[] = $imgPath;
        }

        return $command;
    }

    private function resolveJSignPdfOutputFile(string $outputDir, string $inputFile): ?string
    {
        $suffix = config('signatures.jsignpdf.output_suffix', '_signed');
        $baseName = pathinfo($inputFile, PATHINFO_FILENAME);
        $expected = $outputDir.DIRECTORY_SEPARATOR.$baseName.$suffix.'.pdf';
        if (file_exists($expected)) {
            return $expected;
        }

        $candidates = glob($outputDir.DIRECTORY_SEPARATOR.'*.pdf') ?: [];
        if (empty($candidates)) {
            return null;
        }

        usort($candidates, static function ($a, $b) {
            return filemtime($b) <=> filemtime($a);
        });

        return $candidates[0] ?? null;
    }

    private function stringifyCommand(array $command): string
    {
        return implode(' ', array_map(static function ($part) {
            return str_contains($part, ' ') ? "'".$part."'" : $part;
        }, $command));
    }

    private function prepareBackgroundImage(string $sourcePath, int $threshold): ?string
    {
        if (!file_exists($sourcePath)) {
            return null;
        }

        $threshold = max(0, min(255, $threshold));
        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));

        if ($ext === 'png') {
            $src = @imagecreatefrompng($sourcePath);
        } elseif (in_array($ext, ['jpg', 'jpeg'], true)) {
            $src = @imagecreatefromjpeg($sourcePath);
        } else {
            return null;
        }

        if (!$src) {
            return null;
        }

        $width = imagesx($src);
        $height = imagesy($src);
        $dst = imagecreatetruecolor($width, $height);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($src, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                if ($r >= $threshold && $g >= $threshold && $b >= $threshold) {
                    // deixe el pixel transparent
                    continue;
                }
                $color = imagecolorallocatealpha($dst, $r, $g, $b, 0);
                imagesetpixel($dst, $x, $y, $color);
            }
        }

        $tmpPath = storage_path('tmp/jsignpdf_bg_' . Str::orderedUuid() . '.png');
        imagepng($dst, $tmpPath);
        imagedestroy($src);
        imagedestroy($dst);

        return $tmpPath;
    }

    private function composeLogoBackground(
        string $sourcePath,
        int $boxWidth,
        int $boxHeight,
        float $scale,
        int $topPadding,
        float $maxHeightRatio
    ): ?string {
        if (!file_exists($sourcePath) || $boxWidth <= 0 || $boxHeight <= 0) {
            return null;
        }

        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        if ($ext === 'png') {
            $src = @imagecreatefrompng($sourcePath);
        } elseif (in_array($ext, ['jpg', 'jpeg'], true)) {
            $src = @imagecreatefromjpeg($sourcePath);
        } else {
            return null;
        }

        if (!$src) {
            return null;
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);
        if ($srcW <= 0 || $srcH <= 0) {
            imagedestroy($src);
            return null;
        }

        $scale = max(0.1, min(1.0, $scale));
        $targetW = (int) round($boxWidth * $scale);
        $ratio = $targetW / $srcW;
        $targetH = (int) round($srcH * $ratio);
        $maxH = (int) round($boxHeight * max(0.1, min(0.9, $maxHeightRatio)));
        if ($targetH > $maxH) {
            $ratio = $maxH / $srcH;
            $targetH = $maxH;
            $targetW = (int) round($srcW * $ratio);
        }

        $dst = imagecreatetruecolor($boxWidth, $boxHeight);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);

        $x = (int) max(0, floor(($boxWidth - $targetW) / 2));
        $y = (int) max(0, $topPadding);
        imagecopyresampled($dst, $src, $x, $y, 0, 0, $targetW, $targetH, $srcW, $srcH);

        $tmpPath = storage_path('tmp/jsignpdf_bg_' . Str::orderedUuid() . '.png');
        imagepng($dst, $tmpPath);
        imagedestroy($src);
        imagedestroy($dst);

        return $tmpPath;
    }

    private function getLastPageNumber(string $inputFile, int $fallback): int
    {
        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($inputFile);
            return max(1, (int) $pageCount);
        } catch (Throwable $th) {
            Log::channel('certificate')->warning("No s'ha pogut calcular l'última pàgina.", [
                'pdfPath' => $inputFile,
                'error' => $th->getMessage(),
            ]);
            return max(1, $fallback);
        }
    }

    private function buildVisibleSignatureText(string $certPath, string $certPassword): string
    {
        $signer = null;
        try {
            $cert = $this->readCertificate($certPath, $certPassword);
            $subject = is_array($cert['subject'] ?? null) ? $cert['subject'] : [];
            $signer = $subject['CN'] ?? $subject['commonName'] ?? null;
        } catch (Throwable $th) {
            Log::channel('certificate')->warning("No s'ha pogut obtindre el nom del certificat.", [
                'pdfCert' => $certPath,
                'error' => $th->getMessage(),
            ]);
        }

        if (!$signer && function_exists('authUser') && authUser()) {
            $signer = authUser()->fullName;
        }

        $signer = $signer ?: 'Signant';
        $date = now()->format('d/m/Y');

        return "Signat per {$signer} a data de {$date}";
    }

    private function normalizePdf(string $inputFile): string
    {
        $info = pathinfo($inputFile);
        $outputFile = $info['dirname'].'/'.$info['filename'].'_norm.'.$info['extension'];

        $cmd = sprintf(
            'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -sOutputFile=%s %s 2>&1',
            escapeshellarg($outputFile),
            escapeshellarg($inputFile)
        );

        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0 || !file_exists($outputFile) || filesize($outputFile) === 0) {
            Log::channel('certificate')->error('Error normalitzant PDF per a signatura', [
                'cmd'    => $cmd,
                'output' => $output,
                'code'   => $returnVar,
            ]);
            throw new IntranetException("No s'ha pogut adaptar el PDF per a la signatura.");
        }

        return $outputFile;
    }


    private function getEncrypter($password): Encrypter
    {
        return new Encrypter(substr('base64:' . $password . config('app.key'), 0, 32), config('app.cipher'));
    }

    private function fileNameCrypt($fileName): string
    {
        return storage_path('app/zip/' . $fileName . '.tmp');
    }

    private function fileNameDeCrypt($fileName): string
    {
        return storage_path('tmp/' . $fileName . '.pfx');
    }
}
