<?php

namespace Intranet\Filters;

class EmptyFilter implements Filter {

    private $document;


    public function __construct($document){
        $this->document = $document;
    }

    public function exec(&$elements){}

    public function getDocument()
    {
        return $this->document;
    }


}