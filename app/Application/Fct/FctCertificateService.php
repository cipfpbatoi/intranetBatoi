<?php

declare(strict_types=1);

namespace Intranet\Application\Fct;

use Intranet\Services\Document\PdfService;

class FctCertificateService
{
    public function colaboradorCertificateData(): array
    {
        $secretario = cargo('secretario');
        $director = cargo('director');

        return [
            'date' => FechaString(hoy(), 'ca'),
            'fecha' => FechaString(hoy(), 'es'),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName,
        ];
    }

    public function streamColaboradorCertificate(mixed $fct)
    {
        return app(PdfService::class)->hazPdf(
            'pdf.fct.certificatColaborador',
            $fct,
            $this->colaboradorCertificateData()
        )->stream();
    }
}
