<?php
namespace Intranet\Finders\MailFinders;

use Intranet\Entities\AlumnoFct;
use Intranet\Http\Resources\SignaturaResource;


class MySignaturesFinder extends Finder
{
    public function __construct()
    {
        $dni = apiAuthUser()->dni;
        $fcts = AlumnoFct::misFcts($dni)->whereNotNull('idSao')
            ->where('desde','>=', date('Y-m-d'))
            ->where(function ($query) {
                $query->whereDoesntHave('signatures')
                    ->orWhereHas('signatures', function ($query) {
                        $query->where('signed', 0);
                    });
            })->get();
        $this->elements = SignaturaResource::collection($fcts);
    }

}
