<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Signatura;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\Services\DigitalSignatureService;

/**
 * Class PanelExpedienteController
 * @package Intranet\Http\Controllers
 */
class PanelSignaturaController extends BaseController
{

    /**
     * @var array
     */
    protected $gridFields = [ 'profesor', 'centre', 'tipus',  'alumne',  'created_at'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Signatura';

    /**
     * @var array
     */
    protected $parametresVista = [ 'modal' => ['signaturaDirector']];


    /**
     *
     */
    protected function iniBotones()
    {
        if (authUser()->dni === config('avisos.director') || authUser()->dni === config('avisos.errores')) {
            $this->panel->setBotonera([], ['delete']);
            $this->panel->setBoton(
                'index',
                new BotonBasico(
                    "signatura.post",
                    ['class' => 'btn-success signatura']
                )
            );
        }
    }

    /**
     * @return mixed
     */
    protected function search()
    {
        return Signatura::where('signed', 0)->get();
    }


    public function sign(Request $request)
    {
        $signatures = array_keys($request->toArray(), "on");
        $decrypt = $request['decrypt']??null;
        $passCert = $request['cert']??null;

        if (isset($decrypt)) {
            $nameFile = AuthUser()->fileName;
            $certFile = DigitalSignatureService::decryptCertificate($nameFile, $decrypt);
        }

        if ($certFile && $passCert) {
            foreach ($signatures as $signature) {
                $signatura = Signatura::find($signature);
                if ($signatura) {
                    $file = $signatura->routeFile;
                    if (file_exists($file)) {
                        $x = config("signatures.files.{$signatura->tipus}.director.x");
                        $y = config("signatures.files.{$signatura->tipus}.director.y");
                        DigitalSignatureService::sign(
                            $file,
                            $file,
                            $x,
                            $y,
                            $certFile,
                            $passCert
                        );
                        $signatura->signed = 1;
                        $signatura->save();
                    }
                }
            }
        }
        return back();
    }
}
