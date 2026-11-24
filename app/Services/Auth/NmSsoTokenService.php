<?php

declare(strict_types=1);

namespace Intranet\Services\Auth;

use DateTimeImmutable;
use Intranet\Entities\Profesor;
use InvalidArgumentException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use LogicException;

/**
 * Emet assertions JWT de curta duració per al SSO de nm.cipfpbatoi.es.
 */
class NmSsoTokenService
{
    private Configuration $configuration;

    /**
     * Carrega el parell de claus dedicat al SSO de NM.
     */
    public function __construct()
    {
        $this->configuration = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file(
                $this->requiredStringConfig('private_key'),
                (string) config('sso.nm.passphrase', '')
            ),
            InMemory::file($this->requiredStringConfig('public_key'))
        );
    }

    /**
     * Genera una asserció firmada per a un professor identificable en NM.
     */
    public function issueFor(Profesor $profesor): string
    {
        $subject = trim((string) $profesor->codigo);
        if ($subject === '') {
            throw new InvalidArgumentException('El professor no té un codi vàlid per al SSO de NM.');
        }

        $now = new DateTimeImmutable();
        $expiresAt = $now->modify('+' . $this->ttl() . ' seconds');

        $token = $this->configuration->builder()
            ->withHeader('typ', $this->requiredStringConfig('token_type'))
            ->withHeader('kid', $this->requiredStringConfig('key_id'))
            ->issuedBy($this->requiredStringConfig('issuer'))
            ->permittedFor($this->requiredStringConfig('audience'))
            ->relatedTo($subject)
            ->identifiedBy(bin2hex(random_bytes(16)))
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($expiresAt)
            ->withClaim('token_use', 'nm_sso')
            ->withClaim('name', (string) $profesor->nombre)
            ->withClaim('surnames', (string) $profesor->surnames)
            ->withClaim('email', (string) $profesor->email)
            ->withClaim('role', 'profesor')
            ->withClaim('version', 1)
            ->getToken($this->configuration->signer(), $this->configuration->signingKey());

        return $token->toString();
    }

    /**
     * Retorna una cadena de configuració obligatòria.
     */
    private function requiredStringConfig(string $key): string
    {
        $value = trim((string) config('sso.nm.' . $key));
        if ($value === '') {
            throw new LogicException("Falta la configuració sso.nm.{$key}.");
        }

        return $value;
    }

    /**
     * Retorna la vida del token i impedix ampliar-la més de dos minuts.
     */
    private function ttl(): int
    {
        $ttl = (int) config('sso.nm.ttl', 120);
        if ($ttl < 1 || $ttl > 120) {
            throw new LogicException('La caducitat del SSO de NM ha d\'estar entre 1 i 120 segons.');
        }

        return $ttl;
    }
}
