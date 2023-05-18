<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\IpGuardia;
use DB;

class IpGuardiaController extends ApiBaseController
{
    protected $model = 'ipGuardia';

    public function arrayIps()
    {
        $ips = array();
        foreach (IpGuardia::all() as $key => $ip) {
            $ips[$key]['ip'] = $ip->ip;
            $ips[$key]['codOcup'] = (int)$ip->codOcup;
        }
        return $this->sendResponse($ips, 'OK');

    }
}
