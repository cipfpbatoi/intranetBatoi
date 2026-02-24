<?php

declare(strict_types=1);

namespace Intranet\Application\Poll;

use Illuminate\Http\Request;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Departamento;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\Vote;

class PollWorkflowService
{
    public function prepareSurvey(int|string $id, object $user): ?array
    {
        $poll = Poll::find($id);
        if (!$poll) {
            return null;
        }

        $modelo = $poll->modelo;
        $quests = $modelo::loadPoll($this->loadPreviousVotes($poll, $user));

        return [
            'poll' => $poll,
            'quests' => $quests,
        ];
    }

    public function saveSurvey(Request $request, int|string $id, object $user): bool
    {
        $poll = Poll::find($id);
        if (!$poll) {
            return false;
        }

        $modelo = $poll->modelo;
        $quests = $modelo::loadPoll($this->loadPreviousVotes($poll, $user));

        foreach ($poll->Plantilla->options as $question => $option) {
            $i = 0;
            foreach ($quests ?? [] as $quest) {
                if (isset($quest['option2'])) {
                    foreach ($quest['option2'] ?? [] as $profesores) {
                        foreach ($profesores as $dni) {
                            $i++;
                            $field = 'option' . ($question + 1) . '_' . $i;
                            $this->saveVote($poll, $option, $quest['option1']->id, $dni, $request->$field, $user);
                        }
                    }
                } else {
                    $field = 'option' . ($question + 1) . '_' . $quest['option1']->id;
                    $this->saveVote($poll, $option, $quest['option1']->id, null, $request->$field, $user);
                }
            }
        }

        return true;
    }

    public function myVotes(int|string $id): ?array
    {
        $poll = Poll::find($id);
        if (!$poll) {
            return null;
        }

        $modelo = $poll->modelo;
        $myVotes = $modelo::loadVotes($id);

        return [
            'poll' => $poll,
            'myVotes' => $myVotes,
            'myGroupsVotes' => $modelo::loadGroupVotes($id),
            'options_numeric' => $poll->Plantilla->options->where('scala', '>', 0),
            'options_text' => $poll->Plantilla->options->where('scala', '=', 0),
            'options' => $poll->Plantilla->options,
        ];
    }

    public function allVotes(int|string $id, GrupoService $grupoService): ?array
    {
        $poll = Poll::find($id);
        if (!$poll) {
            return null;
        }

        $modelo = $poll->modelo;
        $optionsNumeric = $poll->Plantilla->options->where('scala', '>', 0);
        $allVotes = Vote::allNumericVotes($id)->get();
        $option1 = $allVotes->groupBy(['idOption1', 'option_id']);
        $option2 = $allVotes->groupBy(['idOption2', 'option_id']);

        $votes = [];
        $this->initValues($votes, $optionsNumeric, $grupoService);
        $votes['all'] = $allVotes->groupBy('option_id');
        $modelo::aggregate($votes, $option1, $option2);

        $stats = [
            'all' => [],
            'grup' => [],
            'cicle' => [],
            'departament' => [],
        ];

        foreach ($votes['all'] as $optionId => $optionVote) {
            $stats['all'][$optionId] = [
                'avg' => round($optionVote->avg('value'), 1),
                'count' => $optionVote->groupBy('user_id')->count(),
            ];
        }

        foreach (['grup', 'cicle', 'departament'] as $bucket) {
            foreach ($votes[$bucket] as $nameGroup => $groupVotes) {
                foreach ($groupVotes as $optionId => $optionVote) {
                    $stats[$bucket][$nameGroup][$optionId] = [
                        'avg' => round($optionVote->avg('value'), 1),
                        'count' => $optionVote->groupBy('user_id')->count(),
                    ];
                }
            }
        }

        $hasVotes = [
            'grup' => [],
            'cicle' => [],
            'departament' => [],
        ];

        foreach (['grup', 'cicle', 'departament'] as $bucket) {
            foreach ($stats[$bucket] as $nameGroup => $groupStats) {
                $hasVotes[$bucket][$nameGroup] = false;
                foreach ($groupStats as $stat) {
                    if ($stat['count'] > 0) {
                        $hasVotes[$bucket][$nameGroup] = true;
                        break;
                    }
                }
            }
        }

        return [
            'poll' => $poll,
            'votes' => $votes,
            'options_numeric' => $optionsNumeric,
            'hasVotes' => $hasVotes,
            'stats' => $stats,
        ];
    }

    private function userKey(Poll $poll, object $user): string
    {
        $key = $poll->keyUser;
        if ($poll->anonymous) {
            return hash('md5', $user->$key);
        }

        return (string) $user->$key;
    }

    private function loadPreviousVotes(Poll $poll, object $user): array
    {
        return hazArray(
            Vote::where('user_id', '=', $this->userKey($poll, $user))
                ->where('idPoll', $poll->id)
                ->get(),
            'idOption1',
            'idOption1'
        );
    }

    private function saveVote(
        Poll $poll,
        mixed $option,
        mixed $option1,
        mixed $option2,
        mixed $value,
        object $user
    ): void {
        if ($value === '' || $value === '0') {
            return;
        }

        $vote = new Vote();
        $vote->idPoll = $poll->id;
        $vote->user_id = $poll->anonymous ? hash('md5', $user->id) : $user->id;
        $vote->option_id = $option->id;
        $vote->idOption1 = $option1;
        $vote->idOption2 = $option2;

        if ((int) $option->scala === 0) {
            $vote->text = $value;
        } else {
            $vote->value = voteValue($option2, $value);
        }

        $vote->save();
    }

    private function initValues(array &$votes, mixed $options, GrupoService $grupoService): void
    {
        $grupos = $grupoService->all();
        $ciclos = Ciclo::all();
        $departamentos = Departamento::all();

        foreach ($options as $value) {
            foreach ($grupos as $grupo) {
                $votes['grup'][$grupo->codigo][$value->id] = collect();
            }
            foreach ($ciclos as $ciclo) {
                $votes['cicle'][$ciclo->id][$value->id] = collect();
            }
            foreach ($departamentos as $departamento) {
                $votes['departament'][$departamento->id][$value->id] = collect();
            }
        }
    }
}
