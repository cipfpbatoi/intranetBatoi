<?php

namespace Intranet\Componentes;

use Illuminate\Support\Fluent;
use LSNepomuceno\LaravelA1PdfSign\ManageCert;
use LSNepomuceno\LaravelA1PdfSign\SealImage;

class signImage extends SealImage
{
    public static function fromCert(
        ManageCert $cert,
        string $fontSize = self::FONT_SIZE_LARGE,
        bool   $showDueDate = false,
        string $dueDateFormat = 'd/m/Y H:i:s'
    ): string {
        $subject    = new Fluent($cert->getCert()->data['subject']);
        $firstLine  = 'Signat per '. $subject->commonName ?? $subject->organizationName;
        $issuer     = new Fluent($cert->getCert()->data['issuer']);
        $secondLine = 'Certificat emés per '.
                $issuer->organizationalUnitName ?? $issuer->commonName ?? $issuer->organizationName;
        $certDueDate = $showDueDate
            ? now()->createFromTimestamp(
                $cert->getCert()->data['validTo_time_t']
            )->format($dueDateFormat)
            : date($dueDateFormat);
        $callback = function ($font) use ($fontSize) {
            $font->file(__DIR__ . '/Resources/font/Roboto-Medium.ttf');
            $font->size(
                $fontSize === self::FONT_SIZE_SMALL ? 15
                    : ($fontSize === self::FONT_SIZE_MEDIUM ? 20 : 25)
            );
            $font->color('#16A085');
        };
        $selfObj = new static;
        return $selfObj
            ->setImagePath()
            ->addTextField(
                $selfObj->breakText($firstLine ?? $secondLine ?? '', $fontSize),
                160,
                80,
                $callback
            )
            ->addTextField(
                $selfObj->breakText($firstLine ? $secondLine : '', $fontSize),
                160,
                150,
                $callback
            )
            ->addTextField($certDueDate ?? '', 160, 250, $callback)
            ->generateImage();
    }

    private function breakText(string $text, string $fontSize = self::FONT_SIZE_LARGE): string
    {
        $cropSize = $fontSize === self::FONT_SIZE_SMALL ? 60
            : ($fontSize === self::FONT_SIZE_MEDIUM ? 48 : 35);
        $this->previousTextBreakLine = strlen($text) >= $cropSize;
        if ($this->previousTextBreakLine) {
            $textSplit = str_split($text, ($cropSize - 3));
            $textSplit = array_map('trim', $textSplit);
            $text = join(PHP_EOL, $textSplit);
        }
        return $text;
    }
}
