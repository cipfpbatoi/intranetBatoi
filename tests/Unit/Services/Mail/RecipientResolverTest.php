<?php

namespace Tests\Unit\Services\Mail;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Instructor;
use Intranet\Services\Mail\RecipientResolver;
use Tests\TestCase;

class RecipientResolverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::connection('sqlite')->create('instructores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('name')->nullable();
            $table->string('surnames')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('departamento')->nullable();
        });
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('instructores');

        parent::tearDown();
    }

    public function testFormatReceiversAddsTrailingComma()
    {
        $resolver = new RecipientResolver();

        $a = (object) ['id' => 1, 'mail' => 'a@b.com', 'contact' => 'A'];
        $b = (object) ['id' => 2, 'email' => 'c@d.com', 'contacto' => 'B'];

        $result = $resolver->formatReceivers([$a, $b]);

        $this->assertSame('1(a@b.com;A),2(c@d.com;B),', $result);
    }

    public function testResolveElementsReturnsCollectionForNull()
    {
        $resolver = new RecipientResolver();

        $result = $resolver->resolveElements(null, null);

        $this->assertTrue($result->isEmpty());
    }

    public function testResolveElementAccepta_claus_textuals_com_els_dni_dels_instructors(): void
    {
        Instructor::query()->create([
            'dni' => '27459573B',
            'name' => 'Juan',
            'surnames' => 'Hernandez Garcia',
            'email' => 'juan@example.test',
        ]);

        $resolver = new RecipientResolver();

        $result = $resolver->resolveElement(
            '27459573B(juan@example.test;Juan Hernandez Garcia)',
            Instructor::class
        );

        $this->assertInstanceOf(Instructor::class, $result);
        $this->assertSame('27459573B', $result->dni);
    }
}
