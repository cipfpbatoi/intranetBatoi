<?php

declare(strict_types=1);

namespace Intranet\Application\Grupo;

use Intranet\Entities\Alumno;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Grupo;
use Intranet\Jobs\SendEmail;
use Intranet\Services\School\SecretariaService;

class GrupoWorkflowService
{
    public function assignMissingCiclo(): int
    {
        $updated = 0;
        foreach (Grupo::all() as $grupo) {
            if ($grupo->ciclo == '') {
                $ciclo = Ciclo::select('id')
                    ->where('codigo', '=', substr($grupo->codigo, 1, 4))
                    ->first();

                if ($ciclo) {
                    $grupo->idCiclo = $ciclo->id;
                    $grupo->save();
                    $updated++;
                }
            }
        }

        return $updated;
    }

    public function selectedStudentsPlainText(array $payload): string
    {
        $ids = [];
        foreach ($payload as $nia => $value) {
            if ($value === 'on') {
                $ids[] = $nia;
            }
        }

        $alumnos = Alumno::whereIn('nia', $ids)->get()->sortBy('nameFull');
        $alumnes = hazArray($alumnos, 'nameFull');

        return implode('; ', $alumnes);
    }

    /**
     * @return array{sent:int,errors:array<int,string>}
     */
    public function sendFolCertificates(Grupo $grupo, callable $pdfSaver): array
    {
        $errors = [];
        $count = 0;

        $sService = new SecretariaService();
        $datos['ciclo'] = $grupo->Ciclo;
        $remitente = ['email' => cargo('secretario')->email, 'nombre' => cargo('secretario')->FullName];

        foreach ($grupo->Alumnos as $alumno) {
            if ($alumno->fol != 1) {
                continue;
            }

            try {
                $id = $alumno->nia;
                $tmp = storage_path("tmp/fol_$id.pdf");
                if (file_exists($tmp)) {
                    unlink($tmp);
                }

                $pdfSaver($alumno, $datos, $tmp);

                $attach = ["tmp/fol_$id.pdf" => 'application/pdf'];
                $document = [
                    'title' => 15,
                    'dni' => $alumno->dni,
                    'alumne' => trim($alumno->shortName),
                    'route' => "tmp/fol_$id.pdf",
                    'name' => "fol_$id.pdf",
                    'size' => filesize($tmp),
                ];

                $sService->uploadFile($document);
                dispatch(new SendEmail($alumno->email, $remitente, 'email.fol', $alumno, $attach));
                $count++;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        $grupo->fol = 2;
        $grupo->save();

        return [
            'sent' => $count,
            'errors' => $errors,
        ];
    }
}
