<?php

namespace Intranet\Services;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use LSNepomuceno\LaravelA1PdfSign\ManageCert;
use LSNepomuceno\LaravelA1PdfSign\SealImage;
use LSNepomuceno\LaravelA1PdfSign\SignaturePdf;
use Styde\Html\Facades\Alert;

class DigitalSignatureService
{
    public static function readCertificat($certificat, $password):ManageCert
    {

        $cert = new ManageCert;
        $cert
            ->setPreservePfx()
            ->fromPfx($certificat, $password);
        return $cert;
    }

    public static function cryptCertificate($certificat, $file, $password)
    {
        $customKey = substr('base64:'.$password.config('app.key'), 0, 32);
        $encrypter = new Encrypter($customKey, config('app.cipher'));
        $content = $encrypter->encryptString(base64_encode(file_get_contents($certificat)));
        file_put_contents($file, $content);
    }


    public static function decryptCertificate($file, $certificat, $password)
    {
        $customKey = substr('base64:'.$password.config('app.key'), 0, 32);
        $encrypter = new Encrypter($customKey, config('app.cipher'));
        $fileContent = file_get_contents($file);
        $cert = base64_decode($encrypter->decryptString($fileContent));
        File::put($certificat, $cert);
    }

    public static function getFilesNameCertificate($profesor)
    {
       return [
           'crypt' => storage_path('app/certificats/'.$profesor->fileName.'.tmp'),
           'decrypt' => storage_path('app/certificats/'.$profesor->fileName.'.pfx')
       ];
    }



    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function sign(
        $file,
        $newFile,
        $coordx,
        $coordy,
        $filecrt,
        $passCert='EICLMP5_a'
    )
    {
        try {
            $cert = self::readCertificat($filecrt, $passCert);
            $image = SealImage::fromCert($cert, SealImage::FONT_SIZE_LARGE, true, 'd/m/Y');
            $imagePath = a1TempDir(true, '.png');
            File::put($imagePath, $image);

            $pdf = new SignaturePdf(
                $file,
                $cert,
                SignaturePdf::MODE_RESOURCE
            );

            $signed_pdf_content = $pdf->setImage($imagePath, $coordx, $coordy)
                ->signature();
            file_put_contents($newFile, $signed_pdf_content);
        } catch (\Throwable $th) {
            Alert::danger($th->getMessage().' '.$th->getLine().' '.$th->getFile());
        }
    }
}
