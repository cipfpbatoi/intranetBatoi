<?php
namespace Intranet\Finders\MailFinders;

use Illuminate\Support\Facades\DB;
use Intranet\Entities\AlumnoFct;
use Intranet\Http\Resources\SignaturaResource;


class MySignaturesFinder extends Finder
{
    public function __construct()
    {

        $dni = apiAuthUser()->dni;
        $fcts = AlumnoFct::misFcts($dni)->whereNotNull('idSao')
            ->where('desde','>=', date('Y-m-d'))
            ->whereNotIn('id', function($query) {
                $query->select(DB::raw("SUBSTRING(route, POSITION('/' IN route) + 1)"))
                    ->from('adjuntos')
                    ->where('route', 'LIKE', 'alumnofctaval/%');
            })->get();
        $this->elements = SignaturaResource::collection($fcts);
    }

}
