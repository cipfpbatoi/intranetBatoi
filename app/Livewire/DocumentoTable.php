<?php

namespace Intranet\Livewire;

use Intranet\Entities\Documento;
use Intranet\Services\Document\TipoDocumentoService;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentoTable extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $tipoDocumento = '';
    public string $curso = '';
    public string $propietario = '';
    public string $tags = '';
    public int $perPage = 25;
    public int $page = 1;

    public array $tipoOptions = [];
    public array $cursoOptions = [];

    protected array $queryString = [
        'search' => ['except' => ''],
        'tipoDocumento' => ['except' => ''],
        'curso' => ['except' => ''],
        'propietario' => ['except' => ''],
        'tags' => ['except' => ''],
        'perPage' => ['except' => 25],
    ];

    public function mount(): void
    {
        if (!$this->isDireccion()) {
            $this->propietario = AuthUser()->FullName;
        }

        $configTipos = TipoDocumentoService::allPestana();
        $bdTipos = Documento::query()
            ->select('tipoDocumento')
            ->distinct()
            ->orderBy('tipoDocumento')
            ->pluck('tipoDocumento')
            ->filter();

        $tipoOptions = [];
        foreach ($configTipos as $key => $label) {
            $tipoOptions[$key] = $label;
        }
        foreach ($bdTipos as $tipo) {
            $tipoOptions[$tipo] = $configTipos[$tipo] ?? $tipo;
        }
        $this->tipoOptions = $tipoOptions;

        $this->cursoOptions = Documento::query()
            ->select('curso')
            ->distinct()
            ->orderBy('curso', 'desc')
            ->limit(12)
            ->pluck('curso')
            ->toArray();
    }

    public function updating($name): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Documento::query()
            ->select([
                'id',
                'tipoDocumento',
                'descripcion',
                'curso',
                'idDocumento',
                'propietario',
                'created_at',
                'grupo',
                'tags',
                'ciclo',
                'modulo',
                'detalle',
                'fichero',
                'rol',
                'activo',
            ])
            ->orderBy('curso', 'desc');

        $roles = RolesUser(AuthUser()->rol);
        $isDireccion = $this->isDireccion();

        if ($isDireccion) {
            $query->whereIn('rol', $roles);
        } else {
            $this->propietario = AuthUser()->FullName;
            $query->where('propietario', AuthUser()->FullName)
                ->whereIn('rol', $roles);
        }

        $search = trim($this->search);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                foreach ($this->searchableFields() as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        if ($this->tipoDocumento !== '') {
            $query->where('tipoDocumento', $this->tipoDocumento);
        }

        if ($this->curso !== '') {
            $query->where('curso', $this->curso);
        }

        if ($isDireccion && $this->propietario !== '') {
            $query->where('propietario', 'like', "%{$this->propietario}%");
        }

        if ($this->tags !== '') {
            $query->where('tags', 'like', "%{$this->tags}%");
        }

        $documentos = $query->paginate($this->perPage);

        return view('livewire.documento-table', [
            'documentos' => $documentos,
            'isDireccion' => $isDireccion,
        ]);
    }

    private function searchableFields(): array
    {
        return [
            'tipoDocumento',
            'descripcion',
            'curso',
            'idDocumento',
            'propietario',
            'created_at',
            'grupo',
            'tags',
            'ciclo',
            'modulo',
            'detalle',
            'fichero',
        ];
    }

    private function isDireccion(): bool
    {
        return userIsAllow(config('roles.rol.direccion'));
    }
}
