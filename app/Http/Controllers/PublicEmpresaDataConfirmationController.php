<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intranet\Application\Empresa\EmpresaDataConfirmationService;

/**
 * Mostra i processa el formulari públic limitat per un token de confirmació.
 */
class PublicEmpresaDataConfirmationController extends Controller
{
    public function __construct(private readonly EmpresaDataConfirmationService $service)
    {
        parent::__construct();
    }

    /**
     * Mostra les dades incloses en la sol·licitud o el seu estat final.
     */
    public function show(string $token)
    {
        $confirmation = $this->service->findByToken($token);
        $collaborations = $this->service->collaborations($confirmation);
        $centers = $this->service->centers($confirmation);

        return view('public.empresa-data-confirmation', compact(
            'confirmation',
            'collaborations',
            'centers',
            'token'
        ));
    }

    /**
     * Valida i guarda la confirmació sense crear una sessió d'usuari.
     */
    public function update(Request $request, string $token)
    {
        $confirmation = $this->service->findByToken($token);
        $centerIds = $this->service->centers($confirmation)->pluck('id')->map(fn ($id): int => (int) $id)->all();
        $collaborationIds = $this->service->collaborations($confirmation)->pluck('id')->all();
        $instructorDnis = $this->service->instructors($confirmation)->pluck('dni')->all();

        $data = $request->validate([
            'empresa.nombre' => ['required', 'string', 'max:100'],
            'empresa.cif' => ['required', 'string', 'max:20'],
            'empresa.email' => ['required', 'email', 'max:255'],
            'empresa.telefono' => ['required', 'string', 'max:30'],
            'empresa.direccion' => ['required', 'string', 'max:100'],
            'empresa.localidad' => ['required', 'string', 'max:50'],
            'empresa.gerente' => ['required', 'string', 'max:150'],
            'empresa.nif_gerente' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9][A-Za-z0-9-]{7,19}$/'],
            'confirmed_colaborations' => ['required', 'array'],
            'confirmed_colaborations.*' => [Rule::in($collaborationIds)],
            'centers' => ['required', 'array'],
            'centers.*.nombre' => ['required', 'string', 'max:100'],
            'centers.*.email' => ['nullable', 'email', 'max:255'],
            'centers.*.telefono' => ['nullable', 'string', 'max:30'],
            'centers.*.direccion' => ['required', 'string', 'max:100'],
            'centers.*.localidad' => ['required', 'string', 'max:50'],
            'centers.*.horarios' => ['nullable', 'string', 'max:255'],
            'instructors' => ['required', 'array'],
            'instructors.existing' => ['nullable', 'array'],
            'instructors.existing.*.dni' => ['required', 'string', Rule::in($instructorDnis)],
            'instructors.existing.*.name' => ['required', 'string', 'max:100'],
            'instructors.existing.*.surnames' => ['required', 'string', 'max:150'],
            'instructors.existing.*.email' => ['required', 'email', 'max:255'],
            'instructors.existing.*.telefono' => ['nullable', 'string', 'max:30'],
            'instructor_removals' => ['nullable', 'array'],
            'instructor_removals.*' => ['nullable', 'array'],
            'instructor_removals.*.*' => ['required', 'string', Rule::in($instructorDnis)],
            'instructors.new.dni' => ['nullable', 'string', 'max:20'],
            'instructors.new.name' => ['nullable', 'required_with:instructors.new.dni', 'string', 'max:100'],
            'instructors.new.surnames' => ['nullable', 'required_with:instructors.new.dni', 'string', 'max:150'],
            'instructors.new.email' => ['nullable', 'required_with:instructors.new.dni', 'email', 'max:255'],
            'instructors.new.telefono' => ['nullable', 'string', 'max:30'],
            'instructors.new.center_ids' => ['nullable', 'required_with:instructors.new.dni', 'array', 'min:1'],
            'instructors.new.center_ids.*' => [Rule::in($centerIds)],
            'coordinator_dni' => ['required', 'string'],
        ]);

        if (collect(array_keys($data['centers']))->map(fn ($id): int => (int) $id)->sort()->values()->all()
            !== collect($centerIds)->sort()->values()->all()) {
            abort(422, 'Els centres rebuts no corresponen a la sol·licitud.');
        }

        $this->service->confirm($confirmation, $data);

        return redirect()->route('empresa.confirmacio.show', ['token' => $token]);
    }
}
