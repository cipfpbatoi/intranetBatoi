<?php

namespace Intranet\Http\Controllers\Auth\Profesor;

use Intranet\Http\Controllers\Auth\HomeController as HomeIdentifyController;

/**
 * Description of HomeIdentifyController
 *
 * @author igomis
 */
class HomeController extends HomeIdentifyController
{

    protected $guard = 'profesor';

}
