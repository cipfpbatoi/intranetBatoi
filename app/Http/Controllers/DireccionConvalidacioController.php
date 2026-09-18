<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Intranet\Application\Convalidacio\ConvalidacioException;
use Intranet\Application\Convalidacio\ConvalidacioQueryService;
use Intranet\Application\Convalidacio\ConvalidacioService;
use Intranet\Entities\Convalidacio;
use Intranet\Entities\Profesor;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Pantalles i accions de revisió reservades a Direcció. */
class DireccionConvalidacioController extends Controller
{
    public function __construct(
        private readonly ConvalidacioService $service,
        private readonly ConvalidacioQueryService $queries
    ) {
        parent::__construct();
    }

    /** Mostra el panell amb filtres d'estat i origen. */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'estat' => ['nullable', 'string', Rule::in(array_keys(Convalidacio::estatOptions()))],
            'origen' => ['nullable', 'string', Rule::in(array_keys(Convalidacio::origenOptions()))],
        ]);

        return view('intranet.convalidacions.direccion.index', [
            'sollicituds' => $this->queries->sollicitudsDireccion($filters['estat'] ?? null, $filters['origen'] ?? null),
            'estats' => Convalidacio::estatOptions(),
            'origens' => Convalidacio::origenOptions(),
            'filters' => $filters,
        ]);
    }

    /** Mostra totes les peticions d'una sol·licitud. */
    public function show(int $sollicitud): View
    {
        $model = $this->queries->sollicitudDetail($sollicitud) ?? abort(404);
        Gate::forUser($this->profesor())->authorize('view', $model);

        return view('intranet.convalidacions.direccion.show', ['sollicitud' => $model, 'estats' => Convalidacio::estatOptions()]);
    }

    /** Canvia l'estat d'una única petició. */
    public function resolve(Request $request, Convalidacio $convalidacio): RedirectResponse
    {
        Gate::forUser($this->profesor())->authorize('resolve', $convalidacio);
        $validated = $request->validate([
            'estat' => ['required', 'string', Rule::in(array_keys(Convalidacio::estatOptions()))],
            'observacions' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->service->revisar($convalidacio, $this->profesor(), $validated['estat'], $validated['observacions'] ?? null);
        } catch (ConvalidacioException $exception) {
            return back()->withErrors(['estat' => $exception->getMessage()]);
        }

        return back()->with('success', 'Petició actualitzada correctament.');
    }

    /** Descarrega un adjunt autoritzat des del disc privat. */
    public function download(Convalidacio $convalidacio): StreamedResponse
    {
        Gate::forUser($this->profesor())->authorize('viewPeticio', $convalidacio);
        abort_unless($convalidacio->document_path && Storage::disk('convalidacions')->exists($convalidacio->document_path), 404);

        return Storage::disk('convalidacions')->download($convalidacio->document_path, $convalidacio->document_original_name);
    }

    /** Retorna la identitat autenticada pel guard de professorat. */
    private function profesor(): Profesor
    {
        $profesor = auth('profesor')->user();
        abort_unless($profesor instanceof Profesor, 401);

        return $profesor;
    }
}
