<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;

use Intranet\Services\Mail\MyMail;
use Illuminate\Http\Request;
use Intranet\Http\Requests\MyMailStoreRequest;
use Styde\Html\Facades\Alert;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class MyMailController extends Controller
{
    
    public function send(Request $request)
    {
        $attach = []; // sempre definida

        // Si tens un input múltiple tipus name="file[]" :
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $key => $file) {
                if ($file && $file->isValid()) {
                    $ext  = $file->getClientOriginalExtension();
                    $mime = $file->getMimeType();
                    $nom  = "AdjuntCorreu$key.".$ext;

                    $file->move(storage_path('tmp/'), $nom);

                    $fitxer = 'tmp/'.$nom;
                    $attach[] = [$fitxer => $mime];
                }
            }
        }

        // Si no hi ha adjunts vàlids, passem null a MyMail
        $attach = $attach ?: null;

        if (strlen($request->contenido) < 50 && $request->editable) {
            Alert::danger('El contingut ha de ser més de 50 caracters');
        } else {
            $mail = new MyMail(
                $request->to,
                $request->contenido,
                $request->toArray(),
                $attach
            );
            $mail->send();
        }

        return redirect($request->route);
    }


    public function store(MyMailStoreRequest $request)
    {
        $stringFinder = 'Intranet\\Finders\\MailFinders\\'.$request->collect.'Finder';
        $finder = new $stringFinder();

        $mail = new MyMail($finder->getElements(), null, [], null, true);
        return $mail->render('\\');
    }
    
    public function create()
    {
        return view('email.chooseCollect');
    }


}
