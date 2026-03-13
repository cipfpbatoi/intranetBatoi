<?php

namespace Intranet\Http\Controllers;

use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\PPoll;

/**
 * Controlador per mostrar els resultats d'enquestes finalitzades als professors.
 * Filtra per tipus d'enquesta segons el rol i per curs del grup del professor.
 */
class PanelPollResultController extends PollController
{
    protected $gridFields = ['id', 'title'];

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('poll.show', ['img' => 'fa-eye']));
    }

    /**
     * Retorna les enquestes finalitzades visibles per a l'usuari autenticat,
     * filtrades per rol i per curs del seu grup.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function search()
    {
        if (esRol(AuthUser()->rol, config('roles.rol.tutor'))) {
            $ppolls = hazArray(PPoll::all(), 'id', 'id');
        } else {
            $ppolls = hazArray(PPoll::where('what', 'Profesor')->get(), 'id', 'id');
        }

        $query = Poll::whereIn('idPPoll', $ppolls)->where('hasta', '<=', now());

        $userCurs = $this->getUserCurs();
        
        if ($userCurs !== null) {
            $query->where(function ($q) use ($userCurs): void {
                $q->whereNull('curs')->orWhere('curs', $userCurs);
            });
        }

        return $query->get();
    }
}
