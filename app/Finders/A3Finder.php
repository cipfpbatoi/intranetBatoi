<?php
namespace Intranet\Finders;

use Intranet\Entities\Signatura;

class A3Finder extends Finder
{
    public function exec()
    {
        return Signatura::where('signed', '2')
            ->where('idProfesor', apiAuthUser()->dni)
            ->whereIn('tipus', ['A3', 'A3DUAL'])
            ->get();
    }

}
