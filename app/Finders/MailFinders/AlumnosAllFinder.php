<?php


namespace Intranet\Finders\MailFinders;

use Intranet\Entities\Alumno;


class AlumnosAllFinder extends Finder
{
    public function __construct(){
        $this->elements = Alumno::all();

    }

}