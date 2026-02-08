<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Services\NotificationService;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Profesor;
use Intranet\Entities\Signatura;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\Exceptions\CertException;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\DigitalSignatureService;
use Styde\Html\Facades\Alert;

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
            $this->panel->setBotonera([], ['pdf','delete']);
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
        return Signatura::where(function ($query) {
            $query->where('tipus', 'like', 'A1%') // Canviat a 'like' i afegit '%'
            ->where('signed', 0);
        })
            ->orWhere(function ($query) {
                $query->where('tipus', 'like', 'A2%') // Canviat a 'like' i afegit '%'
                ->where('signed', '<', 3);
            })
            ->get();

    }

    public function sign(Request $request)
    {
        $signatures = array_keys($request->toArray(), "on");
        $decrypt = $request['decrypt']??null;
        $passCert = $request['cert']??null;
        $signed = array();
        if (isset($decrypt)) {
            try {

                $file = DigitalSignatureService::decryptCertificateUser($decrypt, authUser());
                DigitalSignatureService::readCertificat($file, $passCert);
                foreach ($signatures as $signature) {
                    $signatura = Signatura::find($signature);
                    if ($signatura) {
                        $fileToSign = $signatura->routeFile;
                        if (!file_exists($fileToSign)) {
                            throw new IntranetException("No s'ha trobat el PDF a signar: $fileToSign");
                        }
                        if (file_exists($file)) {
                            $x = config("signatures.files.{$signatura->tipus}.director.x");
                            $y = config("signatures.files.{$signatura->tipus}.director.y");
                            DigitalSignatureService::sign(
                                $fileToSign,
                                $fileToSign,
                                $x,
                                $y,
                                $file,
                                $passCert
                            );
                            $signatura->signed += 3;
                            $signatura->save();
                            $signed[$signatura->idProfesor] = true;
                        }
                    }
                }
            } catch (CertException $exception){
                Log::channel('certificate')->alert($exception->getMessage(), [
                    'intranetUser' => authUser()->fullName,
                ]);
                Alert::warning($exception->getMessage());
                app(NotificationService::class)->send(
                    config('avisos.errores'),
                    $exception->getMessage()." : ".authUser()->fullName
                );
                if (isset($file)) {
                    unlink($file);
                }
                return back();
            } catch (IntranetException $exception){
                Log::channel('certificate')->alert($exception->getMessage(), [
                    'intranetUser' => authUser()->fullName,
                ]);
                Alert::warning($exception->getMessage());
                app(NotificationService::class)->send(
                    config('avisos.errores'),
                    $exception->getMessage()." : ".authUser()->fullName
                );
                if (isset($file)) {
                    unlink($file);
                }
                return back();
            }
        }
        foreach (array_keys($signed) as $dni){
            app(NotificationService::class)->send($dni,'Tens nous documents signats','/signatura');
        }
        return back();
    }
}
