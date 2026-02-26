<?php

namespace Intranet\Console\Commands;

use ErrorException;
use Exception;
use Illuminate\Console\Command;
use Intranet\Entities\AlumnoFct;
use Intranet\Mail\CertificatAlumneFct;
use Intranet\Mail\CertificatInstructorFct;
use Illuminate\Support\Facades\Mail;
use Intranet\Mail\AvalFct;
use Intranet\Entities\Fct;

use Swift_RfcComplianceException;
use Swift_TransportException;

/**
 * Envia correus diaris de FCT a alumnat i instructors.
 */
class SendFctEmails extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fct:Daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email valoració FCT';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

            $alumnosPendientes = AlumnoFct::pendienteNotificar()->get();

            foreach ($alumnosPendientes as $alumno) {
                $fct = $alumno->Fct;

                try {
                    Mail::to($alumno->Alumno->email, $alumno->Alumno->fullName)
                        ->cc($alumno->Tutor->email)
                        ->send(new CertificatAlumneFct($alumno));
                    avisa($alumno->Tutor->dni,
                        'El correu amb el certificat de FCT de ' . $alumno->Alumno->fullName . " ha estat enviat a l'adreça " . $alumno->Alumno->email,
                        '#', 'Servidor de correu');
                    $alumno->correoAlumno = 1;
                    $alumno->save();
                } catch (Exception  $e) {
                    $mensaje = "Error : Enviant certificats a l'alumne:  ".$e->getMessage().".".
                        $alumno->Alumno->fullName.' al email '.
                        $alumno->Alumno->email;
                    avisa(config('avisos.errores'), $mensaje, '#', 'Servidor de correu');
                    if ($alumno->Tutor != null) {
                        avisa($alumno->Tutor->dni, $mensaje, '#', 'Servidor de correu');
                    }

                }

                if ($fct->correoInstructor == 0 && isset($fct->Instructor->email)) {
                    $this->correuInstructor($fct);
                }
            }
            $fcts = Fct::where('correoInstructor', 0)->get();
            foreach ($fcts as $fct) {
                $first = $fct->AlFct->first();
                if ( isset($fct->Instructor->email) && $first->correoAlumno) {
                    $this->correuInstructor($fct);
                }
            }
        }

    /**
     * @param $fct
     * @return array
     */
    private function correuInstructor($fct): int
    {
        try {
            $instructor = $fct->Instructor;
            $emailInstructor = $this->normalizeEmail($instructor?->email ?? null);
            if (!$emailInstructor) {
                $mensaje = 'Error : Enviant certificats al Instructor: '.
                    ($instructor->nombre ?? 'Sense instructor').' al email '.
                    ($instructor->email ?? '').': Email invàlid o buit.';
                avisa(config('avisos.errores'), $mensaje, '#', 'Servidor de correu');
                if ($fct->Encarregat != null) {
                    avisa($fct->Encarregat->dni, $mensaje, '#', 'Servidor de correu');
                }
                return 0;
            }

            $encarregat = $fct->Encarregat;

            if (!$encarregat && $fct->AlFct && $fct->AlFct->first()) {
                $encarregat = $fct->AlFct->first()->Tutor;
            }

            if (!$encarregat) {
                throw new \ErrorException('No hi ha tutor assignat a la FCT ni encarregat directe.');
            }
            Mail::to($emailInstructor, $instructor->nombre ?? '')
                ->cc($encarregat->email)
                ->send(new AvalFct($fct, 'instructor'));
            Mail::to($emailInstructor, $instructor->nombre ?? '')
                ->cc($encarregat->email)
                ->send(new CertificatInstructorFct($fct, $encarregat));
            avisa($encarregat->dni,
                'El correu amb el certificat de FCT de '.$instructor->nombre." ha estat enviat a l'adreça ".$emailInstructor,
                '#', 'Servidor de correu');
            $fct->correoInstructor = 1;
            $fct->save();
            return 1;
        } catch (\Exception $e) {
            $mensaje = 'Error : Enviant certificats al Instructor: '.
                $fct->Instructor->nombre.' al email '.
                $fct->Instructor->email.':'.$e->getMessage();
            avisa(config('avisos.errores'), $mensaje, '#', 'Servidor de correu');
            if ($fct->Encarregat != null) {
                avisa($fct->Encarregat->dni, $mensaje, '#', 'Servidor de correu');
            }
            return 0;
        }
    }

    /**
     * Normalitza i valida un email.
     *
     * @param string|null $email
     * @return string|null
     */
    private function normalizeEmail(?string $email): ?string
    {
        $email = is_string($email) ? trim($email) : null;
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $email;
    }


}
