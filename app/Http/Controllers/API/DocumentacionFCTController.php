<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Botones\DocumentoFct;

use Intranet\Http\Resources\SelectAlumnoFctResource;
use Intranet\Http\Resources\SelectColaboracionResource;
use Intranet\Http\Resources\SelectFctResource;
use Intranet\Finders\fctAlumnoFinder;
use Intranet\Finders\ColaboracionFinder;
use Intranet\Finders\FctFinder;

use Intranet\Services\DocumentService;


class DocumentacionFCTController
{

    public function exec($documento){
        $documento = new DocumentoFct($documento);
        $finder = "Intranet\\Finders\\".$documento->modelo."Finder";
        $resource = "Intranet\\Http\\Resources\\Select".$documento->modelo."Resource";

        $service = new DocumentService(new $finder($documento));
        return $resource::collection($service->load());
    }

    /**
    public function colaboracion($documento){
        $finder = new ColaboracionFinder(new DocumentoFct($documento));
        $service = new DocumentService($finder);

        return SelectColaboracionResource::collection($service->load());
    }

    public function alumno($documento){
        $finder = new FctAlumnoFinder(new DocumentoFct($documento));
        $service = new DocumentService($finder);

        return SelectAlumnoFctResource::collection($service->load());
    }

    public function fct($documento){
        $finder = new FctFinder(new DocumentoFct($documento));
        $service = new DocumentService($finder);

        return SelectFctResource::collection($service->load());
    }
     */
}
