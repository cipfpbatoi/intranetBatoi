<?php
namespace Intranet\Botones;

class StateModels
{
    private $features;

    public function __construct($document)
    {
        $this->features = config('modelos.' . $document);
    }

    public function __get($key)
    {
        return isset($this->$key) ? $this->$key : (isset($this->features[$key]) ? $this->features[$key] : null);
    }

    public function getView(){
        return isset($this->features['pdf']) ?
            $this->pdf:'pdf.'.$this->modelo;
    }
}

