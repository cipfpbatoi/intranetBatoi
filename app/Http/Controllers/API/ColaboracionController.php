<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Intranet\Application\Seguimiento\SeguimientoService;
use Intranet\Entities\Activity;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Seguimiento;
use Intranet\Services\General\StateService;


class ColaboracionController extends ApiResourceController
{
    /**
     * @var array<string, array{action:string,document:string}>
     */
    private const CONTACT_TYPES = [
        'telefonada' => ['action' => 'phone', 'document' => 'Telefonada'],
        'correu' => ['action' => 'email', 'document' => 'Correu'],
        'visita' => ['action' => 'visita', 'document' => 'Visita'],
        'reunio' => ['action' => 'review', 'document' => 'Reunió'],
        'seguiment' => ['action' => 'book', 'document' => 'Seguiment'],
    ];

    protected $model = 'Colaboracion';

    public function __construct(private readonly SeguimientoService $seguimientoService)
    {
        parent::__construct();
    }

    public function instructores($id)
    {
        $colaboracion = Colaboracion::find($id);
        if ($colaboracion === null) {
            return $this->sendFail(['success' => false, 'message' => 'Colaboració no trobada.'], 404);
        }

        $data = isset($colaboracion->Centro)
            ?$colaboracion->Centro->instructores->sortBy('surnames')
            :[];
        return $this->sendResponse($data, 'OK');
    }

    public function resolve($id)
    {
        return $this->changeState($id, 'resolve');
    }

    public function refuse($id)
    {
        return $this->changeState($id, 'refuse');
    }

    public function unauthorize($id)
    {
        $colaboracion = Colaboracion::find($id);
        if ($colaboracion === null) {
            return $this->sendFail(['success' => false, 'message' => 'Colaboració no trobada.'], 404);
        }

        $staSer = new StateService($colaboracion);
        $staSer->putEstado(1);
        return $this->sendResponse($colaboracion, 'OK');
    }

    public function switch($id, Request $request)
    {
        $colaboracion = Colaboracion::find($id);
        if ($colaboracion === null) {
            return $this->sendFail(['success' => false, 'message' => 'Colaboració no trobada.'], 404);
        }

        $profesor = $request->user('sanctum') ?? $request->user('api');
        if ($profesor === null) {
            return $this->sendError('Unauthorized', 401);
        }

        $colaboracion->tutor = $profesor->dni;
        $colaboracion->save();

        return $this->sendResponse($profesor, 'OK');
    }

    public function telefon($id, Request $request)
    {
        $activity = $this->recordLegacyContact(
            $id,
            'telefonada',
            (string) $request->explicacion
        );

        return $this->sendResponse($activity, 'OK');
    }

    /**
     * Retorna un contacte de col·laboració en format estructurat per al modal.
     */
    public function showContact(string $id)
    {
        [$activity, $seguimiento] = $this->resolveContactRecord($id);

        if ($activity === null && $seguimiento === null) {
            return $this->sendFail(['success' => false, 'message' => 'Contacte no trobat.'], 404);
        }

        return $this->sendResponse($this->buildContactPayload($activity, $seguimiento), 'OK');
    }

    /**
     * Actualitza un contacte de col·laboració ja existent.
     */
    public function updateContact(string $id, Request $request)
    {
        [$activity, $seguimiento] = $this->resolveContactRecord($id);

        if ($activity === null && $seguimiento === null) {
            return $this->sendFail(['success' => false, 'message' => 'Contacte no trobat.'], 404);
        }

        $validated = $request->validate([
            'contact_type' => 'required|string|in:' . implode(',', array_keys(self::CONTACT_TYPES)),
            'resultat' => 'nullable|string|max:120',
            'observacions' => 'nullable|string',
            'proxima_accio' => 'nullable|string|max:255',
            'data_prevista' => 'nullable|date',
        ]);

        $contactType = (string) $validated['contact_type'];
        $document = $this->buildContactDocument(
            $contactType,
            Arr::get($validated, 'resultat'),
            $activity?->document ?? $seguimiento?->title
        );
        $comentari = $this->buildStructuredComment(
            Arr::get($validated, 'observacions'),
            Arr::get($validated, 'proxima_accio'),
            Arr::get($validated, 'data_prevista')
        );
        $mapping = self::CONTACT_TYPES[$contactType];

        if ($activity !== null) {
            $activity->action = $mapping['action'];
            $activity->document = $document;
            $activity->comentari = $comentari;
            $activity->save();
            $seguimiento = $this->seguimientoService->syncFromActivity($activity);
        } elseif ($seguimiento !== null) {
            $seguimiento->contact_type = $mapping['action'];
            $seguimiento->title = $document;
            $seguimiento->comment = $comentari;
            $seguimiento->save();
        }

        return $this->sendResponse($this->buildContactPayload($activity, $seguimiento), 'OK');
    }

    public function alumnat($id, Request $request)
    {
        $activity = Activity::record(
            'review',
            Fct::find($id),
            $request->explicacion,
            null,
            'Seguiment Alumnat'
        );

        return $this->sendResponse($activity, 'OK');
    }

    public function book($id, Request $request)
    {
        $activity = $this->recordLegacyContact(
            $id,
            'seguiment',
            (string) $request->explicacion
        );

        return $this->sendResponse($activity, 'OK');
    }

    public function contact($id, Request $request)
    {
        $colaboracion = Colaboracion::find($id);
        if ($colaboracion === null) {
            return $this->sendFail(['success' => false, 'message' => 'Colaboració no trobada.'], 404);
        }

        $validated = $request->validate([
            'contact_type' => 'required|string|in:' . implode(',', array_keys(self::CONTACT_TYPES)),
            'resultat' => 'required|string|max:120',
            'observacions' => 'nullable|string',
            'proxima_accio' => 'nullable|string|max:255',
            'data_prevista' => 'nullable|date',
        ]);

        $contactType = (string) $validated['contact_type'];
        $mapping = self::CONTACT_TYPES[$contactType];
        $document = $mapping['document'] . ' · ' . trim((string) $validated['resultat']);
        $comentari = $this->buildStructuredComment(
            Arr::get($validated, 'observacions'),
            Arr::get($validated, 'proxima_accio'),
            Arr::get($validated, 'data_prevista')
        );

        $activity = Activity::record(
            $mapping['action'],
            $colaboracion,
            $comentari,
            null,
            $document
        );

        $this->seguimientoService->record(
            $colaboracion,
            $mapping['action'],
            $document,
            $comentari,
            ['source' => 'activities', 'activity_id' => $activity->id]
        );

        return $this->sendResponse($activity, 'OK');
    }

    private function changeState(string|int $id, string $action)
    {
        $colaboracion = Colaboracion::find($id);
        if ($colaboracion === null) {
            return $this->sendFail(['success' => false, 'message' => 'Colaboració no trobada.'], 404);
        }

        $stateService = new StateService($colaboracion);
        $stateService->{$action}();

        return $this->sendResponse($colaboracion, 'OK');
    }

    /**
     * Manté compatibilitat amb els botons legacy, però ja registra un contacte nou cada vegada.
     */
    private function recordLegacyContact(string|int $id, string $contactType, string $explicacion): Activity
    {
        $colaboracion = Colaboracion::find($id);

        $mapping = self::CONTACT_TYPES[$contactType];
        $document = $mapping['document'];
        if ($contactType === 'seguiment') {
            $document = 'Contacte previ';
        }

        $activity = Activity::record(
            $mapping['action'],
            $colaboracion,
            $explicacion,
            null,
            $document
        );

        $this->seguimientoService->record(
            $colaboracion,
            $mapping['action'],
            $document,
            $explicacion,
            ['source' => 'activities', 'activity_id' => $activity->id]
        );

        return $activity;
    }

    private function buildStructuredComment(?string $observacions, ?string $proximaAccio, ?string $dataPrevista): ?string
    {
        $parts = [];

        if (trim((string) $observacions) !== '') {
            $parts[] = 'Observacions: ' . trim((string) $observacions);
        }

        if (trim((string) $proximaAccio) !== '') {
            $parts[] = 'Pròxim pas: ' . trim((string) $proximaAccio);
        }

        if (trim((string) $dataPrevista) !== '') {
            $parts[] = 'Data prevista: ' . trim((string) $dataPrevista);
        }

        return empty($parts) ? null : implode("\n", $parts);
    }

    /**
     * @return array{0:?Activity,1:?Seguimiento}
     */
    private function resolveContactRecord(string $id): array
    {
        if (str_starts_with($id, 'seguimiento-')) {
            $seguimientoId = substr($id, strlen('seguimiento-'));
            $seguimiento = Seguimiento::query()
                ->whereKey($seguimientoId)
                ->where('domain_type', 'Colaboracion')
                ->first();

            return [null, $seguimiento];
        }

        $activity = Activity::query()
            ->whereKey($id)
            ->where('model_class', Colaboracion::class)
            ->first();

        if ($activity === null) {
            return [null, null];
        }

        return [$activity, $this->seguimientoService->findByActivityId((string) $activity->id)];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildContactPayload(?Activity $activity, ?Seguimiento $seguimiento): array
    {
        $action = (string) ($activity?->action ?? $seguimiento?->contact_type ?? '');
        $document = (string) ($activity?->document ?? $seguimiento?->title ?? '');
        $comment = $activity?->comentari ?? $seguimiento?->comment;
        $parsedComment = $this->parseStructuredComment($comment);

        return [
            'id' => (string) ($activity?->id ?? ('seguimiento-' . $seguimiento?->id)),
            'contact_type' => $this->contactTypeFromAction($action),
            'resultat' => $this->extractResultat($document),
            'observacions' => $parsedComment['observacions'],
            'proxima_accio' => $parsedComment['proxima_accio'],
            'data_prevista' => $parsedComment['data_prevista'],
            'document' => $document,
            'comentari' => $comment,
        ];
    }

    /**
     * @return array{observacions:string,proxima_accio:string,data_prevista:string}
     */
    private function parseStructuredComment(?string $comment): array
    {
        $parsed = [
            'observacions' => '',
            'proxima_accio' => '',
            'data_prevista' => '',
        ];

        foreach (preg_split("/\\r\\n|\\r|\\n/", (string) $comment) ?: [] as $line) {
            $trimmed = trim($line);

            if (str_starts_with($trimmed, 'Observacions: ')) {
                $parsed['observacions'] = trim(substr($trimmed, strlen('Observacions: ')));
                continue;
            }

            if (str_starts_with($trimmed, 'Pròxim pas: ')) {
                $parsed['proxima_accio'] = trim(substr($trimmed, strlen('Pròxim pas: ')));
                continue;
            }

            if (str_starts_with($trimmed, 'Data prevista: ')) {
                $parsed['data_prevista'] = trim(substr($trimmed, strlen('Data prevista: ')));
                continue;
            }
        }

        if ($parsed['observacions'] === '' && trim((string) $comment) !== '') {
            $parsed['observacions'] = trim((string) $comment);
        }

        return $parsed;
    }

    private function extractResultat(string $document): string
    {
        $parts = explode('·', $document, 2);

        return isset($parts[1]) ? trim($parts[1]) : '';
    }

    private function contactTypeFromAction(string $action): string
    {
        return match ($action) {
            'phone' => 'telefonada',
            'email' => 'correu',
            'visita' => 'visita',
            'review' => 'reunio',
            'book' => 'seguiment',
            default => 'telefonada',
        };
    }

    private function buildContactDocument(string $contactType, ?string $resultat, ?string $currentDocument = null): string
    {
        $base = self::CONTACT_TYPES[$contactType]['document'];
        if ($contactType === 'seguiment' && str_starts_with((string) $currentDocument, 'Contacte previ')) {
            $base = 'Contacte previ';
        }

        $resultat = trim((string) $resultat);

        return $resultat === '' ? $base : $base . ' · ' . $resultat;
    }
}
