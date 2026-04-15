<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\Http\Requests\OptionStoreRequest;
use Intranet\Entities\Poll\Option;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Controlador de preguntes dins d'una plantilla d'enquesta.
 */
class OptionController extends ModalController
{
    /**
     * @var string
     */
    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Option';
    /**
     * @var array
     */
    protected $gridFields = [ 'question','scala'];

    /**
     * @param OptionStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(OptionStoreRequest $request)
    {
        $this->authorize('create', Option::class);
        $request->merge($this->normalizedPayload($request));
        $this->persist($request);
        return redirect()->route('ppoll.show', ['id' => $request->ppoll_id]);
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $option = Option::findOrFail((int) $id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Opció no trobada', ['option_id' => $id]);
        }
        $this->authorize('delete', $option);
        $poll = $option->ppoll_id;
        parent::destroy($id);
        return redirect()->route('ppoll.show', ['id' => $poll]);
    }

    /**
     * Normalitza el formulari segons el tipus de resposta seleccionat.
     *
     * @return array<string, int|string|null>
     */
    private function normalizedPayload(OptionStoreRequest $request): array
    {
        $kind = (string) $request->input('kind', 'numeric');

        return [
            'scala' => $kind === 'numeric' ? (int) $request->input('scala') : 0,
            'choices' => $kind === 'select' ? $this->normalizeChoices((string) $request->input('choices', '')) : null,
            'idCiclo' => $request->filled('idCiclo') ? (int) $request->input('idCiclo') : null,
        ];
    }

    /**
     * Netega la llista d'opcions eliminant línies buides i duplicades.
     */
    private function normalizeChoices(string $choices): ?string
    {
        $lines = preg_split('/\r\n|\r|\n/', $choices) ?: [];
        $clean = [];

        foreach ($lines as $line) {
            $value = trim($line);
            if ($value === '' || in_array($value, $clean, true)) {
                continue;
            }

            $clean[] = $value;
        }

        return count($clean) ? implode("\n", $clean) : null;
    }
}
