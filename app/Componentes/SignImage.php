<?php
namespace Intranet\Componentes;

use Illuminate\Support\Fluent;
use LSNepomuceno\LaravelA1PdfSign\Sign\ManageCert;
use LSNepomuceno\LaravelA1PdfSign\Sign\SealImage;

class SignImage extends SealImage
{

    private static array $fontSizes = [
        self::FONT_SIZE_SMALL => 15,
        self::FONT_SIZE_MEDIUM => 20,
        self::FONT_SIZE_LARGE => 25,
    ];

    private static array $cropSizes = [
        self::FONT_SIZE_SMALL => 60,
        self::FONT_SIZE_MEDIUM => 48,
        self::FONT_SIZE_LARGE => 35,
    ];


    public function generateFromCert(
        ManageCert $cert,
        string $fontSize,
        bool $showDueDate,
        string $dueDateFormat
    ): string {
        $subject = new Fluent($cert->getCert()->data['subject']);
        $issuer = new Fluent($cert->getCert()->data['issuer']);

        $firstLine = 'Signat per ' . ($subject->commonName ?? $subject->organizationName ?? 'Desconegut');
        $secondLine = 'Certificat emés per ' . ($issuer->organizationalUnitName ?? $issuer->commonName ?? $issuer->organizationName ?? 'Desconegut');

        $certDueDate = $showDueDate
            ? now()->createFromTimestamp($cert->getCert()->data['validTo_time_t'])->format($dueDateFormat)
            : date($dueDateFormat);

        $callback = $this->getFontConfig($fontSize);

        return $this
            ->setImagePath()
            ->addTextField($this->breakText($firstLine, $fontSize), 160, 80, $callback)
            ->addTextField($this->breakText($secondLine, $fontSize), 160, 150, $callback)
            ->addTextField($certDueDate, 160, 250, $callback)
            ->generateImage();
    }

    private function getFontConfig(string $fontSize): callable
    {
        return function ($font) use ($fontSize) {
            $font->file(__DIR__ . '/Resources/font/Roboto-Medium.ttf');
            $font->size(self::$fontSizes[$fontSize] ?? self::$fontSizes[self::FONT_SIZE_LARGE]);
            $font->color('#16A085');
        };
    }

    private function breakText(string $text, string $fontSize = self::FONT_SIZE_LARGE): string
    {
        $cropSize = self::$cropSizes[$fontSize] ?? self::$cropSizes[self::FONT_SIZE_LARGE];

        return (strlen($text) >= $cropSize)
            ? implode(PHP_EOL, array_map('trim', str_split($text, $cropSize - 3)))
            : $text;
    }
}
