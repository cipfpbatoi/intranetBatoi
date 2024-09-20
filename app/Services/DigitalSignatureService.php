<?php

namespace Intranet\Services;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intranet\Componentes\signImage;
use Intranet\Exceptions\CertException;
use Intranet\Exceptions\IntranetException;
use LSNepomuceno\LaravelA1PdfSign\Sign\ManageCert;
use LSNepomuceno\LaravelA1PdfSign\Sign\SealImage;
use LSNepomuceno\LaravelA1PdfSign\Sign\SignaturePdf;
use LSNepomuceno\LaravelA1PdfSign\Sign\ValidatePdfSignature;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DigitalSignatureService
{

    public static function readCertificat($certificat, $password):ManageCert
    {
        try {
            $cert = new ManageCert;
            $cert->setPreservePfx()->fromPfx($certificat, $password);
            return $cert;
        } catch (\Throwable $th) {
            throw new CertException("Password del certificat incorrecte");
        }
    }

    public static function cryptCertificate($certificat, $fileName, $password)
    {
        $file = self::getFileNameCrypt($fileName);
        $encrypter = self::getEncrypter($password);
        $content = $encrypter->encryptString(base64_encode(file_get_contents($certificat)));
        file_put_contents($file, $content);
        Log::channel('certificate')->info("S'ha pujat el certificat d'usuari.", [
            'intranetUser' => authUser()->fullName,
            'Path' => $file,
        ]);
    }

    public static function deleteCertificate($user)
    {
        unlink($user->pathCertificate);
        Log::channel('certificate')->info("S'ha esborrat el certificat d'usuari.", [
            'intranetUser' => authUser()->fullName,
        ]);
    }


    public static function decryptCertificateUser($decrypt, $user)
    {
        try {
            if (Hash::check($decrypt, $user->password)) {
                $nameFile = $user->fileName;
                return DigitalSignatureService::decryptCertificate($nameFile, $decrypt);
            }
        } catch (\Throwable $th) {
            throw new CertException("Password de la Intranet incorrecte");
        }
    }


    public static function decryptCertificate($fileName, $password)
    {
        $cryptfile = self::getFileNameCrypt($fileName);
        $decryptfile = self::getFileNameDeCrypt($fileName);
        $encrypter = self::getEncrypter($password);
        $fileContent = file_get_contents($cryptfile);
        $cert = base64_decode($encrypter->decryptString($fileContent));
        File::put($decryptfile, $cert);
        Log::channel('certificate')->info("S'ha desxifrat el certificat d'usuari.", [
            'intranetUser' => authUser()->fullName,
        ]);
        return $decryptfile;
    }



    public static function getFileNameCrypt($fileName)
    {
       return  storage_path('app/zip/'.$fileName.'.tmp');
    }

    public static function getFileNameDeCrypt($fileName)
    {
        return storage_path('tmp/'.$fileName.'.pfx');
    }

    public static function signCrypt(
        $file,
        $newFile,
        $coordx,
        $coordy,
        $passCrypt,
        $passCert
    ){
        $nomFitxer = storage_path('tmp/'.authUser()->fileName.'.pfx');
        DigitalSignatureService::decryptCertificateUser($passCrypt, authUser());
        $cert = DigitalSignatureService::readCertificat($nomFitxer, $passCert);
        self::sign($file, $newFile, $coordx, $coordy, $cert);
        if (file_exists($nomFitxer)){
            unlink($nomFitxer);
        }
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
        $cert,
    )
    {
        try {
            $user = $cert->getCert()->data['subject']['commonName'];
            $image = signImage::fromCert($cert, SealImage::FONT_SIZE_LARGE, false, 'd/m/Y');

            $imagePath = storage_path('tmp/'.Str::orderedUuid() . '.png');
            File::put($imagePath, $image);

            $pdf = new SignaturePdf(
                $file,
                $cert,
                SignaturePdf::MODE_RESOURCE
            );


            Log::channel('certificate')->info('Image path:', ['imagePath' => $imagePath, 'imageExists' => file_exists($imagePath)]);

            $signed_pdf_content = $pdf->setImage($imagePath, $coordx, $coordy)->signature();
            file_put_contents($newFile, $signed_pdf_content);
            if (self::validateUserSign($newFile)){

                Log::channel('certificate')->info("S'ha signat el document amb el certificat.", [
                    'signUser' => $user,
                    'intranetUser' => authUser()->fullName,
                    'pdfPath' => $file,
                    'signedPdfPath' => $newFile,
                    'imagePath' => $imagePath,
                    'coordx' => $coordx,
                    'coordy' => $coordy,
                ]);
            } else {
                Log::channel('certificate')->alert("Persona que signa diferent al certificat", [
                    'intranetUser' => authUser()->fullName,
                    'pdfPath' => $file,
                ]);
                throw new IntranetException("Persona que signa diferent al certificat");
            }
        } catch (\Throwable $th) {
            Log::channel('certificate')->alert("Password certificat incorrecte", [
                'intranetUser' => authUser()->fullName,
                'pdfPath' => $file,
            ]);
            throw new IntranetException("Error al signar el document.: ".$th->getMessage());
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

    public static function validate($file){
        $signatura = ValidatePdfSignature::from($file);
        return $signatura->data;
    }

    public static function validateUserSign($file,$dni=null){
        if (is_null($dni)){
            $dni = substr(authUser()->dni,1);
        }
        $signatura = ValidatePdfSignature::from($file);
        if (isset($signatura->data['CN'][0])) {
            return str_contains($signatura->data['CN'][0],$dni);
        }
        return true;    
    }


}
