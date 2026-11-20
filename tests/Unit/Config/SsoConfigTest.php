<?php

declare(strict_types=1);

namespace Tests\Unit\Config;

use Illuminate\Support\Env;
use Tests\TestCase;

/**
 * Proves de resolució de les rutes de claus del SSO.
 */
class SsoConfigTest extends TestCase
{
    public function test_resol_les_rutes_relatives_des_de_l_arrel_de_l_aplicacio(): void
    {
        $config = $this->loadSsoConfig(
            'storage/keys/nm-relative-private.pem',
            'storage/keys/nm-relative-public.pem'
        );

        $this->assertSame(
            base_path('storage/keys/nm-relative-private.pem'),
            $config['nm']['private_key']
        );
        $this->assertSame(
            base_path('storage/keys/nm-relative-public.pem'),
            $config['nm']['public_key']
        );
    }

    public function test_conserva_les_rutes_absolutes_configurades_en_l_entorn(): void
    {
        $config = $this->loadSsoConfig(
            '/run/secrets/nm-absolute-private.pem',
            '/run/secrets/nm-absolute-public.pem'
        );

        $this->assertSame('/run/secrets/nm-absolute-private.pem', $config['nm']['private_key']);
        $this->assertSame('/run/secrets/nm-absolute-public.pem', $config['nm']['public_key']);
    }

    public function test_usa_les_claus_de_storage_quan_no_hi_ha_variables_d_entorn(): void
    {
        $config = $this->loadSsoConfig(null, null);

        $this->assertSame(storage_path('keys/nm-sso-private.pem'), $config['nm']['private_key']);
        $this->assertSame(storage_path('keys/nm-sso-public.pem'), $config['nm']['public_key']);
    }

    /**
     * Carrega la configuració amb valors d'entorn controlats i restaura l'estat original.
     *
     * @return array<string, mixed>
     */
    private function loadSsoConfig(?string $privateKey, ?string $publicKey): array
    {
        $repository = Env::getRepository();
        $originalPrivateKey = $repository->get('NM_SSO_PRIVATE_KEY');
        $originalPublicKey = $repository->get('NM_SSO_PUBLIC_KEY');

        try {
            $this->setEnvironmentValue('NM_SSO_PRIVATE_KEY', $privateKey);
            $this->setEnvironmentValue('NM_SSO_PUBLIC_KEY', $publicKey);

            return require config_path('sso.php');
        } finally {
            $this->setEnvironmentValue('NM_SSO_PRIVATE_KEY', $originalPrivateKey);
            $this->setEnvironmentValue('NM_SSO_PUBLIC_KEY', $originalPublicKey);
        }
    }

    /**
     * Establix o elimina una variable del repositori d'entorn de Laravel.
     */
    private function setEnvironmentValue(string $key, ?string $value): void
    {
        $repository = Env::getRepository();

        if ($value === null) {
            $repository->clear($key);

            return;
        }

        $repository->set($key, $value);
    }
}
