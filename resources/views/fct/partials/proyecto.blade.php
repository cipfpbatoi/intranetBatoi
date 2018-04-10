<ul class="messages colaboracion">
        <li>
            <div class="message_date" style="width:50%">
                <h4>{{ $proyecto->tags }}</h4>
            </div>
            <div class="message_wrapper" style="width:50%">
                <h4>
                    <h4><a href="/documento/{{$proyecto->id}}/show"><i class="fa fa-user user-profile-icon"> </i> {{ $proyecto->descripcion }}</a></h4>
                    <span class='info' style="font-weight: bold">Qualificació: {!! $fct->calProyecto !!} </span>
                    <h4><i class="fa fa-calendar-check-o user-profile-icon"></i> {{ $proyecto->created_at }}
                </h4>
                
            </div>
        </li>            
</ul>

<div class="message_wrapper">
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddColaboration">
        @lang("messages.generic.anadir") @lang("models.modelos.Proyecto")
    </button>
</div>
@include('layouts.partials.error')



