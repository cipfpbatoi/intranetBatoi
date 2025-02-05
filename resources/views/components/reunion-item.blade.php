<div>
    <x-llist image="actas.png" date="{{ $reunion->fecha }}">
        <h4 class="heading" style="float:left">{{ $reunion->xgrupo }}</h4>
        <blockquote class="message" style="float: right">
            <strong>Persones Convocades:</strong>
            <select>
                @foreach ($reunion->Profesores->sortBy(['apellido1','apellido2','nombre']) as $profesor)
                    <option>{{ $profesor->nameFull }}</option>
                @endforeach
            </select>
        </blockquote>
        <br /><br/>
        <p class="url">
            <span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
            {{ $reunion->Espacio->descripcion }}.
            <span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
            {{ $reunion->descripcion }}
        </p>
    </x-llist>
 </div>