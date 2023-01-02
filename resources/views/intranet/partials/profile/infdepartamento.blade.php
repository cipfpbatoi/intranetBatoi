<ul class='list-unstyled top_profiles scroll-view'>
    @foreach ($panel->getElementos($pestana) as $elemento)
    @php $enlace = $elemento->enlace?$elemento->enlace:"/reunion/".$elemento->id."/pdf" @endphp
    <li class="media event col-md-3 col-lg-3" style="border-bottom: 2px">
        <a href="{{$enlace}}" class="pull-left border-aero profile_thumb">
            <em class="fa fa-paperclip aero"></em>
        </a>
        <div class="media-body">

            <a href="{{$enlace}}" class="title">
                <em class="fa fa-calendar"></em> {{$elemento->curso}} <br/>
                {{day($elemento->created_at)}} {{month($elemento->created_at)}}
            </a>
            <p>
                <a href="{{$enlace}}">
                    {{$elemento->departamento}}<br/>
                    {{$elemento->avaluacio}} @lang("validation.attributes.Evaluacion")
                </a>
            </p>
        </div>
    </li>
    @endforeach
</ul>

