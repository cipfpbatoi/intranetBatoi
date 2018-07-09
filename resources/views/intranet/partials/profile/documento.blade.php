<ul class='list-unstyled top_profiles scroll-view'>
    @foreach ($panel->getElementos($pestana) as $elemento)
    @php 
        $enlace = $elemento->enlace?$elemento->enlace:"/documento/".$elemento->id."/show";
        $edit = "/documento/".$elemento->id."/edit";
    @endphp
    <li class="media event col-md-3 col-lg-3" style="border-bottom: 2px">
        @if (esRol(AuthUser()->rol,config('roles.rol.direccion'))) 
         <a href="{{$edit}}" class="pull-left border-aero profile_thumb">
        @else     
         <a href="{{$enlace}}" class="pull-left border-aero profile_thumb"> 
        @endif
            <i class="fa fa-paperclip aero"></i>
         </a> 
        <div class="media-body">
            <a href="{{$enlace}}" class="title"><i class="fa fa-calendar"></i> {{$elemento->curso}} <br/> {{day($elemento->created_at)}} {{month($elemento->created_at)}}</a>
            <p>
                <a href="{{$enlace}}"> 
                    {{substr($elemento->descripcion,0,60)}}
                    @if (strlen($elemento->descripcion)>60) ... @endif
                    @if (strlen($elemento->descripcion)<34)<br/><br/>@endif
                </a>  
            </p>
        </div>
    </li>
    @endforeach
</ul>

