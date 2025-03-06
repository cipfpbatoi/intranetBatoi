<?php

namespace Intranet\Services;

use Illuminate\Support\Fluent;
use LSNepomuceno\LaravelA1PdfSign\Sign\ManageCert;
use LSNepomuceno\LaravelA1PdfSign\Sign\SealImage;
use Carbon\Carbon;


class SignImageService
{
    private SealImage $sealImage;

    public function __construct(SealImage $sealImage)
    {
        $this->sealImage = $sealImage;
    }

    public function generateFromCert(
        ManageCert $cert,
        string $fontSize = SealImage::FONT_SIZE_LARGE,
        bool   $showDueDate = false,
        string $dueDateFormat = 'd/m/Y'
    ): string {
        $subject = new Fluent($cert->getCert()->data['subject']);
        $issuer  = new Fluent($cert->getCert()->data['issuer']);
        $texts   = $this->formatCertificateText($subject, $issuer);

        $certDueDate = $showDueDate
            ? Carbon::createFromTimestamp($cert->getCert()->data['validTo_time_t'])->format($dueDateFormat)
            : now()->format($dueDateFormat);

        return $this->sealImage
            ->setImagePath()
            ->addTextField($texts['firstLine'], 160, 80)
            ->addTextField($texts['secondLine'], 160, 150)
            ->addTextField($certDueDate, 160, 250)
            ->generateImage();
    }

    private function formatCertificateText(Fluent $subject, Fluent $issuer): array
    {
        return [
            'firstLine'  => 'Signat per ' . ($subject->commonName ?? $subject->organizationName),
            'secondLine' => 'Certificat emés per ' . ($issuer->organizationalUnitName ?? $issuer->commonName ?? $issuer->organizationName),
        ];
    }
}