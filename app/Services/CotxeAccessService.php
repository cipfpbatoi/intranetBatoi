<?php

namespace Intranet\Services;



use Intranet\Entities\CotxeAcces;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CotxeAccessService
{
    /**
     * Determina si un cotxe està dins del pàrquing
     */
    public function estaDins(string $matricula): bool
    {
        $últims = CotxeAcces::where('matricula', $matricula)
            ->where('autoritzat', true)
            ->orderByDesc('data')
            ->take(2)
            ->get();

        if ($últims->isEmpty()) return false;

        // Si l'últim accés és entrada (porta oberta) i no hi ha una sortida posterior
        return $últims->first()->porta_oberta;
    }

    /**
     * Retorna els segons que han passat des del darrer accés
     */
    public function segonsDesdeUltimAcces(string $matricula): ?int
    {
        $últim = CotxeAcces::where('matricula', $matricula)
            ->where('autoritzat', true)
            ->orderByDesc('data')
            ->first();

        if (!$últim) return null;

        return Carbon::parse($últim->data)->diffInSeconds(now());
    }

    /**
     * Registra un nou accés al pàrquing
     */
    public function registrarAcces(string $matricula, bool $autoritzat, bool $porta_oberta, string $device = null, string $tipus = null): void
    {
        CotxeAcces::create([
            'matricula' => $matricula,
            'data' => now(),
            'autoritzat' => $autoritzat,
            'porta_oberta' => $porta_oberta,
            'device' => $device,
            'tipus' => $tipus
        ]);
    }

    public function obrirIPorta(): void
    {
        $url = config('parking.porta_url');
        $id = config('parking.porta_device_id');
        $user = config('parking.porta_user');
        $pass = config('parking.porta_pass');

        Http::withBasicAuth($user, $pass)
            ->get("$url/api/callAction?deviceID=$id&name=turnOn");

        sleep(2);

        Http::withBasicAuth($user, $pass)
            ->get("$url/api/callAction?deviceID=$id&name=turnOff");
    }
}
