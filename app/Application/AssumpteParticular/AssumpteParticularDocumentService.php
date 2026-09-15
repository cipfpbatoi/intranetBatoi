<?php

declare(strict_types=1);

namespace Intranet\Application\AssumpteParticular;

use Illuminate\Support\Facades\Storage;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Profesor;
use setasign\Fpdi\Fpdi;
use Throwable;

/**
 * Emplena la sol·licitud oficial i hi incorpora les dues rúbriques gràfiques.
 */
class AssumpteParticularDocumentService
{
    private const TEMPLATE = 'fdf/assumptes_particulars.pdf';
    private const OUTPUT_DIRECTORY = 'assumptes-particulars/resolucions';

    public function __construct(
        private readonly RubricaAssumpteParticularService $rubriques
    ) {
    }

    /**
     * Genera una única pàgina autoritzada i retorna la ruta relativa en el disc local.
     */
    public function generarAutoritzada(
        AssumpteParticular $peticio,
        Profesor $directora
    ): string {
        $professor = $peticio->profesor()->firstOrFail();
        $rubricaProfessor = $this->rubriques->path($professor);
        $rubricaDirectora = $this->rubriques->path($directora, 'director o directora');
        $template = public_path(self::TEMPLATE);
        if (!is_file($template)) {
            throw new AssumpteParticularException('No s’ha trobat la plantilla oficial de la sol·licitud.');
        }

        $rutaRelativa = sprintf(
            '%s/assumpte-particular-%d-%s.pdf',
            self::OUTPUT_DIRECTORY,
            $peticio->getKey(),
            bin2hex(random_bytes(6))
        );
        Storage::disk('local')->makeDirectory(self::OUTPUT_DIRECTORY);
        $rutaAbsoluta = Storage::disk('local')->path($rutaRelativa);

        try {
            $this->crearPdf(
                $template,
                $rutaAbsoluta,
                $peticio,
                $professor,
                $directora,
                $rubricaProfessor,
                $rubricaDirectora
            );
        } catch (AssumpteParticularException $exception) {
            Storage::disk('local')->delete($rutaRelativa);
            throw $exception;
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($rutaRelativa);
            throw new AssumpteParticularException(
                'No s’ha pogut generar la sol·licitud firmada.',
                previous: $exception
            );
        }

        return $rutaRelativa;
    }

    /**
     * Compon la plantilla, els textos i les rúbriques en una sola pàgina A4.
     */
    private function crearPdf(
        string $template,
        string $output,
        AssumpteParticular $peticio,
        Profesor $professor,
        Profesor $directora,
        string $rubricaProfessor,
        string $rubricaDirectora
    ): void {
        $pdf = new Fpdi();
        $pdf->setSourceFile($template);
        $pagina = $pdf->importPage(1);
        $mida = $pdf->getTemplateSize($pagina);
        $pdf->AddPage($mida['orientation'], [$mida['width'], $mida['height']]);
        $pdf->useTemplate($pagina);
        $pdf->SetTextColor(0, 0, 0);

        $this->text($pdf, 36, 55, 48, (string) $professor->surNames);
        $this->text($pdf, 113, 55, 68, (string) $professor->nombre);
        $this->text($pdf, 55, 62, 125, (string) $professor->dni);
        $this->text($pdf, 35, 72, 145, (string) ($professor->domicilio ?? ''), 8);
        $this->text($pdf, 35, 83, 20, (string) ($professor->codigo_postal ?? ''));
        $this->text($pdf, 44, 88, 45, $this->telefon($professor));
        $this->text($pdf, 128, 101, 54, (string) ($professor->especialitat ?? ''), 8);
        $this->text($pdf, 35, 120, 145, (string) config('contacto.nombre'), 9);
        $this->text($pdf, 50, 136, 35, (string) config('contacto.poblacion'));
        $this->text($pdf, 130, 136, 45, (string) config('contacto.provincia'));
        $this->text($pdf, 65, 171, 70, $peticio->data_gaudi->format('d/m/Y'), 10);
        $this->text($pdf, 88, 176, 95, $this->diesConsumits($peticio), 9);

        $hui = now();
        $this->text($pdf, 42, 209, 37, (string) config('contacto.poblacion'), 9);
        $this->text($pdf, 84, 209, 10, $hui->format('d'), 9);
        $this->text($pdf, 101, 209, 45, $this->mes($hui->month), 9);
        $this->text($pdf, 158, 209, 18, $hui->format('Y'), 9);

        $pdf->Image($rubricaProfessor, 80, 222, 50, 16);
        $this->text($pdf, 126, 248, 58, 'AUTORITZAT / AUTORIZADO', 8, 'B', 'C');
        $pdf->Image($rubricaDirectora, 139, 251, 35, 14);
        $this->text($pdf, 116, 269, 78, (string) $directora->fullName, 7, '', 'C');

        $pdf->Output('F', $output);
        if (!is_file($output) || filesize($output) === 0) {
            throw new AssumpteParticularException('No s’ha pogut crear el PDF autoritzat.');
        }
    }

    /**
     * Escriu text compatible amb les fonts bàsiques de FPDF.
     */
    private function text(
        Fpdi $pdf,
        float $x,
        float $y,
        float $width,
        string $value,
        int $size = 9,
        string $style = '',
        string $align = 'L'
    ): void {
        $encoded = iconv('UTF-8', 'windows-1252//TRANSLIT', trim($value));
        $pdf->SetFont('Helvetica', $style, $size);
        $pdf->SetXY($x, $y);
        $pdf->Cell($width, 4, $encoded !== false ? $encoded : '', 0, 0, $align);
    }

    /**
     * Retorna el primer telèfon disponible del professor.
     */
    private function telefon(Profesor $professor): string
    {
        return (string) (filled($professor->movil1) ? $professor->movil1 : $professor->movil2);
    }

    /**
     * Resumeix els dies autoritzats anteriors del curs, separats per tipus.
     */
    private function diesConsumits(AssumpteParticular $peticio): string
    {
        $consumits = AssumpteParticular::query()
            ->where('idProfesor', $peticio->idProfesor)
            ->where('curs', $peticio->curs)
            ->where('estat', AssumpteParticular::ESTAT_AUTORITZADA)
            ->selectRaw('tipus, COUNT(*) as total')
            ->groupBy('tipus')
            ->pluck('total', 'tipus');

        return sprintf(
            'Lectius: %d · No lectius: %d',
            (int) ($consumits[AssumpteParticular::TIPUS_LECTIU] ?? 0),
            (int) ($consumits[AssumpteParticular::TIPUS_NO_LECTIU] ?? 0)
        );
    }

    /**
     * Retorna el mes en Valencià per a la data de firma.
     */
    private function mes(int $mes): string
    {
        return [
            1 => 'gener',
            2 => 'febrer',
            3 => 'març',
            4 => 'abril',
            5 => 'maig',
            6 => 'juny',
            7 => 'juliol',
            8 => 'agost',
            9 => 'setembre',
            10 => 'octubre',
            11 => 'novembre',
            12 => 'desembre',
        ][$mes];
    }
}
