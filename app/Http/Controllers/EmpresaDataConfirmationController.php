<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Intranet\Application\Empresa\EmpresaDataConfirmationService;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Envia sol·licituds de confirmació a empreses des del panell del tutor.
 */
class EmpresaDataConfirmationController extends Controller
{
    public function __construct(private readonly EmpresaDataConfirmationService $service)
    {
        parent::__construct();
        $this->middleware('role:profesor');
    }

    /**
     * Retorna una opció per empresa i desmarca les que ja han rebut una sol·licitud.
     */
    public function options(): JsonResponse
    {
        $tutor = auth('profesor')->user();
        $options = $this->service
            ->selectableCompaniesForTutor((string) $tutor->dni)
            ->map(fn ($item): array => [
                'id' => $item->id,
                'texto' => $item->selection_text,
                'marked' => $item->selection_marked,
            ])
            ->values();

        return response()->json(['data' => $options]);
    }

    /**
     * Agrupa la selecció per empresa i envia un correu per cadascuna.
     */
    public function store(Request $request): RedirectResponse
    {
        $tutor = auth('profesor')->user();
        $companyIds = collect($request->except(['_token', 'checkall']))
            ->filter(fn ($value): bool => $value === 'on')
            ->keys()
            ->filter(fn ($key): bool => ctype_digit((string) $key))
            ->map(fn ($key): int => (int) $key)
            ->all();

        $sent = $this->service->sendForTutor($companyIds, $tutor);
        Alert::info("S'han enviat {$sent->count()} sol·licituds de confirmació de dades.");

        return redirect()->route('colaboracion.mias');
    }
}
