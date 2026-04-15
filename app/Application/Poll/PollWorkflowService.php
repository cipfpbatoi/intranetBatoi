<?php

declare(strict_types=1);

namespace Intranet\Application\Poll;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Departamento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Poll\Option;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\Vote;

/**
 * Orquestra la preparació, persistència i explotació de respostes d'enquestes.
 */
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
        $options = $this->surveyOptions($poll, $user);

        return [
            'poll' => $poll,
            'quests' => $quests,
            'options' => $options,
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
        $options = $this->surveyOptions($poll, $user);

        foreach ($options as $question => $option) {
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

    public function myVotes(int|string $id, ?object $user = null): ?array
    {
        $poll = Poll::find($id);
        if (!$poll) {
            return null;
        }

        $modelo = $poll->modelo;
        $myVotes = $modelo::loadVotes($id);
        $options = $this->surveyOptions($poll, $user);

        return [
            'poll' => $poll,
            'myVotes' => $myVotes,
            'myGroupsVotes' => $modelo::loadGroupVotes($id),
            'options_numeric' => $options->filter(fn(Option $option): bool => $option->isNumericType()),
            'options_text' => $options->filter(fn(Option $option): bool => $option->isTextType()),
            'options_select' => $options->filter(fn(Option $option): bool => $option->isSelectType()),
            'options' => $options,
        ];
    }

    public function allVotes(int|string $id, GrupoService $grupoService): ?array
    {
        $poll = Poll::find($id);
        if (!$poll) {
            return null;
        }

        $modelo = $poll->modelo;
        $options = $poll->Plantilla->options;
        $optionsNumeric = $options->filter(fn(Option $option): bool => $option->isNumericType());
        $optionsSelect = $options->filter(fn(Option $option): bool => $option->isSelectType());

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
            $avg = $optionVote->avg('value');
            $stats['all'][$optionId] = [
                'avg' => $avg !== null ? round((float) $avg, 1) : null,
                'count' => $optionVote->groupBy('user_id')->count(),
            ];
        }

        foreach (['grup', 'cicle', 'departament'] as $bucket) {
            foreach ($votes[$bucket] as $nameGroup => $groupVotes) {
                foreach ($groupVotes as $optionId => $optionVote) {
                    $avg = $optionVote->avg('value');
                    $stats[$bucket][$nameGroup][$optionId] = [
                        'avg' => $avg !== null ? round((float) $avg, 1) : null,
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

        $selectVotes = [];
        $this->initValues($selectVotes, $optionsSelect, $grupoService);
        $selectStats = [
            'all' => [],
            'grup' => [],
            'cicle' => [],
            'departament' => [],
        ];
        $selectHasVotes = [
            'grup' => [],
            'cicle' => [],
            'departament' => [],
        ];

        if ($optionsSelect->isNotEmpty()) {
            $allSelectVotes = Vote::allSelectVotes($id)->get();
            $selectVotes['all'] = $allSelectVotes->groupBy('option_id');
            $modelo::aggregate(
                $selectVotes,
                $allSelectVotes->groupBy(['idOption1', 'option_id']),
                $allSelectVotes->groupBy(['idOption2', 'option_id'])
            );

            foreach ($selectVotes['all'] as $optionId => $optionVote) {
                $selectStats['all'][$optionId] = $this->countChoices($optionVote);
            }

            foreach (['grup', 'cicle', 'departament'] as $bucket) {
                foreach ($selectVotes[$bucket] as $nameGroup => $groupVotes) {
                    foreach ($groupVotes as $optionId => $optionVote) {
                        $selectStats[$bucket][$nameGroup][$optionId] = $this->countChoices($optionVote);
                    }
                }
            }

            foreach (['grup', 'cicle', 'departament'] as $bucket) {
                foreach ($selectStats[$bucket] as $nameGroup => $groupStats) {
                    $selectHasVotes[$bucket][$nameGroup] = false;
                    foreach ($groupStats as $counts) {
                        if (array_sum($counts) > 0) {
                            $selectHasVotes[$bucket][$nameGroup] = true;
                            break;
                        }
                    }
                }
            }
        }

        return [
            'poll' => $poll,
            'votes' => $votes,
            'options_numeric' => $optionsNumeric,
            'options_select' => $optionsSelect,
            'hasVotes' => $hasVotes,
            'stats' => $stats,
            'selectStats' => $selectStats,
            'selectHasVotes' => $selectHasVotes,
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
        if ($this->shouldSkipVote($option, $value)) {
            return;
        }

        $vote = new Vote();
        $vote->idPoll = $poll->id;
        $vote->user_id = $poll->anonymous ? hash('md5', $user->id) : $user->id;
        $vote->option_id = $option->id;
        $vote->idOption1 = $option1;
        $vote->idOption2 = $option2;

        if ($option->isNumericType()) {
            $vote->value = voteValue($option2, $value);
        } else {
            $vote->text = $option->isSelectType()
                ? $this->normalizeSelectValue($option, $value)
                : $value;
        }

        $vote->save();
    }

    /**
     * Decideix si una resposta s'ha d'ignorar per estar buida o no ser vàlida.
     */
    private function shouldSkipVote(mixed $option, mixed $value): bool
    {
        if ($option->isNumericType()) {
            return $value === '' || $value === '0' || $value === null;
        }

        if ($value === null || trim((string) $value) === '') {
            return true;
        }

        if (!$option->isSelectType()) {
            return false;
        }

        return $this->normalizeSelectValue($option, $value) === null;
    }

    /**
     * Retorna la selecció només si forma part de les opcions configurades.
     */
    private function normalizeSelectValue(mixed $option, mixed $value): ?string
    {
        $selected = trim((string) $value);
        if ($selected === '') {
            return null;
        }

        foreach ($option->choice_values as $choice) {
            if ($choice === $selected) {
                return $selected;
            }
        }

        return null;
    }

    /**
     * Recompte de respostes per cada text seleccionat.
     *
     * @param iterable<mixed> $votes
     * @return array<string, int>
     */
    private function countChoices(iterable $votes): array
    {
        $counts = [];

        foreach ($votes as $vote) {
            if (!isset($vote->text) || trim((string) $vote->text) === '') {
                continue;
            }

            $counts[$vote->text] = ($counts[$vote->text] ?? 0) + 1;
        }

        ksort($counts);

        return $counts;
    }

    private function initValues(array &$votes, mixed $options, GrupoService $grupoService): void
    {
        $grupos = $grupoService->all();
        $ciclos = Ciclo::all();
        $departamentos = Departamento::all();

        $votes['grup'] = $votes['grup'] ?? [];
        $votes['cicle'] = $votes['cicle'] ?? [];
        $votes['departament'] = $votes['departament'] ?? [];

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

    /**
     * Retorna les preguntes visibles per a l'usuari actual, reindexades
     * perquè el wizard i els noms dels camps siguen consistents.
     */
    private function surveyOptions(Poll $poll, ?object $user = null): Collection
    {
        $options = $poll->Plantilla->options;

        if (!$this->shouldFilterOptionsByCycle($user)) {
            return $options->values();
        }

        $cycleId = $this->resolveUserCycleId($user);

        return $options
            ->filter(fn(Option $option): bool => $option->matchesCycle($cycleId))
            ->values();
    }

    /**
     * Indica si el qüestionari s'ha de filtrar per cicle del respondedor.
     */
    private function shouldFilterOptionsByCycle(?object $user): bool
    {
        if ($user === null) {
            return false;
        }

        return isset($user->nia) || !empty($user->GrupoTutoria ?? null);
    }

    /**
     * Resol el cicle del respondedor a partir del seu grup o tutoria.
     */
    private function resolveUserCycleId(?object $user): ?int
    {
        if ($user === null) {
            return null;
        }

        $grupos = $user->Grupo ?? null;
        if (isset($user->nia) && $grupos && method_exists($grupos, 'first')) {
            $grupo = $grupos->first();
            if ($grupo?->idCiclo !== null) {
                return (int) $grupo->idCiclo;
            }

            return null;
        }

        $grupoTutoria = $user->GrupoTutoria ?? null;
        if ($grupoTutoria) {
            $grupo = Grupo::find($grupoTutoria);
            if ($grupo?->idCiclo !== null) {
                return (int) $grupo->idCiclo;
            }
        }

        return null;
    }
}
