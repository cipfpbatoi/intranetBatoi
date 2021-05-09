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
        return isset($this->$key) ? $this->$key : (isset($this->features[$key]) ? $this->features[$key] : null);
    }

    public function getFinder(){
        return isset($this->features['finder']) ?
            "Intranet\\Finders\\".$this->finder."Finder":
            "Intranet\\Finders\\".$this->modelo."Finder";
    }
    public function getResource(){
        return isset($this->features['resource'])?
            "Intranet\\Http\\Resources\\Select".$this->resource."Resource":
            "Intranet\\Http\\Resources\\Select".$this->modelo."Resource";
    }


}

