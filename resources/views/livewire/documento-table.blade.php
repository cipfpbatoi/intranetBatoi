<div>
    <div class="row" style="margin-bottom: 12px;">
        <div class="col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 8px;">
            <input type="text"
                   class="form-control"
                   placeholder="Cercar en qualsevol camp..."
                   wire:model.live.debounce.500ms="search">
        </div>
        <div class="col-md-2 col-sm-6 col-xs-12" style="margin-bottom: 8px;">
            <select class="form-control" wire:model.live="tipoDocumento">
                <option value="">Tots els tipus</option>
                @foreach ($tipoOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-sm-6 col-xs-12" style="margin-bottom: 8px;">
            <select class="form-control" wire:model.live="curso">
                <option value="">Tots els cursos</option>
                @foreach ($cursoOptions as $cursoOption)
                    <option value="{{ $cursoOption }}">{{ $cursoOption }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-sm-6 col-xs-12" style="margin-bottom: 8px;">
            @if ($isDireccion)
                <input type="text"
                       class="form-control"
                       placeholder="Propietari"
                       wire:model.live.debounce.500ms="propietario">
            @else
                <input type="text"
                       class="form-control"
                       value="{{ $propietario }}"
                       disabled>
            @endif
        </div>
        <div class="col-md-2 col-sm-6 col-xs-12" style="margin-bottom: 8px;">
            <input type="text"
                   class="form-control"
                   placeholder="Tags"
                   wire:model.live.debounce.500ms="tags">
        </div>
    </div>

    <div class="row" style="margin-bottom: 12px;">
        <div class="col-md-2 col-sm-6 col-xs-12" style="margin-bottom: 8px;">
            <select class="form-control" wire:model.live="perPage">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
        </div>
        @if ($isDireccion)
            <div class="col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 8px;">
                <label style="display: inline-flex; align-items: center; gap: 6px;">
                    <input type="checkbox" wire:model.live="mostrarTot">
                    Mostrar totes les dades (rol permés)
                </label>
            </div>
        @else
            <div class="col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 8px;">
                <span class="text-muted">Mostrant només els teus documents</span>
            </div>
        @endif
        <div class="col-md-6 col-sm-12 col-xs-12 text-right" style="margin-bottom: 8px;">
            <span wire:loading class="text-muted">Carregant...</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Tipus</th>
                <th>Descripció</th>
                <th>Curs</th>
                <th>ID</th>
                <th>Propietari</th>
                <th>Creació</th>
                <th>Grup</th>
                <th>Tags</th>
                <th>Cicle</th>
                <th>Mòdul</th>
                <th>Detall</th>
                <th>Fitxer</th>
                <th>Accions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($documentos as $documento)
                <tr>
                    <td>{{ $documento->tipoDocumento }}</td>
                    <td>{{ $documento->descripcion }}</td>
                    <td>{{ $documento->curso }}</td>
                    <td>{{ $documento->idDocumento }}</td>
                    <td>{{ $documento->propietario }}</td>
                    <td>{{ $documento->created_at }}</td>
                    <td>{{ $documento->grupo }}</td>
                    <td>{{ $documento->tags }}</td>
                    <td>{{ $documento->ciclo }}</td>
                    <td>{{ $documento->modulo }}</td>
                    <td>{{ $documento->detalle }}</td>
                    <td>{{ $documento->fichero }}</td>
                    <td>
                        @if ($documento->link && in_array($documento->rol, RolesUser(AuthUser()->rol)))
                            <a class="btn btn-xs btn-info" href="{{ route('documento.show', $documento->id) }}" title="Veure">
                                <i class="fa fa-eye"></i>
                            </a>
                        @endif
                        @if (userIsAllow(config('roles.rol.direccion')))
                            <a class="btn btn-xs btn-warning" href="{{ route('documento.edit', $documento->id) }}" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a class="btn btn-xs btn-danger" href="{{ route('documento.destroy', $documento->id) }}" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center text-muted">No hi ha dades disponibles</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="text-center">
        {{ $documentos->links('pagination::bootstrap-4') }}
    </div>
</div>
