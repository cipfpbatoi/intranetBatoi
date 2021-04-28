<?php

namespace Intranet\Http\Controllers\API;



use Intranet\Http\Resources\SelectFctResource;
use Intranet\Http\Resources\SelectAlumnoFctResource;
use Intranet\Services\DocumentFctService;
use Intranet\Services\fctAlumnoFindService;
use Intranet\Services\fctFindService;

class FctController extends ApiBaseController
{

    protected $model = 'Espacio';

    public function documentation($dni){
        $document = new DocumentFctService('info',$dni);
        return SelectAlumnoFctResource::collection($document->finder());
    }

    public function follow($dni){
        $finder = new fctFindService($dni,config('fctEmails.follow'));
        return SelectFctResource::collection($finder->exec());
    }

}
