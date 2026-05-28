<?php

namespace Intranet\Http\Controllers;

use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Poll\Poll;

/**
 * Controlador per mostrar les enquestes actives que l'usuari ha de respondre.
 * Filtra per tipus d'usuari (alumne/professor) i per curs del grup.
 */
class PanelPollResponseController extends PollController
{

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('poll.do', ['img' => 'fa-check-square-o']));
    }

    /**
     * Retorna les enquestes actives que corresponen a l'usuari autenticat,
     * filtrades per tipus d'usuari i per curs del seu grup.
     *
     * @return array
     */
    protected function search(): array
    {
        $polls = Poll::all();
        $key = isset(AuthUser()->nia) ? 'nia' : 'dni';
        $activas = $polls->where('state', 'Activa')->where('keyUser', $key);

        $userCurs = $this->getUserCurs();
        
        if ($userCurs !== null) {
            $activas = $activas->filter(
                fn($poll) => $poll->curs === null || (int) $poll->curs === $userCurs
            );
        }

        $usuario = [];
        foreach ($activas as $activa) {
            $modelo = $activa->modelo;
            if ($modelo::has()) {
                $usuario[] = $activa;
            }
        }
        return $usuario;
    }

}
