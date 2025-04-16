<?php
namespace Intranet\Finders;

use Intranet\Entities\Signatura;

class A2Finder extends Finder
{
    public function exec()
    {
        return Signatura::where('signed','>=','2')
            ->where('idProfesor', apiAuthUser()->dni)
            ->where('tipus', 'A2')
            ->get();
    }

}
