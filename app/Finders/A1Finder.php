<?php
namespace Intranet\Finders;

use Intranet\Entities\Signatura;

class A1Finder extends Finder
{
    public function exec()
    {
        return Signatura::where('signed', 1)
            ->where('idProfesor', apiAuthUser()->dni)
            ->where('tipus', 'A1')
            ->get();

    }

}
