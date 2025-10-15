<div class="container mt-3">
    <h3 class="mb-3 text-purple">Gestió Bústia Violeta</h3>

    @if (session()->has('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <div class="d-flex gap-2 mb-3">
        <input class="form-control" placeholder="Cerca…" wire:model.debounce.500ms="search">
        <select class="form-select w-auto" wire:model="categoria">
            <option value="">Totes categories</option>
            <option value="assetjament">Assetjament</option>
            <option value="igualtat">Igualtat</option>
            <option value="altres">Altres</option>
        </select>
        <select class="form-select w-auto" wire:model="estado">
            <option value="">Tots estats</option>
            <option value="nou">Nou</option>
            <option value="en_revisio">En revisió</option>
            <option value="tancat">Tancat</option>
        </select>
    </div>

    <table class="table table-striped table-sm align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Categoria</th>
            <th>Missatge</th>
            <th>Autor</th>
            <th>Anònim</th>
            <th>Estat</th>
            <th>Publicable</th>
            <th>Adjunt</th>
            <th>Data</th>
            <th class="text-end">Accions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($entrades as $e)
            <tr>
                <td>{{ $e->id }}</td>
                <td>{{ $e->categoria }}</td>
                <td style="max-width:420px">{{ \Illuminate\Support\Str::limit($e->mensaje, 140) }}</td>
                <td>{{ $e->autor_display_name }}</td>
                <td>{{ $e->anonimo ? 'Sí' : 'No' }}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button wire:click="setEstado({{ $e->id }}, 'nou')" class="btn {{ $e->estado==='nou' ? 'btn-primary' : 'btn-outline-primary' }}">Nou</button>
                        <button wire:click="setEstado({{ $e->id }}, 'en_revisio')" class="btn {{ $e->estado==='en_revisio' ? 'btn-warning' : 'btn-outline-warning' }}">En revisió</button>
                        <button wire:click="setEstado({{ $e->id }}, 'tancat')" class="btn {{ $e->estado==='tancat' ? 'btn-success' : 'btn-outline-success' }}">Tancat</button>
                    </div>
                </td>
                <td>
                    <button wire:click="togglePublicable({{ $e->id }})" class="btn btn-sm {{ $e->publicable ? 'btn-success' : 'btn-outline-secondary' }}">
                        {{ $e->publicable ? 'Sí' : 'No' }}
                    </button>
                </td>
                <td>
                    @if ($e->adjunto_path)
                        <a href="{{ asset('storage/'.$e->adjunto_path) }}" target="_blank" class="btn btn-sm btn-outline-info">Obrir</a>
                    @else
                        —
                    @endif
                </td>
                <td>{{ $e->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-end">
                    <button wire:click="delete({{ $e->id }})" onclick="return confirm('Eliminar entrada #{{ $e->id }}?')" class="btn btn-sm btn-outline-danger">
                        Eliminar
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $entrades->links() }}
</div>