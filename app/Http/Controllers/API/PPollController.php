<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;

class PPollController extends ApiResourceController
{

    protected $namespace = 'Intranet\Entities\Poll\\';
    protected $model = 'PPoll';

}
