@php
    $direccion = esRol(authUser()->rol, config('roles.rol.direccion'))
@endphp
<div class="container bootstrap snippets bootdeys">
        <div class="row">
            @foreach ($panel->getElementos($pestana) as $elemento)
                @php
                    $enlace = $elemento->enlace??"/documento/".$elemento->id."/show";
                    $edit = "/documento/".$elemento->id."/edit";
                    $name = ($elemento->tipoDocumento === 'Proyecto') ? $elemento->curso : 'Document' ;
                @endphp
                <x-note name="{{$elemento->tipoDocumento}}"
                        color="{{array('blue','green','yellow','brown','purple','orange')[rand(0,5)]}}"
                        title="{{$name}}"
                        message="{!! $elemento->descripcion !!}"
                        linkEdit="{{$direccion?$edit:'#'}}"
                        linkShow="{{$enlace}}"
                ></x-note>
            @endforeach
        </div>
</div>

