<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Validator;
use Intranet\Http\Requests\FicharStoreRequest;
use Intranet\Http\Requests\MyMailStoreRequest;
use Intranet\Http\Requests\SendAvaluacioEmailStoreRequest;
use Tests\TestCase;

class ControllerFormRequestRulesTest extends TestCase
{
    public function test_fichar_store_request_requerix_codigo(): void
    {
        $rules = (new FicharStoreRequest())->rules();

        $invalid = Validator::make([], $rules);
        $this->assertTrue($invalid->fails());
        $this->assertArrayHasKey('codigo', $invalid->errors()->toArray());

        $valid = Validator::make(['codigo' => 'ABC123'], $rules);
        $this->assertFalse($valid->fails());
    }

    public function test_my_mail_store_request_nom_es_accepta_collect_configurat(): void
    {
        config(['auxiliares.collectMailable' => ['Profesor' => 'Professorat', 'Alumno' => 'Alumnat']]);
        $rules = (new MyMailStoreRequest())->rules();

        $invalid = Validator::make(['collect' => 'Invalid'], $rules);
        $this->assertTrue($invalid->fails());
        $this->assertArrayHasKey('collect', $invalid->errors()->toArray());

        $valid = Validator::make(['collect' => 'Profesor'], $rules);
        $this->assertFalse($valid->fails());
    }

    public function test_send_avaluacio_store_request_requerix_nia(): void
    {
        $rules = (new SendAvaluacioEmailStoreRequest())->rules();

        $invalid = Validator::make([], $rules);
        $this->assertTrue($invalid->fails());
        $this->assertArrayHasKey('nia', $invalid->errors()->toArray());

        $valid = Validator::make(['nia' => '10788988'], $rules);
        $this->assertFalse($valid->fails());
    }
}
