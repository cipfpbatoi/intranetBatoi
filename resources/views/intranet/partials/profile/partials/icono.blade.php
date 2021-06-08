@if (firstWord($contacto->document)=='Recordatori')
    <i class="fa fa-flag"></i>
@elseif (firstWord($contacto->document)=='Informació')
    <i class="fa fa-lock"></i>
@elseif (firstWord($contacto->document)=='Revisió')
    <i class="fa fa-check"></i>
@else
    <a href="#" class="small" id="{{$contacto->id}}">
        @if ($contacto->action == 'email') <i class="fa fa-envelope"></i> @endif
        @if ($contacto->action == 'visita') <i class="fa fa-car"></i> @endif
        @if ($contacto->action == 'phone') <i class="fa fa-phone"></i> @endif
        @if (isset($contacto->comentari))  <i class="fa fa-plus"></i> @endif
    </a>
@endif

