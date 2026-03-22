<?php

declare(strict_types=1);

namespace Intranet\Application\Import\Concerns;

use Illuminate\Support\Str;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Alumno;
use Intranet\Services\UI\AppAlert as Alert;

trait SharedImportFieldTransformers
{
    /**
     * @var array<int, int>|null
     */
    private ?array $availableProfesorCodes = null;

    private function emailConselleriaImport($nombre, $apellido1, $apellido2)
    {
        return emailConselleria($nombre, $apellido1, $apellido2);
    }

    private function emailProfesorImport($nombre, $apellido)
    {
        return strtolower(substr($nombre, 0, 1) . $apellido . '@' . config('contacto.host.dominio'));
    }

    private function aleatorio($length = 60): string
    {
        $intLength = (int) $length;
        if ($intLength <= 0) {
            $intLength = 60;
        }

        return Str::random($intLength);
    }

    public function hazDNI(string $dni, int $nia)
    {
        $byNia = Alumno::find($nia);

        if (strlen($dni) <= 8) {
            return $byNia ? $byNia->dni : 'F' . Str::random(9);
        }

        if ($byNia && $byNia->dni !== $dni) {
            Alumno::where('dni', $dni)->where('nia', '<>', $nia)->delete();
            Alert::warning('Alumne amb DNI ' . $dni . ' esborrat per duplicat de nia ' . $nia);
        }

        return $dni;
    }

    /**
     * @return string|null
     */
    private function getFechaFormatoIngles($fecha)
    {
        $fecha = str_replace('/', '-', $fecha);
        $fecha2 = date_create_from_format('j-m-Y', $fecha);
        if (!$fecha2) {
            return null;
        }

        return $fecha2->format('Y-m-d');
    }

    private function cifrar($cadena)
    {
        return bcrypt(trim($cadena));
    }

    private function digitos($telefono)
    {
        return substr($telefono, 0, 9);
    }

    private function hazDomicilio($tipo_via, $domicilio, $numero, $puerta, $escalera, $letra, $piso)
    {
        $tipo_via = ($tipo_via == null) ? '' : trim($tipo_via);
        $domicilio = ($domicilio == null) ? '' : trim($domicilio);
        $numero = ($numero == null) ? '' : trim($numero);
        $puerta = ($puerta == null) ? '' : trim($puerta);
        $escalera = ($escalera == null) ? '' : trim($escalera);
        $letra = ($letra == null) ? '' : trim($letra);
        $piso = ($piso == null) ? '' : trim($piso);

        $domic = $tipo_via . ' ' . $domicilio . ', ' . $numero;
        if ($puerta != '') {
            $domic .= ' pta.' . $puerta;
        }
        if ($escalera != '') {
            $domic .= ' esc.' . $escalera;
        }
        if ($piso != '') {
            $domic .= ' ' . $piso . 'º';
        }
        if ($letra != '') {
            $domic .= '-' . $letra;
        }

        return $domic;
    }

    private function creaCodigoProfesor($unused = null): int
    {
        if ($this->availableProfesorCodes === null) {
            $min = 1050;
            $max = 9000;

            $usedCodes = app(ProfesorService::class)->usedCodigosBetween($min, $max);
            $available = array_flip(range($min, $max));
            foreach ($usedCodes as $usedCode) {
                unset($available[(int) $usedCode]);
            }

            $this->availableProfesorCodes = array_keys($available);
        }

        if ($this->availableProfesorCodes === []) {
            throw new \RuntimeException('No hi ha codis de professor disponibles en el rang 1050-9000.');
        }

        $index = array_rand($this->availableProfesorCodes);
        $code = (int) $this->availableProfesorCodes[$index];
        unset($this->availableProfesorCodes[$index]);

        return $code;
    }

    private function crea_codigo_profesor($unused = null): int
    {
        return $this->creaCodigoProfesor($unused);
    }
}
