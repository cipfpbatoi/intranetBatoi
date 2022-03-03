@if (firstWord($contacto->document)=='Recordatori')
    {{fechaCurta($contacto->created_at)}} <em class="fa fa-flag"></em>
@elseif (firstWord($contacto->document)=='Informació')
    {{fechaCurta($contacto->created_at)}} <em class="fa fa-lock"></em>
@elseif (firstWord($contacto->document)=='Revisió')
    {{fechaCurta($contacto->created_at)}} <em class="fa fa-check"></em>
@else
    <a href="#" class="small @if ($contacto->action != 'phone') dragable @endif" id="{{$contacto->id}}">
        {{fechaCurta($contacto->created_at)}}
        @if ($contacto->action == 'email') <em class="fa fa-envelope"></em> @endif
        @if ($contacto->action == 'visita') <em class="fa fa-car"></em> @endif
        @if ($contacto->action == 'phone') <em class="fa fa-phone"></em> @endif
        @if (isset($contacto->comentari))  <em class="fa fa-plus"></em> @endif
    </a>
@endif

