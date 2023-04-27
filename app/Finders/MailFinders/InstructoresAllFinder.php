<?php


namespace Intranet\Finders\MailFinders;

use Intranet\Entities\Instructor;


class InstructoresAllFinder extends Finder
{
    public function __construct()
    {
        $this->elements = Instructor::all();
    }

}