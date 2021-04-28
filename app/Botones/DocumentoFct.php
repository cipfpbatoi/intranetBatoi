<?php
namespace Intranet\Botones;

class DocumentoFct
{
    public function __construct($document)
    {
        $this->features = config('fctEmails.' . $document);
    }

    public function __get($key)
    {
        return isset($this->$key) ? $this->$key : (isset($this->features[$key]) ? $this->features[$key] : null);
    }
}

