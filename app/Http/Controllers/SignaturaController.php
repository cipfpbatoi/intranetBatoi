<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\File;
use LSNepomuceno\LaravelA1PdfSign\ManageCert;
use LSNepomuceno\LaravelA1PdfSign\SealImage;
use LSNepomuceno\LaravelA1PdfSign\SignaturePdf;


class SignaturaController extends Controller
{

    public function readCertificat():ManageCert
    {

        $cert = new ManageCert;
        $cert
            ->setPreservePfx()
            ->fromPfx('Signatures/Certificats_Ignasi.pfx', 'EICLMP5_a');
        return $cert;
    }



/**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke()
    {
        try {
            $cert = $this->readCertificat();
            $image = SealImage::fromCert($cert);
            $imagePath = a1TempDir(true, '.png');
            File::put($imagePath, $image);

            $pdf = new SignaturePdf(
                'Signatures/A1_ibermatic.pdf',
                $cert,
                SignaturePdf::MODE_DOWNLOAD);
            return $pdf
                ->setImage($imagePath,50,240)
                ->setFileName('A2.pdf')
                ->setInfo(
                    'Ignasi Gomis Mullo',
                    'CIP FP Batoi'
                )
                ->signature();
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }
}
