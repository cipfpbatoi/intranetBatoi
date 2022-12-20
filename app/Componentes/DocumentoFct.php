<?php
namespace Intranet\Componentes;

use function config;

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

    public function __isset($key)
    {
        return isset($this->features[$key]);
    }

    public function __set($key,$value)
    {
        $this->features['email'][$key] = $value;
    }

    public function getFinder()
    {
        return isset($this->features['finder']) ?
            "Intranet\\Finders\\".$this->finder."Finder":
            "Intranet\\Finders\\".$this->modelo."Finder";
    }
    public function getResource()
    {
        return isset($this->features['resource'])?
            "Intranet\\Http\\Resources\\Select".$this->resource."Resource":
            "Intranet\\Http\\Resources\\Select".$this->modelo."Resource";
    }


}

