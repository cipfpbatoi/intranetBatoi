<?php
namespace Intranet\Finders\MailFinders;

use Intranet\Entities\Signatura;
use Intranet\Http\Resources\SignaturaDireccionResource;


class SignaturesFinder extends Finder
{
    public function __construct()
    {
        $signatures = Signatura::where('signed', 0)->get();
        $this->elements = SignaturaDireccionResource::collection($signatures);
    }

}
