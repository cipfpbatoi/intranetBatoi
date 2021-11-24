<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;

use Intranet\Botones\MyMail;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;


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
        if (strlen($request->contenido) < 50) {
            Alert::danger('El contingut ha de ser més de 50 caracters');
        } else {
            $mail = new MyMail($request->to, $request->contenido, $request->toArray(), $attach);
            $mail->send();

        }
        return redirect($request->route);
    }


    public function store(Request $request)
    {
        $stringFinder = 'Intranet\\Finders\\MailFinders\\'.$request->collect.'Finder';
        $finder = new $stringFinder();

        $mail = new MyMail($finder->getElements(),null,[],null,true);
        return $mail->render('\\');
    }
    
    public function create()
    {
        return view('email.chooseCollect');
    }


}
