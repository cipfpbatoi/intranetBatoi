<?php

namespace Intranet\Http\Controllers;

use DateTimeImmutable;
use Illuminate\Http\Request;
use Intranet\Entities\Programacion;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Carbon\Carbon;

class JWTController extends Controller
{
    protected $config;

    public function __construct()
    {
        $this->config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file(config('jwt.private_key'), config('jwt.passphrase')),
            InMemory::file(config('jwt.public_key'))
        );
    }

    public function createToken($idProgramacio)
    {
        $programacio = Programacion::find($idProgramacio);
        $now = new DateTimeImmutable();
        $expiresAt = $now->modify('+' . config('jwt.expiry') . ' seconds');

        $token = $this->config->builder()
            ->issuedBy('http://intranet.cipfpbatoi.es') // Configura-ho amb el teu domini
            ->permittedFor('http://programacions.cipfpbatoi.es') // Configura-ho amb el domini de l'aplicació de programacions
            ->identifiedBy(bin2hex(random_bytes(16)))
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($expiresAt)
            ->withClaim('role', $this->role(authUser()->rol))
            ->withClaim('module', $programacio->Modulo->codigo)
            ->withClaim('cycle', $programacio->Ciclo->ciclo)
            ->withClaim('turn', $this->turno($programacio->Grupo->turno))
            ->getToken($this->config->signer(), $this->config->signingKey());

        return response()->json(['token' => $token->toString()]);
    }

    private function role($role)
    {
        if (esRol($role,config('roles.rol.jefe_dpto'))) {
            return 'ROLE_HEAD_DEPARTMENT';
        }
        if (esRol($role,config('roles.rol.profesor'))) {
            return 'ROLE_TEACHER';
        }
        return 'ROLE_USER';
    }

    private function turno($turno)
    {
        switch ($turno) {
            case 'D':
                return 'presential';
            case 'S':
                return 'semi-presential';
        }
    }
}
