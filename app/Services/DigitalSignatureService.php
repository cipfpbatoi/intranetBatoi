<?php

namespace Intranet\Services;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intranet\Componentes\SignImage;
use Intranet\Exceptions\CertException;
use Intranet\Exceptions\IntranetException;
use LSNepomuceno\LaravelA1PdfSign\Sign\ManageCert;
use LSNepomuceno\LaravelA1PdfSign\Sign\SealImage;
use LSNepomuceno\LaravelA1PdfSign\Sign\SignaturePdf;
use LSNepomuceno\LaravelA1PdfSign\Sign\ValidatePdfSignature;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
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
            throw new CertException("Password del certificat incorrecte");
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

    public static function validateUserSign($file, $dni = null): bool
    {
        return (new self())->validateUserSignature($file, $dni);
    }

    public function validateUserSignature($file, $dni = null): bool
    {
        if (is_null($dni)) {
            $dni = substr(authUser()->dni, 1);
        }
        $signatura = ValidatePdfSignature::from($file);
        return isset($signatura->data['CN'][0]) && str_contains($signatura->data['CN'][0], $dni);
    }

    public static function sign($file, $newFile, $coordx, $coordy, $cert): void
    {
        (new self())->signDocument($file, $newFile, $coordx, $coordy, $cert);
    }

    public function signDocument($file, $newFile, $coordx, $coordy, $cert): void
    {
        try {
            $user = $cert->getCert()->data['subject']['commonName'];
            $imagePath = storage_path('tmp/' . Str::orderedUuid() . '.png');

            File::put($imagePath, (new SignImage())->generateFromCert($cert, SealImage::FONT_SIZE_LARGE, false, 'd/m/Y'));

            $pdf = new SignaturePdf($file, $cert, SignaturePdf::MODE_RESOURCE);
            $signed_pdf_content = $pdf->setImage($imagePath, $coordx, $coordy)->signature();
            file_put_contents($newFile, $signed_pdf_content);

            if (!$this->validateUserSignature($newFile)) {
                throw new IntranetException("Persona que signa diferent al certificat");
            }

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
