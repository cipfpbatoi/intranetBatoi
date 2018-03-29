<?php

namespace Intranet\Http\Controllers\Auth\Profesor;

use Intranet\Http\Controllers\Auth\ResetPasswordController;

class ResetProfesorController extends ResetPasswordController
{

    public function __construct()
    {
        $this->middleware('guest');
    }

}

