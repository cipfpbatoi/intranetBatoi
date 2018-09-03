@php dd($panel->getElementos($pestana)) @endphp
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->Alumno->nia}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief"><i>{{ $elemento->nia}}</i></h4>
            <div class="left col-xs-12">
                <h2>{{ $elemento->Alumno->FullName }} </h2>
            </div>
            <div class="left col-xs-12">
                <p><strong>@lang('validation.attributes.expediente')</strong> {{$elemento->Alumno->expediente}} </p>
                <ul class="list-unstyled">
                    @if ($elemento->Alumno->telef1 != " ")
                    <li><i class="fa fa-phone"></i> {{$elemento->Alumno->telef1}}</li>
                    @endif
                    @if ($elemento->Alumno->telef2 != " ")
                    <li><i class="fa fa-phone"></i> {{$elemento->Alumno->telef2}}</li>
                    @endif
                    @if ($elemento->Alumno->email != " ")
                    <li><i class="fa fa-envelope"></i> {{$elemento->Alumno->email}}</li>
                    @endif
                </ul>
            </div>
            <div class="left col-xs-12">
                <ul class="list-unstyled">
                   <li><i class="fa fa-birthday-cake"></i> {{$elemento->Centro}}</li>
                   <li><i class="fa fa-calendar"></i>{{$elemento->desde}} - {{$elemento->hasta}} </li>
                   <li><i class="fa fa-clock-o"></i>{{ $elemento->horas }}</li>
                   <li><i class="fa-user"></i>{{$elemento->instructor}}</li> 
                   <li><i class="fa fa-envelope"></i> @if (isset($elemento->Instructor->email)) {{$elemento->Instructor->email}} @endif</li>
                </ul>
            </div>
        </div>
        
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-6 emphasis">
                <p class="ratings">
                    
                </p>
            </div>
            <div class="col-xs-12 col-sm-6 emphasis">
                @include ('intranet.partials.buttons',['tipo' => 'profile'])
             </div>
        </div>
    </div>
</div>
@endforeach

