<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\BaseController;

use Illuminate\Support\Facades\Session;

class PanelPGDualController extends BaseController
{
    private ?GrupoService $grupoService = null;

    protected $perfil = 'profesor';
    protected $model = 'Fctdual';
    protected $vista = ['index' => 'FctDual'];
    protected $gridFields = ['id','Nombre','Centro' ,'desde','hasta'];

    public function __construct(?GrupoService $grupoService = null)
    {
        parent::__construct();
        $this->grupoService = $grupoService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }
    
    protected function iniBotones()
    {
        Session::put('redirect', 'PanelPGDualController@indice');
    }
    
    protected function search()
    {
        $grupo = $this->grupos()->find((string) $this->search);
        abort_unless($grupo !== null, 404);
        $this->titulo = ['quien' => $grupo->nombre ];
        return $grupo->codigo;
    }
    
    

}
