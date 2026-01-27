<?php

namespace Intranet\Services;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intranet\Exceptions\CertException;
use Intranet\Exceptions\IntranetException;
use LSNepomuceno\LaravelA1PdfSign\Sign\ManageCert;
use LSNepomuceno\LaravelA1PdfSign\Sign\ValidatePdfSignature;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Throwable;

class DigitalSignatureService
{
    public static function readCertificat($certificat, $password): ManageCert
    {
        return (new self())->readCertificate($certificat, $password);
    }

    public function readCertificate($certificat, $password): ManageCert
    {
        try {
            return (new ManageCert())->setPreservePfx()->fromPfx($certificat, $password);
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
         try {
            $signatura = ValidatePdfSignature::from($file);
            return $signatura->data; // Tot OK: retornem info de la signatura
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'The file is unsigned or the signature is not compatible with the PKCS7 type')) {
                // Cas normal: el PDF no està signat o no és un PKCS7 que entenem
                return null;
            }

            // Altres errors: sí que interessa saber-los
            throw $e;
        }
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
        $sig = ValidatePdfSignature::from($file);

        // 3) Busca el DNI en diversos camps del subjecte
        $fieldsToCheck = [
            'CN', 'serialNumber', '2.5.4.5', 'OID.2.5.4.5', 'subject', 'subjectRaw'
        ];

        foreach ($fieldsToCheck as $key) {
            if (!empty($sig->data[$key])) {
                $val = is_array($sig->data[$key]) ? implode(' ', $sig->data[$key]) : $sig->data[$key];
                if (str_contains(strtoupper($val), $dni)) {
                    return true;
                }
            }
        }

        // 4) Busca també dins del DN complet si el parser l’exposa
        if (!empty($sig->data['DN'])) {
            if (str_contains(strtoupper($sig->data['DN']), $dni)) {
                return true;
            }
        }

        return false;
    }


    public static function sign($file, $newFile, $coordx, $coordy, $certPath, $certPassword): void
    {
        (new self())->signDocument($file, $newFile, $coordx, $coordy, $certPath, $certPassword);
    }

    /*

    public function signDocument($file, $newFile, $coordx, $coordy, $cert): void
    {
        try {
            $user = $cert->getCert()->data['subject']['commonName'];
            $imagePath = storage_path('tmp/' . Str::orderedUuid() . '.png');

            File::put($imagePath, (new SignImage())->generateFromCert($cert, SealImage::FONT_SIZE_LARGE, false, 'd/m/Y'));

            // 1) Escriu en temporal, no in place
            $info = pathinfo($newFile);
            $tmp = $info['dirname'].'/'.$info['filename'].'_tmp.'.$info['extension'];

            $pdf = new SignaturePdf($file, $cert, SignaturePdf::MODE_RESOURCE);
            $coordx = (float) ($coordx ?? 50);
            $coordy = (float) ($coordy ?? 50);
            $signed = $pdf->setImage($imagePath, $coordx, $coordy)->signature();

            if (!$signed || strlen($signed) < 1000) {
                throw new IntranetException("La llibreria no ha generat una signatura vàlida.");
            }

            file_put_contents($tmp, $signed);

            // 2) Valida amb try/catch per traure el missatge original
            
            
            try {
                if (!$this->validateUserSignature($tmp)) {
                    throw new IntranetException("Persona que signa diferent al certificat");
                }
            } catch (\Throwable $e) {
                // Ací veuràs “The file is unsigned or the signature is not compatible with the PKCS7 type”
                Log::channel('certificate')->error('Validació de signatura fallida', [
                    'error' => $e->getMessage(),
                    'pdfPath' => $tmp,
                ]);
                throw $e instanceof IntranetException ? $e : new IntranetException($e->getMessage());
            }
               

            // 3) Si tot bé, substitueix l’original
            rename($tmp, $newFile);

            Log::channel('certificate')->info("S'ha signat el document amb el certificat.", [
                'signUser' => $user,
                'intranetUser' => authUser()->fullName,
                'pdfPath' => $file,
                'signedPdfPath' => $newFile,
                'imagePath' => $imagePath,
                'coordx' => $coordx,
                'coordy' => $coordy,
            ]);
        } catch (Throwable $th) {
            Log::channel('certificate')->alert("Error al signar el document.", [
                'intranetUser' => authUser()->fullName,
                'pdfPath' => $file,
                'error' => $th->getMessage(),
            ]);
            throw new IntranetException("Error al signar el document.: " . $th->getMessage());
        }
    }
        */

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
        $page = (int) ($config['page'] ?? 1);
        $append = (bool) ($config['append'] ?? true);
        $timeout = (int) ($config['timeout'] ?? 60);

        $outputDir = storage_path('tmp/jsignpdf_' . Str::orderedUuid());
        File::ensureDirectoryExists($outputDir);

        try {
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
                $append
            );

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
        bool $append
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
            '-d', $outputDir,
        ]);

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
