<ul class="messages">
    <li>
        @foreach ($documents as $document)
            
            <img src="/img/actas.png" class="avatar" alt="Avatar">
            <div class="message_date">
               <h3 class="date text-info">{{day($document->created_at)}}</h3>
                <p class="month">{{month($document->created_at)}}</p> 
            </div>
            
            <div class="message_wrapper">
                <h4 class="heading">{{$document->grupo}} </h4>
                <blockquote class="message"></blockquote>
                <br />
                <p class="url">
                    <span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
                    @if ($document->enlace) 
                        <a href="{{$document->enlace}}"><i class="fa fa-paperclip"></i>
                        {{$document->descripcion}}
                    </a>
                    @else
                    <a href="/documento/{{$document->id}}/show"><i class="fa fa-paperclip"></i>
                        {{$document->descripcion}}
                    </a>
                    @endif
                </p>
            </div>
        
        @endforeach
    </li>
</ul>