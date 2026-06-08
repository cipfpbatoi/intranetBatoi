<?php

namespace Intranet\Http\Controllers;

use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Poll\Option;
use Intranet\Entities\Poll\Poll;

/**
 * Controlador per mostrar les enquestes actives que l'usuari ha de respondre.
 * Filtra per tipus d'usuari (alumne/professor), curs i preguntes visibles.
 */
class PanelPollResponseController extends PollController
{

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('poll.do', ['img' => 'fa-check-square-o']));
    }

    /**
     * Retorna les enquestes actives que corresponen a l'usuari autenticat,
     * filtrades per tipus d'usuari, curs i preguntes aplicables al seu cicle.
     *
     * @return array
     */
    protected function search(): array
    {
        $user = AuthUser();
        $polls = Poll::all();
        $key = isset($user->nia) ? 'nia' : 'dni';
        $activas = $polls->where('state', 'Activa')->where('keyUser', $key);

        $userCurs = $this->getUserCurs();
        $userCicleId = $this->getUserCicleId();
        $mustFilterByCycle = isset($user->nia) || !empty($user->GrupoTutoria ?? null);
        
        if ($userCurs !== null) {
            $activas = $activas->filter(
                fn($poll) => $poll->curs === null || (int) $poll->curs === $userCurs
            );
        }

        $usuario = [];
        foreach ($activas as $activa) {
            $modelo = $activa->modelo;
            if ($modelo::has() && $this->hasVisibleQuestionsForUser($activa, $mustFilterByCycle, $userCicleId)) {
                $usuario[] = $activa;
            }
        }
        return $usuario;
    }

    /**
     * Comprova si l'enquesta té almenys una pregunta visible per a l'usuari.
     */
    private function hasVisibleQuestionsForUser(Poll $poll, bool $mustFilterByCycle, ?int $userCicleId): bool
    {
        if (!$mustFilterByCycle) {
            return $poll->Plantilla->options->isNotEmpty();
        }

        return $poll->Plantilla->options->contains(
            fn(Option $option): bool => $option->matchesCycle($userCicleId)
        );
    }

}
