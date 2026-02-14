<?php
namespace Intranet\Finders\MailFinders;

use Illuminate\Support\Facades\DB;
use Intranet\Entities\AlumnoFct;
use Intranet\Http\Resources\SignaturaResource;


class MyA1Finder extends Finder
{
    public function __construct()
    {
        $dni = apiAuthUser()->dni;
        $fcts = AlumnoFct::misFcts($dni)->whereNotNull('idSao')->get();
        $this->elements = SignaturaResource::collection($fcts);
    }

}
