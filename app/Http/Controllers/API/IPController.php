<?php

namespace Intranet\Http\Controllers\API;


class IPController extends Controller
{
    public function miIP(){
        return response()->json(['success'=>true,'data'=>getClientIpAddress()]);
    }
}
