<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\ModalController;

use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\Projecte;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\ProyectoRequest;

/**
 * Class PanelProjecteController
 * @package Intranet\Http\Controllers
 */
class PanelProjecteController extends ModalController
{
    private ?ProfesorService $profesorService = null;
    private ?GrupoService $grupoService = null;
    const TUTOR = 'roles.rol.tutor';
    /**
     * @var string
     */
    protected $model = 'Projecte';
    /**
     * @var array
     */
    protected $gridFields = ['alumne','titol', 'status', 'defensa', 'hora_defensa'];
    protected $formFields = [
        'grup' => ['type' => 'hidden'],
        'estat' => ['type' => 'hidden'],
        'idAlumne' => ['type' => 'select'],
        'titol' => ['type' => 'text'],
        'descripcio' => ['type' => 'textarea'],
        'objectius' => ['type' => 'textarea'],
        'resultats'=> ['type' => 'textarea'],
        'aplicacions' => ['type' => 'textarea'],
        'recursos' => ['type' => 'textarea'],
        'defensa' => ['type' => 'date'],
        'hora_defensa' => ['type' => 'time'],
     ];
    protected $parametresVista = ['modal' => ['defensa']];

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function myTutorGroup()
    {
        return $this->grupos()->byTutorOrSubstitute(AuthUser()->dni, AuthUser()->sustituye_a);
    }

    /**
     * Recupera els projectes del grup del tutor autenticat.
     */
    public function search()
    {
        $miGrupo = $this->myTutorGroup();
        if ($miGrupo === null) {
            return collect();
        }
        $alumnos = hazArray($miGrupo->Alumnos,'nia','nia');
        return Projecte::whereIn('idAlumne', $alumnos)->get();
    }

    public function store(ProyectoRequest $request)
    {
        $this->authorize('create', Projecte::class);
        $miGrupo = $this->myTutorGroup();
        if ($miGrupo === null) {
            return back()->withErrors('No tens grup assignat');
        }
        $request->request->add(['grup' => $miGrupo->codigo,'estat'=>1]);
        $this->persist($request);

        return back();
    }

    /**
     * @param ProyectoRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProyectoRequest $request, $id)
    {
        $projecte = $this->findModelOrFail(
            Projecte::class,
            $id,
            'Projecte no trobat',
            ['projecte_id' => $id]
        );
        $this->authorize('update', $projecte);
        $this->persist($request, (int) $id);
        return back();
    }

    /**
     * Valida una proposta de projecte.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function check($id)
    {
        $projecte = $this->findModelOrFail(
            Projecte::class,
            $id,
            'Projecte no trobat',
            ['projecte_id' => $id]
        );
        $this->authorize('check', $projecte);
        $projecte->estat = 2;
        $projecte->save();
        return back();
    }

    /**
     * Elimina una proposta de projecte.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        $elemento = $this->findModelOrFail(
            Projecte::class,
            $id,
            'Projecte no trobat',
            ['projecte_id' => $id]
        );
        $this->authorize('delete', $elemento);
        if ($elemento) {
            $elemento->delete();
        }
        return back();
    }


    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('projectes.sendP',[ 'class' => 'btn-info '  ] ));

        $this->panel->setBoton('index', new BotonBasico('projectes.actaP',[ 'class' => 'btn-warning '  ] ));
        $this->panel->setBoton('index', new BotonBasico('projectes.actaE',[ 'class' => 'btn-success '  ] ));
        $this->panel->setBoton('index', new BotonBasico('projecte.create'));

        //$this->panel->setBoton('grid', new BotonImg('projecte.show'));
        $this->panel->setBoton('grid', new BotonImg('projecte.edit', ['roles' => config(self::TUTOR), 'where' => ['estat', '<' , '3']]));
        $this->panel->setBoton('grid',
            new BotonImg('projectes.delete', ['roles' => config(self::TUTOR), 'where' => ['estat', '< ', '2']]));
        $this->panel->setBoton('grid', new BotonImg('projecte.pdf', ['roles' => config(self::TUTOR)]));
        $this->panel->setBoton('grid', new BotonImg('projecte.check', ['img' => 'fa-check','roles' => config(self::TUTOR), 'where' => ['estat', '==', '1']]));
    }

}
