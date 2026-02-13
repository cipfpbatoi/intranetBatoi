<?php

namespace Intranet\Livewire;

use Livewire\Component;
use Intranet\Entities\Horario;
use Intranet\Entities\Hora;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Storage;

class HorariProfessorCanvi extends Component
{
    public string $dni = '';
    public string $profesorNom = '';
    public bool $isDireccion = false;
    public array $horas = [];
    public array $dias = [
        'L' => 'Dilluns',
        'M' => 'Dimarts',
        'X' => 'Dimecres',
        'J' => 'Dijous',
        'V' => 'Divendres',
    ];
    public array $items = [];
    public array $grid = [];
    public array $originalGrid = [];
    public array $turnoBySesion = [];
    public ?string $selectedCell = null;
    public string $estado = 'No hi ha proposta';
    public string $obs = '';
    public bool $editable = true;
    public string $error = '';
    public string $message = '';
    public string $fechaInicio = '';
    public string $fechaFin = '';
    public ?string $propuestaId = null;
    public array $propuestaOptions = [];
    public ?string $selectedPropuestaId = null;
    public array $declaraciones = [
        'mantenimiento_turno' => false,
        'ausencia_alumnado' => false,
        'servicios_inamovibles' => false,
        'atencion_refuerzo' => false,
        'permanencia' => false,
    ];

    public function mount($dni = null)
    {
        $this->dni = $dni ?: (AuthUser()->dni ?? AuthUser()->id);
        $this->isDireccion = AuthUser() ? esRol(AuthUser()->rol, config('roles.rol.direccion')) : false;

        $profesor = Profesor::find($this->dni);
        if ($profesor) {
            $this->profesorNom = $profesor->fullName;
        }

        $this->loadHoras();
        $this->loadPropuestasDisponibles();
        $this->loadHorario();
        $this->loadCambios();
    }

    protected function loadPropuestasDisponibles(): void
    {
        $this->propuestaOptions = [];
        $disk = Storage::disk('local');
        $dir = '/horarios/' . $this->dni;

        if ($disk->exists($dir)) {
            $files = $disk->allFiles($dir);
            $items = [];

            foreach ($files as $file) {
                if (!str_ends_with($file, '.json')) {
                    continue;
                }
                $raw = $disk->get($file);
                $data = json_decode($raw, true);
                if (!is_array($data)) {
                    continue;
                }
                $id = (string) ($data['id'] ?? basename($file, '.json'));
                $estado = (string) ($data['estado'] ?? 'Pendiente');
                $inicio = (string) ($data['fecha_inicio'] ?? '');
                $fin = (string) ($data['fecha_fin'] ?? '');
                $updated = (string) ($data['updated_at'] ?? '');

                $rango = trim($inicio . ' → ' . $fin, ' →');
                $label = $estado;
                if ($rango !== '') {
                    $label .= ' · ' . $rango;
                } elseif ($updated !== '') {
                    $label .= ' · ' . $updated;
                } else {
                    $label .= ' · ' . $id;
                }

                $items[] = [
                    'id' => $id,
                    'label' => $label,
                    'updated' => $updated,
                ];
            }

            usort($items, function ($a, $b) {
                return strcmp($b['updated'], $a['updated']);
            });

            foreach ($items as $item) {
                $this->propuestaOptions[$item['id']] = $item['label'];
            }
        }

        $requested = request()->get('proposta');
        if ($requested && array_key_exists($requested, $this->propuestaOptions)) {
            $this->selectedPropuestaId = $requested;
        }
    }

    public function updatedSelectedPropuestaId(): void
    {
        if ($this->selectedPropuestaId === null || $this->selectedPropuestaId === '') {
            $this->selectedPropuestaId = null;
            $this->loadHorario();
            $this->novaProposta();
            return;
        }

        $this->selectedCell = null;
        $this->error = '';
        $this->message = '';
        $this->loadHorario();
        $this->loadCambios();
    }

    protected function loadHoras(): void
    {
        $horas = Hora::orderBy('codigo')->get();
        $this->horas = [];
        $this->turnoBySesion = [];

        foreach ($horas as $hora) {
            $this->horas[] = [
                'codigo' => (int) $hora->codigo,
                'turno' => (string) $hora->turno,
                'hora_ini' => (string) $hora->hora_ini,
                'hora_fin' => (string) $hora->hora_fin,
            ];
            $this->turnoBySesion[(int) $hora->codigo] = (string) $hora->turno;
        }
    }

    protected function loadHorario(): void
    {
        $this->items = [];
        $this->grid = [];

        $horarios = Horario::Profesor($this->dni)
            ->with(['Modulo', 'Ocupacion', 'Grupo', 'Hora'])
            ->get();

        foreach ($horarios as $horario) {
            $cell = $horario->sesion_orden . '-' . $horario->dia_semana;
            $itemId = (string) $horario->id;

            $titulo = '';
            $subtitulo = '';
            $tipo = 'modul';

            if ($horario->ocupacion) {
                $tipo = 'ocupacio';
                $titulo = $horario->Ocupacion->nombre
                    ?? $horario->Ocupacion->nom
                    ?? 'Ocupacio desconeguda';
            } else {
                $titulo = $horario->Modulo->cliteral
                    ?? $horario->Modulo->literal
                    ?? 'Modul desconegut';
                if ($horario->Grupo) {
                    $subtitulo = $horario->Grupo->nombre;
                }
            }

            $this->items[$itemId] = [
                'id' => $itemId,
                'orig' => $cell,
                'cell' => $cell,
                'tipo' => $tipo,
                'titulo' => $titulo,
                'subtitulo' => $subtitulo,
                'aula' => $horario->aula ?? '',
                'is_guardia' => in_array($horario->ocupacion, config('constants.ocupacionesGuardia'), true),
            ];
            $this->grid[$cell] = $itemId;
        }

        $this->originalGrid = $this->grid;
    }

    protected function loadCambios(): void
    {
        if ($this->selectedPropuestaId) {
            $this->loadPropuestaById($this->selectedPropuestaId);
            return;
        }

        $propostaId = request()->get('proposta');
        if ($propostaId) {
            $this->selectedPropuestaId = (string) $propostaId;
            $this->loadPropuestaById((string) $propostaId);
            return;
        }

        $pending = $this->latestPropuestaByEstado('Pendiente');
        if ($pending) {
            $this->applyPropuestaData($pending);
            return;
        }

        $accepted = $this->latestPropuestaByEstado('Aceptado');
        if ($accepted) {
            $this->applyPropuestaData($accepted);
        }
    }

    protected function loadPropuestaById(string $id): void
    {
        $disk = Storage::disk('local');
        $path = '/horarios/' . $this->dni . '/' . $id . '.json';

        if (!$disk->exists($path)) {
            return;
        }

        $raw = $disk->get($path);
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return;
        }

        $this->selectedPropuestaId = $id;

        $this->applyPropuestaData($data);
    }

    protected function applyPropuestaData(array $data): void
    {
        $this->propuestaId = isset($data['id']) ? (string) $data['id'] : null;
        if ($this->selectedPropuestaId === null && $this->propuestaId) {
            $this->selectedPropuestaId = $this->propuestaId;
        }
        $this->estado = (string) ($data['estado'] ?? $this->estado);
        $this->obs = (string) ($data['obs'] ?? '');
        $this->fechaInicio = (string) ($data['fecha_inicio'] ?? '');
        $this->fechaFin = (string) ($data['fecha_fin'] ?? '');
        $this->declaraciones = array_merge(
            $this->declaraciones,
            is_array($data['declaraciones'] ?? null) ? $data['declaraciones'] : []
        );
        $this->editable = $this->isDireccion || !in_array($this->estado, ['Aceptado', 'Guardado'], true);

        $cambios = $data['cambios'] ?? [];
        if (is_array($cambios)) {
            $this->applyCambios($cambios);
        }
    }

    protected function applyCambios(array $cambios): void
    {
        foreach ($cambios as $cambio) {
            if (!isset($cambio['de'], $cambio['a'])) {
                continue;
            }
            $this->forceMove($cambio['de'], $cambio['a']);
        }
    }

    protected function forceMove(string $from, string $to): void
    {
        if ($from === $to) {
            return;
        }

        $fromId = $this->grid[$from] ?? null;
        if (!$fromId) {
            return;
        }

        $toId = $this->grid[$to] ?? null;

        $this->grid[$to] = $fromId;
        $this->items[$fromId]['cell'] = $to;

        if ($toId) {
            $this->grid[$from] = $toId;
            $this->items[$toId]['cell'] = $from;
        } else {
            unset($this->grid[$from]);
        }
    }

    public function cellClicked(string $cell): void
    {
        $this->error = '';
        $this->message = '';

        if (!$this->editable) {
            $this->error = 'Aquest horari ja esta acceptat i no es pot modificar.';
            return;
        }

        if ($this->selectedCell === null) {
            if (!isset($this->grid[$cell])) {
                $this->error = 'Selecciona una hora ocupada per començar.';
                return;
            }
            if ($this->cellHasGuardia($cell)) {
                $this->error = 'No es poden canviar les guardies.';
                return;
            }
            $this->selectedCell = $cell;
            return;
        }

        if ($this->selectedCell === $cell) {
            $this->selectedCell = null;
            return;
        }

        $this->moveSelectedTo($cell);
        $this->selectedCell = null;
    }

    public function moveFromTo(string $from, string $to): void
    {
        $this->error = '';
        $this->message = '';

        if (!$this->editable) {
            $this->error = 'Aquest horari ja esta acceptat i no es pot modificar.';
            return;
        }

        if ($from === $to) {
            return;
        }

        if (!isset($this->grid[$from])) {
            $this->error = 'Selecciona una hora ocupada per a moure.';
            return;
        }
        if ($this->cellHasGuardia($from) || $this->cellHasGuardia($to)) {
            $this->error = 'No es poden canviar les guardies.';
            return;
        }

        $this->selectedCell = $from;
        $this->moveSelectedTo($to);
        $this->selectedCell = null;
    }

    protected function moveSelectedTo(string $dest): void
    {
        $from = $this->selectedCell;
        if ($from === null) {
            return;
        }

        $fromId = $this->grid[$from] ?? null;
        if (!$fromId) {
            return;
        }

        if ($this->itemIsGuardia($fromId)) {
            $this->error = 'No es poden canviar les guardies.';
            return;
        }

        if ($this->cellHasGuardia($dest)) {
            $this->error = 'No es poden canviar les guardies.';
            return;
        }

        [$fromSesion] = explode('-', $from, 2);
        [$toSesion] = explode('-', $dest, 2);
        [, $fromDia] = explode('-', $from, 2);
        [, $toDia] = explode('-', $dest, 2);

        if ($fromDia !== $toDia) {
            $this->error = 'No pots canviar una hora d\'un dia a un altre.';
            return;
        }
        $fromTurno = $this->turnoBySesion[(int) $fromSesion] ?? null;
        $toTurno = $this->turnoBySesion[(int) $toSesion] ?? null;

        if ($fromTurno !== null && $toTurno !== null && $fromTurno !== $toTurno) {
            $this->error = 'No pots moure una hora del mati a la vesprada (ni al reves).';
            return;
        }

        $this->forceMove($from, $dest);
        $this->message = 'Canvi aplicat.';
    }

    public function resetCanvis(): void
    {
        $this->error = '';
        $this->message = '';
        $this->selectedCell = null;

        $this->grid = $this->originalGrid;
        foreach ($this->items as $id => $item) {
            $this->items[$id]['cell'] = $item['orig'];
        }
    }

    public function novaProposta(): void
    {
        $this->resetCanvis();
        $this->propuestaId = null;
        $this->selectedPropuestaId = null;
        $this->estado = 'Nova';
        $this->editable = true;
        $this->obs = '';
        $this->fechaInicio = '';
        $this->fechaFin = '';
        foreach ($this->declaraciones as $key => $value) {
            $this->declaraciones[$key] = false;
        }
    }

    public function esborrarProposta(): void
    {
        $this->error = '';
        $this->message = '';

        if (!$this->propuestaId) {
            $this->error = 'No hi ha proposta seleccionada.';
            return;
        }

        if (in_array($this->estado, ['Aceptado', 'Guardado'], true)) {
            $this->error = 'No es pot esborrar una proposta acceptada.';
            return;
        }

        $disk = Storage::disk('local');
        $path = '/horarios/' . $this->dni . '/' . $this->propuestaId . '.json';
        if ($disk->exists($path)) {
            $disk->delete($path);
        }

        $this->loadPropuestasDisponibles();
        $this->novaProposta();
        $this->message = 'Proposta esborrada.';
    }

    public function guardarProposta(): void
    {
        $this->error = '';
        $this->message = '';

        if (!$this->editable && !$this->isDireccion) {
            $this->error = 'Aquest horari ja esta acceptat i no es pot modificar.';
            return;
        }

        if (trim($this->obs) === '') {
            $this->error = 'Has d\'indicar les tasques a realitzar en observacions.';
            return;
        }

        if ($this->fechaInicio === '' || $this->fechaFin === '') {
            $this->error = 'Has d\'indicar la data d\'inici i la data de fi del canvi.';
            return;
        }

        $inicioTs = strtotime($this->fechaInicio);
        $finTs = strtotime($this->fechaFin);
        if ($inicioTs === false || $finTs === false) {
            $this->error = 'Les dates indicades no son valides.';
            return;
        }
        if ($inicioTs > $finTs) {
            $this->error = 'La data d\'inici no pot ser posterior a la data de fi.';
            return;
        }

        if ($this->datesOverlapExisting($this->fechaInicio, $this->fechaFin, $this->propuestaId)) {
            $this->error = 'Les dates se solapen amb una altra proposta.';
            return;
        }

        foreach ($this->declaraciones as $valor) {
            if ($valor !== true) {
                $this->error = 'Has de marcar totes les declaracions responsables.';
                return;
            }
        }

        $cambios = $this->buildCambios();
        if (empty($cambios)) {
            $this->error = 'No hi ha canvis per a guardar.';
            return;
        }

        $estadoGuardar = ($this->isDireccion && $this->estado === 'Aceptado') ? 'Aceptado' : 'Pendiente';
        $propuestaId = $this->propuestaId ?: $this->generatePropuestaId();
        $data = [
            'id' => $propuestaId,
            'dni' => $this->dni,
            'estado' => $estadoGuardar,
            'cambios' => $cambios,
            'obs' => $this->obs,
            'fecha_inicio' => $this->fechaInicio,
            'fecha_fin' => $this->fechaFin,
            'declaraciones' => $this->declaraciones,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $disk = Storage::disk('local');
        $baseDir = '/horarios/' . $this->dni;
        if (!$disk->exists($baseDir)) {
            $disk->makeDirectory($baseDir);
        }
        $path = $baseDir . '/' . $propuestaId . '.json';
        if (!$disk->exists($path)) {
            $data['created_at'] = $data['updated_at'];
        }

        $disk->put($path, json_encode($data));

        $this->estado = $estadoGuardar;
        $this->propuestaId = $propuestaId;
        $this->selectedPropuestaId = $propuestaId;
        $this->loadPropuestasDisponibles();
        $this->editable = $this->isDireccion || !in_array($this->estado, ['Aceptado', 'Guardado'], true);
        $this->message = $estadoGuardar === 'Aceptado'
            ? 'Canvis guardats per direccio.'
            : 'Sol·licitud enviada a direccio.';
    }

    public function downloadJson()
    {
        $data = [
            'estado' => 'Pendiente',
            'cambios' => $this->buildCambios(),
            'obs' => $this->obs,
            'fecha_inicio' => $this->fechaInicio,
            'fecha_fin' => $this->fechaFin,
            'declaraciones' => $this->declaraciones,
        ];

        $filename = 'horari-proposta-' . $this->dni . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, $filename);
    }

    public function getCambiosCountProperty(): int
    {
        return count($this->buildCambios());
    }

    protected function buildCambios(): array
    {
        $cambios = [];

        foreach ($this->items as $item) {
            if ($item['cell'] !== $item['orig']) {
                $cambios[] = [
                    'de' => $item['orig'],
                    'a' => $item['cell'],
                ];
            }
        }

        return $cambios;
    }

    protected function latestPropuestaByEstado(string $estado): ?array
    {
        $disk = Storage::disk('local');
        $dir = '/horarios/' . $this->dni;
        if (!$disk->exists($dir)) {
            return null;
        }

        $files = $disk->allFiles($dir);
        $candidates = [];

        foreach ($files as $file) {
            if (!str_ends_with($file, '.json')) {
                continue;
            }
            $raw = $disk->get($file);
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                continue;
            }
            if (($data['estado'] ?? null) !== $estado) {
                continue;
            }
            $candidates[] = $data;
        }

        if (empty($candidates)) {
            return null;
        }

        usort($candidates, function ($a, $b) {
            return strcmp((string) ($b['updated_at'] ?? ''), (string) ($a['updated_at'] ?? ''));
        });

        return $candidates[0] ?? null;
    }

    protected function generatePropuestaId(): string
    {
        return date('YmdHis') . '-' . substr(bin2hex(random_bytes(4)), 0, 8);
    }

    protected function datesOverlapExisting(string $inicio, string $fin, ?string $excludeId = null): bool
    {
        $disk = Storage::disk('local');
        $dir = '/horarios/' . $this->dni;
        if (!$disk->exists($dir)) {
            return false;
        }

        $start = strtotime($inicio);
        $end = strtotime($fin);
        if ($start === false || $end === false) {
            return false;
        }

        foreach ($disk->allFiles($dir) as $file) {
            if (!str_ends_with($file, '.json')) {
                continue;
            }
            $raw = $disk->get($file);
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                continue;
            }
            if ($excludeId && isset($data['id']) && (string) $data['id'] === $excludeId) {
                continue;
            }
            if (($data['estado'] ?? null) === 'Rebutjat') {
                continue;
            }
            $otherStart = strtotime((string) ($data['fecha_inicio'] ?? ''));
            $otherEnd = strtotime((string) ($data['fecha_fin'] ?? ''));
            if ($otherStart === false || $otherEnd === false) {
                continue;
            }
            if ($start <= $otherEnd && $end >= $otherStart) {
                return true;
            }
        }

        return false;
    }

    protected function cellHasGuardia(string $cell): bool
    {
        $itemId = $this->grid[$cell] ?? null;
        return $itemId ? $this->itemIsGuardia($itemId) : false;
    }

    protected function itemIsGuardia(string $itemId): bool
    {
        return (bool) ($this->items[$itemId]['is_guardia'] ?? false);
    }

    public function render()
    {
        return view('livewire.horari-professor-canvi');
    }
}
