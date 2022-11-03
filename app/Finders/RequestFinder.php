<?php

namespace Intranet\Finders;

use Illuminate\Support\Collection;


class RequestFinder extends Finder
{

    private $request;

    /**
     * RequestFinder constructor.
     * @param $modelo
     * @param $id
     */

    public function __construct($document)
    {
        $this->document = $document['document'];
        $this->request = $document['request'];
    }

    public function exec()
    {
        $elementos = new Collection();
        $modelo = "Intranet\\Entities\\".$this->document->modelo;
        foreach ($this->request->toArray() as $item => $value){
            if ($value == 'on'){
                $elementos->push($modelo::find($item));
            }
        }
        return $elementos;
    }
}
