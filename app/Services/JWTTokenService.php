<?php

namespace Intranet\Services;

use DateTimeImmutable;
use Intranet\Entities\Modulo_grupo;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class JWTTokenService
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

    public function createTokenProgramacio($idModuleGrupo)
    {
        $moduleGrupo =Modulo_grupo::find($idModuleGrupo);
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
            ->withClaim('module', $moduleGrupo->ModuloCiclo->idModule)
            ->withClaim('cycle', $moduleGrupo->ModuloCiclo->Ciclo->ciclo)
            ->withClaim('turn', $this->turno($moduleGrupo->Grupo->turno))
            ->withClaim('name', authUser()->fullName)
            ->withClaim('email', authUser()->email)
            ->withClaim('department', $moduleGrupo->Grupo->Departamento->deptcurt)
            ->getToken($this->config->signer(), $this->config->signingKey());

        return response()->json(['token' => $token->toString()]);
    }

    private function role($role): array
    {
        $roles = ['ROLE_USER'];
        if (esRol($role,config('roles.rol.profesor'))) {
            $roles[] = 'ROLE_TEACHER';
        }
        if (esRol($role,config('roles.rol.jefe_dpto'))) {
            $roles[] = 'ROLE_HEAD_DEPARTMENT';
        }
        return $roles;
    }

    private function turno($turno)
    {
        switch ($turno) {
            case 'D':
                return 'presential';
            case 'S':
                return 'half-presential';
        }
    }
}