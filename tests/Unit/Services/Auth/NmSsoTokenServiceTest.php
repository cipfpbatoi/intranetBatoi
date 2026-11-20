<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Auth;

use DateTimeImmutable;
use Intranet\Entities\Profesor;
use Intranet\Services\Auth\NmSsoTokenService;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use LogicException;
use Tests\TestCase;

/**
 * Proves del contracte criptogràfic del SSO de NM.
 */
class NmSsoTokenServiceTest extends TestCase
{
    private string $privateKeyPath;
    private string $publicKeyPath;
    private string $passphrase = 'test-passphrase';

    /**
     * Genera un parell RSA efímer i configura l'emissor de prova.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->privateKeyPath = storage_path('nm-sso-test-private.pem');
        $this->publicKeyPath = storage_path('nm-sso-test-public.pem');
        $this->generateKeyPair();

        config([
            'sso.nm.issuer' => 'https://intranet.cipfpbatoi.es',
            'sso.nm.audience' => 'nm.cipfpbatoi.es',
            'sso.nm.ttl' => 120,
            'sso.nm.key_id' => 'nm-test-01',
            'sso.nm.token_type' => 'nm-login+jwt',
            'sso.nm.private_key' => $this->privateKeyPath,
            'sso.nm.public_key' => $this->publicKeyPath,
            'sso.nm.passphrase' => $this->passphrase,
            'jwt.private_key' => storage_path('keys/unrelated-private.pem'),
            'jwt.public_key' => storage_path('keys/unrelated-public.pem'),
        ]);
    }

    /**
     * Elimina les claus efímeres de prova.
     */
    protected function tearDown(): void
    {
        @unlink($this->privateKeyPath);
        @unlink($this->publicKeyPath);

        parent::tearDown();
    }

    public function test_emet_el_contracte_rs256_amb_dos_minuts_de_vida(): void
    {
        $token = (new NmSsoTokenService())->issueFor($this->profesor());
        $configuration = $this->verificationConfiguration();
        $parsed = $configuration->parser()->parse($token);

        $this->assertInstanceOf(Plain::class, $parsed);
        $this->assertTrue($configuration->validator()->validate(
            $parsed,
            new SignedWith($configuration->signer(), $configuration->verificationKey())
        ));
        $this->assertSame('RS256', $parsed->headers()->get('alg'));
        $this->assertSame('nm-login+jwt', $parsed->headers()->get('typ'));
        $this->assertSame('nm-test-01', $parsed->headers()->get('kid'));
        $this->assertTrue($parsed->hasBeenIssuedBy('https://intranet.cipfpbatoi.es'));
        $this->assertTrue($parsed->isPermittedFor('nm.cipfpbatoi.es'));
        $this->assertTrue($parsed->isRelatedTo('2042'));
        $this->assertSame('nm_sso', $parsed->claims()->get('token_use'));
        $this->assertSame('JOAN', $parsed->claims()->get('name'));
        $this->assertSame('Pérez Ferri', $parsed->claims()->get('surnames'));
        $this->assertSame('joan@example.test', $parsed->claims()->get('email'));
        $this->assertSame('profesor', $parsed->claims()->get('role'));
        $this->assertSame(1, $parsed->claims()->get('version'));
        $this->assertNotSame('', $parsed->claims()->get('jti'));

        $issuedAt = $parsed->claims()->get('iat');
        $notBefore = $parsed->claims()->get('nbf');
        $expiresAt = $parsed->claims()->get('exp');
        $this->assertInstanceOf(DateTimeImmutable::class, $issuedAt);
        $this->assertEquals($issuedAt, $notBefore);
        $this->assertSame(120, $expiresAt->getTimestamp() - $issuedAt->getTimestamp());
    }

    public function test_rebutja_una_caducitat_superior_a_dos_minuts(): void
    {
        config(['sso.nm.ttl' => 121]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('entre 1 i 120 segons');

        (new NmSsoTokenService())->issueFor($this->profesor());
    }

    /**
     * Crea el professor mínim per als claims del token.
     */
    private function profesor(): Profesor
    {
        $profesor = new Profesor();
        $profesor->setRawAttributes([
            'dni' => '12345678Z',
            'codigo' => 2042,
            'nombre' => 'JOAN',
            'apellido1' => 'PÉREZ',
            'apellido2' => 'FERRI',
            'email' => 'joan@example.test',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
        ]);

        return $profesor;
    }

    /**
     * Genera les claus que només existixen durant la prova.
     */
    private function generateKeyPair(): void
    {
        $key = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        $this->assertNotFalse($key);

        $privatePem = '';
        $this->assertTrue(openssl_pkey_export($key, $privatePem, $this->passphrase));
        $details = openssl_pkey_get_details($key);
        $this->assertIsArray($details);
        $this->assertArrayHasKey('key', $details);
        $this->assertNotFalse(file_put_contents($this->privateKeyPath, $privatePem));
        $this->assertNotFalse(file_put_contents($this->publicKeyPath, $details['key']));
    }

    /**
     * Configura el verificador amb la clau pública dedicada.
     */
    private function verificationConfiguration(): Configuration
    {
        return Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file($this->privateKeyPath, $this->passphrase),
            InMemory::file($this->publicKeyPath)
        );
    }
}
