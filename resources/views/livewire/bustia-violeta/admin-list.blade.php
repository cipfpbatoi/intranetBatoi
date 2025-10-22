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
        <select class="form-select w-auto" wire:model="tipus">
            <option value="">Totes les bústies</option>
            <option value="violeta">Violeta</option>
            <option value="convivencia">Convivència</option>
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
            <th>Finalitat</th>
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
                <td>{{ ucfirst($e->finalitat) }}</td>
                <td>{{ $e->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-end">
                   {{-- BOTÓ ACCIÓ --}}
                    @if (!$e->anonimo && $e->dni)
                    <button wire:click="viewContact({{ $e->id }})" class="btn btn-sm btn-outline-primary">
                        Contacte
                    </button>
                    @endif

                    <button wire:click="delete({{ $e->id }})" onclick="return confirm('Eliminar entrada #{{ $e->id }}?')" class="btn btn-sm btn-outline-danger">
                        Eliminar
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $entrades->links() }}

    {{-- Modal de contacte (Bootstrap) --}}
    <div wire:ignore.self class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">
            Dades de contacte — {{ $contact['rol'] === 'profesor' ? 'Professorat' : 'Alumnat' }}
            </h5>
        </div>
        <div class="modal-body">
            @if (!$contact['nom'] && !$contact['email'] && !$contact['telefon'])
            <div class="alert alert-warning mb-0">
                No s’han trobat dades per al DNI <strong>{{ $contact['dni'] }}</strong>.
            </div>
            @else
            <dl class="row mb-0">
                <dt class="col-sm-4">Nom</dt>
                <dd class="col-sm-8">{{ $contact['nom'] ?? '—' }}</dd>

                @if ($contact['rol'] !== 'profesor')
                <dt class="col-sm-4">Grup</dt>
                <dd class="col-sm-8">{{ $contact['grup'] ?? '—' }}</dd>
                @endif

                <dt class="col-sm-4">Email</dt>
                <dd class="col-sm-8">
                @if ($contact['email'])
                    <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>
                @else — @endif
                </dd>

                <dt class="col-sm-4">Telèfon</dt>
                <dd class="col-sm-8">{{ $contact['telefon'] ?? '—' }}</dd>

                <dt class="col-sm-4">DNI</dt>
                <dd class="col-sm-8">{{ $contact['dni'] ?? '—' }}</dd>
            </dl>
            @endif
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" wire:click="closeContact">Tancar</button>
        </div>
        </div>
    </div>
</div>