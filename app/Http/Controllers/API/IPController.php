<?php

namespace Intranet\Http\Controllers\API;
use Intranet\Http\Controllers\Controller;

class IPController extends Controller
{
    public function miIP(){
        return response()->json(['success'=>true,'data'=>getClientIpAddress()]);
    }
}
