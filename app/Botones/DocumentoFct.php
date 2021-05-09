<?php
namespace Intranet\Botones;

class DocumentoFct
{
    private $features;

    public function __construct($document)
    {
        $this->features = config('fctEmails.' . $document);
    }

    public function __get($key)
    {
        return $this->features[$key]??null;
    }

    public function __isset($key){
        return isset($this->features[$key]);
    }

    public function getFinder(){
        return isset($this->finder) ?
            "Intranet\\Finders\\".$this->finder."Finder":
            "Intranet\\Finders\\".$this->modelo."Finder";
    }
    public function getResource(){
        return isset($this->resource)?
            "Intranet\\Http\\Resources\\Select".$this->resource."Resource":
            "Intranet\\Http\\Resources\\Select".$this->modelo."Resource";
    }


}

