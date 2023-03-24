<?php

namespace Intranet\Services;

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



    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function sign(
        $file,
        $newFile,
        $x=50,
        $y=240,
        $certificat='Signatures/Certificats_Ignasi.pfx',
        $password='EICLMP5_a'
    )
    {
        try {
            $cert = self::readCertificat($certificat, $password);
            $image = SealImage::fromCert($cert);
            $imagePath = a1TempDir(true, '.png');
            File::put($imagePath, $image);

            $pdf = new SignaturePdf(
                $file,
                $cert,
                SignaturePdf::MODE_RESOURCE
            );

            $signed_pdf_content = $pdf->setImage($imagePath, $x, $y)
                ->setInfo(
                    'Ignasi Gomis Mullor',
                    'CIP FP Batoi'
                )
                ->signature();
            file_put_contents($newFile, $signed_pdf_content);
        } catch (\Throwable $th) {
            Alert::danger($th->getMessage());
        }
    }
}
