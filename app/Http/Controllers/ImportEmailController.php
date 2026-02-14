<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Profesor\ProfesorService;
use Illuminate\Http\Request;
Use Intranet\Entities\Alumno;
use Styde\Html\Facades\Alert;


/**
 * Class ImportController
 * @package Intranet\Http\Controllers
 */
class ImportEmailController extends Controller
{


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('seeder.importEmail');
    }

    public function hazDNI($dni,$nia){
        // nia diferent per al mateix dni
        $alumno = Alumno::where('dni',$dni)->where('nia','<>',$nia)->first();
        if ($alumno){
            $alumno->nia = $nia;
            $alumno->save();
            return $dni;
        } else {
            if (strlen($dni) > 8) return $dni;
            $alumno = Alumno::find($nia);
            if ($alumno) return $alumno->dni;
            else {
                $dniFictici = 'F'.Str::random(9);
                Alert::warning('Alumne amb DNI Fictici '.$dniFictici);
                return $dniFictici;
            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(Request $request)
    {
        if (!$request->hasFile('fichero') || !file_exists($request->file('fichero'))) {
            Alert::danger(trans('messages.generic.noFile'));
            return back();
        }
        $extension = $request->file('fichero')->getClientOriginalExtension();
        if (!$request->file('fichero')->isValid() || $extension <> 'csv') {
            Alert::danger(trans('messages.generic.invalidFormat'));
            return back();
        }


        if (!$fp = fopen($request->file('fichero'), "r")){
            Alert::danger("No s'ha pogut obrir el fitxer");
            return back();
        }
        $contents = fread($fp,filesize($request->file('fichero')));
        $linees = explode(PHP_EOL,$contents);
        $quants = 0;
        foreach ($linees as $linea){
            $resultat = explode(';',$linea);
            if (isset($resultat[1])) {
                $quants += $this->modifica($resultat[0], $resultat[1]);
            }
        }
        Alert::info("Modificats $quants emails");

        return view('seeder.store');
    }

    private function modifica($key,$email){
        $long = strlen($key);
        if ($long == 9){
            $key = '0'.$key;
            if ($profesor = app(ProfesorService::class)->find((string) $key)){
                $profesor->email = trim($email);
                $profesor->save();
                Alert::success("Professor: Email $email incorporat a DNI $key");
                return 1;
            }
        } else {
            if ($long == 8) {
                if ($alumne = Alumno::find($key)) {
                    $alumne->email = trim($email);
                    $alumne->save();
                    Alert::success("Alumne: Email $email incorporat a NIA $key");
                    return 1;
                }
            }
        }
        Alert::warning("$key No trobat en BD");
        return 0;
    }



}
