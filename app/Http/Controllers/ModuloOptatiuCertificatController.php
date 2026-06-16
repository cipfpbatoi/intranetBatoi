<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Application\ModuloOptatiu\ModuloOptatiuCertificatService;
use Intranet\Entities\Alumno;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\ModulOptatiuCertificat;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Panell de preparació i emissió de certificats de mòduls optatius.
 */
class ModuloOptatiuCertificatController extends Controller
{
    protected $perfil = 'profesor';
    protected $model = 'ModulOptatiuCertificat';

    private ?ModuloOptatiuCertificatService $certificatService = null;

    /**
     * Mostra els mòduls assignats al professor autenticat.
     */
    public function index()
    {
        $moduls = $this->certificats()->modulesForTeacher((string) authUser()->dni);

        return view('modul_optatiu_certificat.index', compact('moduls'));
    }

    /**
     * Mostra el formulari de denominació i notes d'un mòdul-grup.
     *
     * @param int|string $moduloGrupo
     */
    public function show($moduloGrupo)
    {
        $modul = $this->allowedModuloGrupo($moduloGrupo);
        $certificat = $this->certificats()->certificateFor($modul, (string) authUser()->dni);
        $data = $this->certificats()->panelData($certificat);

        return view('modul_optatiu_certificat.show', [
            'modul' => $modul,
            'certificat' => $certificat,
            'alumnes' => $data['alumnes'],
            'resultats' => $data['resultats'],
            'estats' => $data['estats'],
            'pdfDisponibles' => $data['pdfDisponibles'],
            'notes' => config('auxiliares.notas'),
        ]);
    }

    /**
     * Guarda denominació i notes del mòdul optatiu.
     *
     * @param Request $request
     * @param int|string $moduloGrupo
     */
    public function update(Request $request, $moduloGrupo)
    {
        $modul = $this->allowedModuloGrupo($moduloGrupo);
        $certificat = $this->certificats()->certificateFor($modul, (string) authUser()->dni);

        $validated = $request->validate([
            'denominacio' => 'required|string|max:200',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|integer|min:0|max:13',
        ]);

        $saved = $this->certificats()->save(
            $certificat,
            $validated['denominacio'],
            $validated['notes'] ?? []
        );

        Alert::success("S'han guardat {$saved} notes del mòdul optatiu.");

        return redirect()->route('modulOptatiuCertificat.show', ['moduloGrupo' => $modul->id]);
    }

    /**
     * Genera, envia i registra els certificats del mòdul.
     *
     * @param int|string $certificat
     */
    public function emit($certificat)
    {
        $certificat = ModulOptatiuCertificat::query()->findOrFail((int) $certificat);
        $this->allowedModuloGrupo($certificat->idModuloGrupo);

        $result = $this->certificats()->emit($certificat);
        foreach ($result['errors'] as $error) {
            Alert::danger($error);
        }

        if ($result['sent'] > 0) {
            Alert::success("S'han emés {$result['sent']} certificats.");
        }

        return redirect()->route('modulOptatiuCertificat.show', ['moduloGrupo' => $certificat->idModuloGrupo]);
    }

    /**
     * Mostra el certificat PDF d'un alumne sense registrar-lo ni enviar correus.
     *
     * @param int|string $certificat
     * @param int|string $alumne
     */
    public function pdf($certificat, $alumne)
    {
        $certificat = ModulOptatiuCertificat::query()->findOrFail((int) $certificat);
        $modul = $this->allowedModuloGrupo($certificat->idModuloGrupo);

        $alumne = $modul->Grupo->Alumnos
            ->firstWhere('nia', (string) $alumne);
        if (!$alumne instanceof Alumno) {
            throw new NotFoundDomainException('Alumne no matriculat en el grup del mòdul optatiu', [
                'certificat_id' => $certificat->id,
                'alumne' => $alumne,
            ]);
        }

        $errors = $this->certificats()->validationErrorsForAlumno($certificat, $alumne);
        if ($errors !== []) {
            foreach ($errors as $error) {
                Alert::danger($error);
            }

            return redirect()->route('modulOptatiuCertificat.show', ['moduloGrupo' => $certificat->idModuloGrupo]);
        }

        return $this->certificats()->pdf($certificat, $alumne)->stream();
    }

    /**
     * Retorna un mòdul-grup gestionable pel professor autenticat.
     *
     * @param int|string $moduloGrupo
     * @throws NotFoundDomainException
     */
    private function allowedModuloGrupo($moduloGrupo): Modulo_grupo
    {
        $modul = Modulo_grupo::query()
            ->with('Grupo.Alumnos', 'ModuloCiclo.Modulo')
            ->findOrFail((int) $moduloGrupo);

        if (!$this->certificats()->canManage($modul, (string) authUser()->dni)) {
            throw new NotFoundDomainException('Mòdul no assignat al professor', [
                'modulo_grupo_id' => $moduloGrupo,
                'profesor' => authUser()->dni,
            ]);
        }

        return $modul;
    }

    private function certificats(): ModuloOptatiuCertificatService
    {
        return $this->certificatService ??= app(ModuloOptatiuCertificatService::class);
    }
}
