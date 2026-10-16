<?php

namespace Intranet\Livewire;

use Intranet\Application\AssumpteParticular\AssumpteParticularArchiveService;
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
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public array $tipoOptions = [];
    public array $cursoOptions = [];

    protected array $queryString = [
        'search' => ['except' => ''],
        'tipoDocumento' => ['except' => ''],
        'curso' => ['except' => ''],
        'propietario' => ['except' => ''],
        'tags' => ['except' => ''],
        'perPage' => ['except' => 25],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
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
        if ($name !== 'page') {
            $this->resetPage();
        }
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
                'propietario_dni',
                'created_at',
                'grupo',
                'tags',
                'ciclo',
                'modulo',
                'detalle',
                'fichero',
                'rol',
                'activo',
            ]);

        $roles = RolesUser(AuthUser()->rol);
        $isDireccion = $this->isDireccion();

        if ($isDireccion) {
            $query->whereIn('rol', $roles);
        } else {
            $this->propietario = AuthUser()->FullName;
            $query->where(function ($q): void {
                $q->where(function ($normal): void {
                    $normal->where('tipoDocumento', '!=', AssumpteParticularArchiveService::TIPO_DOCUMENTO)
                        ->where('propietario', AuthUser()->FullName);
                })->orWhere(function ($resolucio): void {
                    $resolucio->where('tipoDocumento', AssumpteParticularArchiveService::TIPO_DOCUMENTO)
                        ->where('propietario_dni', AuthUser()->dni);
                });
            })->whereIn('rol', $roles);
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

        $sortField = $this->sanitizeSortField($this->sortField);
        $sortDirection = $this->sanitizeSortDirection($this->sortDirection);
        $query->orderBy($sortField, $sortDirection)->orderBy('id', 'desc');

        $documentos = $query->paginate($this->perPage);

        return view('livewire.documento-table', [
            'documentos' => $documentos,
            'isDireccion' => $isDireccion,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
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

    public function sortBy(string $field): void
    {
        $field = $this->sanitizeSortField($field);

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    private function sanitizeSortField(string $field): string
    {
        $allowed = ['created_at'];
        return in_array($field, $allowed, true) ? $field : 'created_at';
    }

    private function sanitizeSortDirection(string $direction): string
    {
        return $direction === 'asc' ? 'asc' : 'desc';
    }

    private function isDireccion(): bool
    {
        return userIsAllow(config('roles.rol.direccion'))
            || userIsAllow(config('roles.rol.administrador'));
    }
}
