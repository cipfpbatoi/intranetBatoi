<?php
namespace Intranet\Finders\MailFinders;

use Illuminate\Database\Eloquent\Collection;
use Intranet\Entities\Signatura;
use Intranet\Http\Resources\SignaturaDireccionResource;


class SignaturesFinder extends Finder
{
    public function __construct()
    {
        $collection = new Collection();
        $signatures = Signatura::where('signed', 0)->get();
        foreach ($signatures as $signature) {
            $collection->add($signature);
        }
        $signatures = Signatura::where('signed','<', 3)->where('tipus','A2')->get();
        foreach ($signatures as $signature) {
            $collection->add($signature);
        }
        $this->elements = SignaturaDireccionResource::collection($collection);
    }

}
