<div>
    <h3>{{ $fechaEsp }}</h3>

    <div class="mb-3" style="display:flex;gap:8px;align-items:center;">
        <button type="button" class="btn btn-default" wire:click="diaAnterior">Dia anterior</button>
        <input type="date" class="form-control" style="max-width:180px;" wire:model.live="fecha">
        <button type="button" class="btn btn-default" wire:click="diaSeguent">Dia següent</button>
    </div>

    <table id="tabla-datos" border="1">
        <tr id="profe-title">
            <th>Departament</th>
            <th>Professorat</th>
            <th>Horari</th>
            <th>Fitxatges</th>
        </tr>
        @foreach($rows as $row)
            <tr id="{{ $row['dni'] }}">
                <th>{{ $row['departamento'] }}</th>
                <th>{{ $row['nom'] }}</th>
                <td>
                    <span class="fichaje">{{ $row['horario'] }}</span>
                    <a href="/profesor/{{ $row['dni'] }}/horario" class="btn-success btn btn-xs iconButton">
                        <i class="fa fa-table"></i>
                    </a>
                </td>
                <td>
                    @if(empty($row['fichajes']))
                        <span class="fichaje"></span>
                    @else
                        {!! implode('<br>', $row['fichajes']) !!}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    @if (empty($rows))
        <div class="alert alert-warning" style="margin-top:12px;">
            No hi ha professorat per mostrar en la data {{ $fecha }}.
        </div>
    @endif
</div>
