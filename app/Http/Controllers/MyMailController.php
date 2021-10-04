<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;

use Intranet\Botones\MyMail;
use Illuminate\Http\Request;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class MyMailController extends Controller
{
    
    public function send(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()){
            $ext = $request->file('file')->getClientOriginalExtension();
            $mime = $request->file('file')->getMimeType();
            $nom = "AdjuntCorreu.".$ext;
            $request->file('file')->move(storage_path('tmp/'), $nom);
            $fitxer = 'tmp/'.$nom;
            $attach = [$fitxer => $mime];
        }
        else {
            $attach = null;
        }
        $mail = new MyMail($request->to,$request->contenido,$request->toArray(),$attach);
        $mail->send();
        return redirect($request->route);
    }




    public function store(Request $request)
    {
        $colectiu = 'Intranet\\Entities\\'.$request->collect;
        $mail = new MyMail($colectiu::all(),null,[],null,true);
        return $mail->render('\\');
    }
    
    public function create()
    {
        return view('email.chooseCollect');
    }


}
