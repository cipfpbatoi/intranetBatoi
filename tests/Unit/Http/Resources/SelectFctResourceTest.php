<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Resources;

use Illuminate\Http\Request;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Fct;
use Intranet\Entities\Instructor;
use Intranet\Http\Resources\SelectFctResource;
use Tests\TestCase;

class SelectFctResourceTest extends TestCase
{
    public function test_to_array_uses_fct_context_helpers(): void
    {
        $centro = new Centro(['nombre' => 'Centre test']);
        $colaboracion = new Colaboracion();
        $colaboracion->setRelation('Centro', $centro);

        $instructor = new Instructor(['name' => 'Ada', 'surnames' => 'Lovelace']);

        $fct = new Fct();
        $fct->setRawAttributes(['id' => 7], true);
        $fct->setRelation('Colaboracion', $colaboracion);
        $fct->setRelation('Instructor', $instructor);
        $fct->marked = true;

        $resource = new SelectFctResource($fct);

        $this->assertSame([
            'id' => 7,
            'texto' => 'Ada Lovelace(Centre test)',
            'marked' => true,
        ], $resource->toArray(Request::create('/')));
    }
}
