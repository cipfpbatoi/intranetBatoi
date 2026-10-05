<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Intranet\Application\Convalidacio\ConvalidacioAutomaticaService;
use Intranet\Application\Convalidacio\ConvalidacioException;
use Intranet\Application\Convalidacio\ConvalidacioReglesManager;
use Intranet\Entities\Profesor;

/** Gestiona el catàleg de regles i la seua aplicació per Direcció. */
class DireccionConvalidacioReglesController extends Controller
{
    public function __construct(
        private readonly ConvalidacioReglesManager $regles,
        private readonly ConvalidacioAutomaticaService $automatitzacions
    ) {
        parent::__construct();
    }

    /** Mostra el catàleg actiu i els casos que es poden resoldre. */
    public function index(): View
    {
        return view('intranet.convalidacions.direccion.regles', [
            'preview' => $this->automatitzacions->previsualitzar(),
        ]);
    }

    /** Valida i substituïx el catàleg de regles actiu. */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'yaml' => ['required', 'file', 'max:2048', 'extensions:yaml,yml'],
        ]);

        try {
            $catalog = $this->regles->guardar($validated['yaml']);
        } catch (ConvalidacioException $exception) {
            return back()->withErrors(['yaml' => $exception->getMessage()]);
        }

        return redirect()->route('convalidacions.direction.rules.index')->with(
            'success',
            'Catàleg ' . $catalog['version'] . ' carregat correctament.'
        );
    }

    /** Aplica de manera idempotent els casos verificables pel catàleg actiu. */
    public function apply(): RedirectResponse
    {
        $responsable = auth('profesor')->user();
        abort_unless($responsable instanceof Profesor, 401);

        $resultat = $this->automatitzacions->aplicar($responsable);

        return redirect()->route('convalidacions.direction.rules.index')->with('resultatAutomatic', $resultat);
    }
}
