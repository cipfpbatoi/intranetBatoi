<?php

namespace Intranet\Sao;

use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Intranet\Services\Notifications\NotificationService;
use Intranet\Exceptions\CertException;
use Intranet\Sao\Documents\A1DocumentService;
use Intranet\Sao\Documents\A2DocumentService;
use Intranet\Sao\Documents\A5DocumentService;
use Intranet\Services\Signature\DigitalSignatureService;
use Intranet\Entities\AlumnoFct;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Log;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class A2
{
    private DigitalSignatureService $digitalSignatureService;
    private A1DocumentService $a1DocumentService;
    private A2DocumentService $a2DocumentService;
    private A5DocumentService $a5DocumentService;

    public function __construct(
        DigitalSignatureService $digitalSignatureService,
        ?A1DocumentService $a1DocumentService = null,
        ?A2DocumentService $a2DocumentService = null,
        ?A5DocumentService $a5DocumentService = null
    )
    {
        $this->digitalSignatureService = $digitalSignatureService;
        $this->a1DocumentService = $a1DocumentService ?? new A1DocumentService();
        $this->a2DocumentService = $a2DocumentService ?? new A2DocumentService($digitalSignatureService);
        $this->a5DocumentService = $a5DocumentService ?? new A5DocumentService($digitalSignatureService);
    }

    
    public static function setFireFoxCapabilities()
    {

        $profile = new FirefoxProfile();
        
        $profile->setPreference('browser.download.folderList', 2);
        $profile->setPreference(
            'browser.download.dir',
            config('sao.download.directory', storage_path('tmp'))
        );
        $profile->setPreference('browser.helperApps.neverAsk.saveToDisk', 'application/pdf');
        $profile->setPreference('browser.download.useDownloadDir', true);
        $profile->setPreference('browser.download.manager.showWhenStarting', false);
        $profile->setPreference('pdfjs.enabledCache.state', false);
        $profile->setPreference('modifyheaders.headers.count', 1);
        $profile->setPreference("modifyheaders.headers.action0", "Add");
            # Set here the name of the header
        $profile->setPreference("modifyheaders.headers.name0", "Content-Disposition");
            # Set here the value of the header
        $profile->setPreference("modifyheaders.headers.value0", "inline");
        $profile->setPreference("modifyheaders.headers.enabled0", true);
        $profile->setPreference("modifyheaders.config.active", true);
        $profile->setPreference("modifyheaders.config.alwaysOn", true);

        if (config('services.selenium.firefox_path')) {
            $caps = DesiredCapabilities::firefox()->setCapability(FirefoxOptions::CAPABILITY,
                ['binary' => config('services.selenium.firefox_path')]);
        } else {
            $caps = DesiredCapabilities::firefox();
        }
        $caps->setCapability('firefox_profile', $profile);
        return $caps;
    }

    public function index($driver, $request, $file = null)
    {
        $driver->manage()->timeouts()->pageLoadTimeout(2);
        $fcts = array_keys($request, 'on');
        $decrypt = $request['decrypt'] ?? null;
        $passCert = $request['cert'] ?? null;
        $nomFitxer = storage_path('tmp/' . authUser()->fileName . '.pfx');

        try {
            $certPath = null;
            $certPassword = null;
            if (isset($decrypt)) {
                $this->digitalSignatureService->decryptUserCertificateInstance($decrypt, authUser());
                $this->digitalSignatureService->readCertificate($nomFitxer, $passCert);
                $certPath = $nomFitxer;
                $certPassword = $passCert;
            }

            if ($file) {
                $file->move(dirname($nomFitxer), basename($nomFitxer));
                @unlink($file->getRealPath());
                $this->digitalSignatureService->readCertificate($nomFitxer, $passCert);
                $certPath = $nomFitxer;
                $certPassword = $passCert;
            }
            $this->downloadFilesFromFcts($driver, $fcts, $certPath, $certPassword);
            if (file_exists($nomFitxer)) {
                unlink($nomFitxer);
            }
        } catch (CertException $exception) {
            Log::channel('certificate')->alert($exception->getMessage(), [
                'intranetUser' => authUser()->fullName,
            ]);
            Alert::warning($exception->getMessage());
            app(NotificationService::class)->send(
                config('avisos.errores'),
                $exception->getMessage() . " : " . authUser()->fullName
            );
            if (file_exists($nomFitxer)) {
                unlink($nomFitxer);
            }
            return back();
        } catch (\Throwable $exception) {
            Log::error('Error en procés SAO A2', [
                'error' => $exception->getMessage(),
                'user' => authUser()->fullName,
            ]);
            Alert::warning('S\'ha produït un error inesperat en el procés SAO A2');
            return back();
        }
        return back();
    }



    public function downloadFilesFromFcts(RemoteWebDriver $driver, $fcts, $certPath = null, $certPassword = null)
    {
        $signat = false;
        $a1 = $a2 = $a3 = $fA1 = $a5 = false;

        foreach ($fcts as $fct) {
            if ($fct === 'FA1'){ //A1 forçat
                $fA1 = true;
            }
            if ($fct === 'A1') { //A1 dèbil
                $a1 = true;
            }
            if ($fct === 'A2') { //A2 forçat
                $a2 = true;
            }
            if ($fct === 'A3') { //A3 forçat
                $a3 = true;
            }
            if ($fct === 'A5') {
                $a5 = true;
            }
            $fctAl = AlumnoFct::find($fct);
            if ($fctAl){
                // Anexe 1
                if ($fA1 || ($a1 && ($fctAl->Fct->Colaboracion->Centro->Empresa->ConveniCaducat
                    || $fctAl->Fct->Colaboracion->Centro->Empresa->RenovatConveni))) {
                    $signat = $this->a1DocumentService->download($fctAl, $driver);
                }
                // Anexe 2
                if ($a2 && $this->a2DocumentService->download($fctAl, $driver, $certPath, $certPassword, 2)) {
                    $signat = true;
                }
                // Anexe 3
                if ($a3 && $certPath){
                    $this->a2DocumentService->download($fctAl, $driver, $certPath, $certPassword, 3);
                }
                if ($a5) {
                    $this->a5DocumentService->download($fctAl, $driver, $certPath, $certPassword);
                }
            }
        }
        if ($signat) {
            app(NotificationService::class)->send(
                config('avisos.director'),
                'Tens nous documents per signar de '.authUser()->fullName,
                '/direccion/signatures'
            );
        }
    }

}
