<?php

namespace Intranet\Services;

use DateTimeImmutable;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Profesor;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class JWTTokenService
{
    protected $config;

    const EXPIRATION_DATE = '15 October';

    public function __construct()
    {
        $this->config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file(config('jwt.private_key'), config('jwt.passphrase')),
            InMemory::file(config('jwt.public_key'))
        );
    }

    public function createTokenProgramacio($idModuleGrupo,$dni=null)
    {
        $moduleGrupo =Modulo_grupo::findOrFail($idModuleGrupo);
        $now = new DateTimeImmutable();
        if (!$dni){
            $user = authUser();
            $expiresAt = $now->modify('+' . config('jwt.expiry') . ' seconds');
        } else {
            $user = Profesor::findOrFail($dni);
            $year = $now->format('Y');
            $expiresAt = new DateTimeImmutable(self::EXPIRATION_DATE . " $year");
        }

         $token = $this->config->builder()
            ->issuedBy('http://intranet.cipfpbatoi.es') // Configura-ho amb el teu domini
            ->permittedFor('http://programacions.cipfpbatoi.es') // Configura-ho amb el domini de l'aplicació de programacions
            ->identifiedBy(bin2hex(random_bytes(16)))
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($expiresAt)
            ->withClaim('role', $this->role($user->rol))
            ->withClaim('module', $moduleGrupo->ModuloCiclo->idModulo)
            ->withClaim('cycle', $moduleGrupo->ModuloCiclo->Ciclo->ciclo)
            ->withClaim('turn', $this->turno($moduleGrupo->Grupo->turno))
            ->withClaim('name', $user->nombre)
            ->withClaim('surnames', $user->surnames)
            ->withClaim('email', $user->email)
            ->withClaim('department', $user->Departamento->depcurt)
            ->withClaim('version', '1')
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }

    public static function getTokenLink($id,$dni = null)
    {
        $service = new JWTTokenService();
        $token = $service->createTokenProgramacio($id,$dni);
        return "https://pcompetencies.cipfpbatoi.es/login/auth/{$token}";

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