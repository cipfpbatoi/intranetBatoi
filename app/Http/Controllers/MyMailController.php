<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;

use Intranet\Botones\Mail as myMail;
use Illuminate\Http\Request;

/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class MyMailController extends Controller
{
    public function send(Request $request)
    {
        $mail = new myMail($request->to,$request->toPeople,$request->subject,$request->content);
        $mail->send();
        return $this->redirect($request->route);
    }



    
    
}
