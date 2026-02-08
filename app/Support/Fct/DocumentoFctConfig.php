<?php
namespace Intranet\Support\Fct;

use function config;

class DocumentoFctConfig
{
    private $features;


    public function __construct($document)
    {
        if (config("fctEmails.$document")) {
            $this->features = config("fctEmails.$document");
        } elseif (config("fctPdfs.$document")) {
            $this->features = config("fctPdfs.$document");
        } else {
            $this->features = null;
        }
    }


    public function __get($key)
    {
        return $this->features[$key]??null;
    }

    public function __isset($key)
    {
        return isset($this->features[$key]);
    }

    public function __set($key, $value)
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
