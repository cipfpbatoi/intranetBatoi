<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Gestiona el bloqueig temporal d'accés a les convalidacions de l'alumnat. */
class ConvalidacioAccessService
{
    private const COLLECTION = 'convalidacions';
    private const KEY = 'acces_alumnat_bloquejat';
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

    /** Retorna el testimoni del cicle actual de bloqueig. */
    private function generation(): string
    {
        return substr($this->state(), 2);
    }
}
