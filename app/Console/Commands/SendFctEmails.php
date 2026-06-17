<?php

namespace Intranet\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Intranet\Mail\CertificatInstructorFct;
use Illuminate\Support\Facades\Mail;
use Intranet\Mail\AvalFct;
use Intranet\Entities\Fct;

/**
 * Envia correus diaris de certificació de FCT a instructors.
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
        $fcts = Fct::where('correoInstructor', 0)->get();
        foreach ($fcts as $fct) {
            if ($this->shouldSendInstructorCertificate($fct)) {
                $this->correuInstructor($fct);
            }
        }
        return Command::SUCCESS;
    }

    /**
     * Indica si la FCT compartida ja està preparada per enviar el certificat a l'instructor.
     *
     * @param Fct $fct
     * @return bool
     */
    private function shouldSendInstructorCertificate(Fct $fct): bool
    {
        if ((int) $fct->correoInstructor !== 0 || !isset($fct->Instructor->email)) {
            return false;
        }

        $alumnos = $fct->AlFct()->get(['calificacion']);
        if ($alumnos->isEmpty()) {
            return false;
        }

        $hasApprovedStudent = false;

        foreach ($alumnos as $alumno) {
            if ($alumno->calificacion === null) {
                return false;
            }

            if ((int) $alumno->calificacion === 1) {
                $hasApprovedStudent = true;
            }
        }

        return $hasApprovedStudent;
    }

    /**
     * Envia a l'instructor l'avaluació i el certificat de les hores de FCT.
     *
     * @param Fct $fct
     * @return int
     */
    private function correuInstructor(Fct $fct): int
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
            report($e);
            Log::error('Error enviant certificats al instructor', [
                'fct_id' => $fct->id ?? null,
                'instructor_dni' => $fct->Instructor->dni ?? null,
                'error' => $e->getMessage(),
            ]);
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
