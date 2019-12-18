<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;

use Intranet\Botones\Mail as myMail;
use Illuminate\Http\Request;
use Intranet\Entities\Instructor;

/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class MyMailController extends Controller
{
    
    public function send(Request $request)
    {
        $mail = new myMail($request->to,$request->toPeople,$request->subject,$request->content,null,null,$request->class);
        $mail->send();
        return redirect($request->route);
    }


    public function store(Request $request)
    {
        $colectiu = 'Intranet\\Entities\\'.$request->collect;
       
        $mail = new myMail($colectiu::all(),null,null,null,null,null,null);
        return $mail->render('\\');
    }
    
    public function create()
    {
        return view('email.chooseCollect');
    }


}
