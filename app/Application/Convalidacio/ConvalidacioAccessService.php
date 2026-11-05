<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intranet\Entities\Alumno;

/** Gestiona el bloqueig temporal d'accés a les convalidacions de l'alumnat. */
class ConvalidacioAccessService
{
    private const COLLECTION = 'convalidacions';
    private const KEY = 'acces_alumnat_bloquejat';
    private const KEY_NEW_REQUESTS_ENABLED = 'noves_sollicituds_permeses';
    private const KEY_JULY_ENROLMENTS_BLOCKED = 'noves_sollicituds_juliol_bloquejades';
    private const SESSION_KEY = 'convalidacions_access_generation';

    /** Indica si Direcció ha bloquejat l'accés de l'alumnat. */
    public function isBlocked(): bool
    {
        return str_starts_with((string) $this->state(), '1:');
    }

    /** Actualitza el bloqueig i invalida els accessos de prova anteriors. */
    public function setBlocked(bool $blocked): void
    {
        DB::table('settings')->updateOrInsert(
            ['collection' => self::COLLECTION, 'key' => self::KEY],
            ['value' => ($blocked ? '1:' : '0:') . Str::uuid()]
        );
    }

    /** Indica si Direcció permet presentar noves sol·licituds a l'alumnat. */
    public function newRequestsEnabled(): bool
    {
        return $this->booleanSetting(self::KEY_NEW_REQUESTS_ENABLED, true);
    }

    /** Indica si Direcció bloqueja les noves sol·licituds de matrícules de juliol. */
    public function julyEnrolmentsBlocked(): bool
    {
        return $this->booleanSetting(self::KEY_JULY_ENROLMENTS_BLOCKED, false);
    }

    /** Guarda els controls d'obertura de noves sol·licituds. */
    public function setNewRequestControls(bool $enabled, bool $julyEnrolmentsBlocked): void
    {
        $this->setBooleanSetting(self::KEY_NEW_REQUESTS_ENABLED, $enabled);
        $this->setBooleanSetting(self::KEY_JULY_ENROLMENTS_BLOCKED, $julyEnrolmentsBlocked);
    }

    /** Retorna el missatge de bloqueig de noves sol·licituds o null si l'alumne pot presentar-ne. */
    public function newRequestBlockMessage(Alumno $alumno): ?string
    {
        if (!$this->newRequestsEnabled()) {
            return 'Les noves sol·licituds de convalidació no estan obertes en este moment.';
        }

        if ($this->julyEnrolmentsBlocked() && substr((string) $alumno->fecha_matricula, 5, 2) === '07') {
            return 'No pots presentar una nova sol·licitud de convalidació perquè la teua matrícula és de juliol.';
        }

        return null;
    }

    /** Comprova la contrasenya sense exposar-la al navegador. */
    public function verifyPassword(string $password): bool
    {
        return hash_equals((string) config('convalidacions_access.password'), $password);
    }

    /** Guarda en sessió l'accés temporal associat a la generació actual del bloqueig. */
    public function unlockSession(): void
    {
        session([self::SESSION_KEY => $this->generation()]);
    }

    /** Comprova que la sessió s'haja desbloquejat en el cicle actual. */
    public function sessionIsUnlocked(): bool
    {
        $generation = $this->generation();
        $sessionGeneration = (string) session(self::SESSION_KEY, '');

        return $this->isBlocked()
            && $generation !== ''
            && $sessionGeneration !== ''
            && hash_equals($generation, $sessionGeneration);
    }

    /** Retorna el valor persistent del control obert per defecte. */
    private function state(): string
    {
        return (string) (DB::table('settings')
            ->where('collection', self::COLLECTION)
            ->where('key', self::KEY)
            ->value('value') ?? '0:');
    }

    /** Retorna un booleà persistent, amb el valor predeterminat quan encara no s'ha configurat. */
    private function booleanSetting(string $key, bool $default): bool
    {
        $value = DB::table('settings')
            ->where('collection', self::COLLECTION)
            ->where('key', $key)
            ->value('value');

        return $value === null ? $default : $value === '1';
    }

    /** Guarda un valor booleà persistent dels controls de convalidacions. */
    private function setBooleanSetting(string $key, bool $value): void
    {
        DB::table('settings')->updateOrInsert(
            ['collection' => self::COLLECTION, 'key' => $key],
            ['value' => $value ? '1' : '0']
        );
    }

    /** Retorna el testimoni del cicle actual de bloqueig. */
    private function generation(): string
    {
        return substr($this->state(), 2);
    }
}
