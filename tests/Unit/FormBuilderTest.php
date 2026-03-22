<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Intranet\Services\UI\FormBuilder;
use Tests\TestCase;

/**
 * Verifica el contracte de tipus generat per FormBuilder.
 */
class FormBuilderTest extends TestCase
{
    public function test_form_fields_date_time_i_datetime_usan_tipos_natius(): void
    {
        $builder = new FormBuilder(new DummyFormBuilderModel(), [
            'fecha' => ['type' => 'date'],
            'hora' => ['type' => 'time'],
            'desde' => ['type' => 'datetime'],
        ]);

        $default = $builder->getDefault();

        $this->assertSame('date', $default['fecha']['type']);
        $this->assertSame('time', $default['hora']['type']);
        $this->assertSame('datetimeLocal', $default['desde']['type']);
        $this->assertSame('themes/bootstrap/fields/date', $default['fecha']['params']['template']);
        $this->assertSame('themes/bootstrap/fields/time', $default['hora']['params']['template']);
        $this->assertSame('themes/bootstrap/fields/datetime', $default['desde']['params']['template']);
        $this->assertStringContainsString(' date', $default['fecha']['params']['class']);
        $this->assertStringContainsString(' time', $default['hora']['params']['class']);
        $this->assertStringContainsString(' datetime', $default['desde']['params']['class']);
    }
}

class DummyFormBuilderModel extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'fecha',
        'hora',
        'desde',
    ];

    /**
     * @var array<string, string>
     */
    protected $rules = [
        'fecha' => 'required|date',
    ];

    public $timestamps = false;

    /**
     * Indica si un camp és obligatori segons les regles simulades.
     *
     * @param string $campo
     * @return bool
     */
    public function isRequired($campo): bool
    {
        return isset($this->rules[$campo]) && str_contains($this->rules[$campo], 'required');
    }
}
