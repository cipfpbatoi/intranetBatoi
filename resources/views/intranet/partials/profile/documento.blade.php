@php
    $direccion = esRol(authUser()->rol, config('roles.rol.direccion'));
    $colors = ['blue', 'green', 'yellow', 'brown', 'purple', 'orange'];
@endphp
<div class="container bootstrap snippets bootdeys">
        <div class="documento-ticket-list">
            @foreach ($panel->getElementos($pestana) as $elemento)
                @php
                    $enlace = $elemento->enlace??"/documento/".$elemento->id."/show";
                    $edit = "/documento/".$elemento->id."/edit";
                    $color = $colors[abs((int) $elemento->id) % count($colors)];
                @endphp
                <x-note :name="$elemento->tipoDocumento"
                        :color="$color"
                        :title="$elemento->curso"
                        :message="$elemento->descripcion"
                        linkEdit="{{$direccion?$edit:'#'}}"
                        linkShow="{{$enlace}}"
                ></x-note>
            @endforeach
        </div>
</div>
