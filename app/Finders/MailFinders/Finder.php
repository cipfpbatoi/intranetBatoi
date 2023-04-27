<?php
namespace Intranet\Finders\MailFinders;

abstract class Finder{

    protected $elements;

    public function getElements()
    {
        return $this->elements;
    }
}