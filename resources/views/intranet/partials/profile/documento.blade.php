@php
    $direccion = esRol(authUser()->rol, config('roles.rol.direccion'))
@endphp
<div class="container bootstrap snippets bootdeys">
        <div class="row">
            @foreach ($panel->getElementos($pestana) as $elemento)
                @php
                    $enlace = $elemento->enlace??"/documento/".$elemento->id."/show";
                    $edit = "/documento/".$elemento->id."/edit";
                @endphp
                <x-note name="{{day($elemento->created_at)}} {{month($elemento->created_at)}}"
                        color="{{array('blue','green','yellow','brown','purple','orange')[rand(0,5)]}}"
                        title="{{$elemento->curso}}" message="{!! $elemento->descripcion !!}" linkEdit="{{$edit}}"
                        linkShow="{{$direccion?$enlace:'#'}}"
                ></x-note>
            @endforeach
        </div>
</div>

