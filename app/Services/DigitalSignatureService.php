<?php

namespace Intranet\Services;

use Illuminate\Encryption\Encrypter;
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

    public static function cryptCertificate($certificat, $fileName, $password)
    {
        $file = self::getFileNameCrypt($fileName);
        $encrypter = self::getEncrypter($password);
        $content = $encrypter->encryptString(base64_encode(file_get_contents($certificat)));
        file_put_contents($file, $content);
    }



    public static function decryptCertificate($fileName, $password)
    {
        $cryptfile = self::getFileNameCrypt($fileName);
        $decryptfile = self::getFileNameDeCrypt($fileName);
        $encrypter = self::getEncrypter($password);
        $fileContent = file_get_contents($cryptfile);
        $cert = base64_decode($encrypter->decryptString($fileContent));
        File::put($decryptfile, $cert);
        return $decryptfile;
    }



    public static function getFileNameCrypt($fileName)
    {
       return  storage_path('app/certificats/'.$fileName.'.tmp');
    }

    public static function getFileNameDeCrypt($fileName)
    {
        return storage_path('app/certificats/'.$fileName.'.pfx');
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
        $passCert
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

    /**
     * @param $password
     * @return Encrypter
     */
    private static function getEncrypter($password): Encrypter
    {
        $customKey = substr('base64:'.$password.config('app.key'), 0, 32);
        return new Encrypter($customKey, config('app.cipher'));
    }
}
