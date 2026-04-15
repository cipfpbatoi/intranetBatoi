<ul class="list-unstyled wizard_steps">
    @foreach ($poll->Plantilla->options as $index => $option)
       <li>
            <a href="#step-{{$index+1}}">
                <span class="step_no">
                @if ($option->isNumericType())  <i class='glyphicon glyphicon-ok'></i>
                @elseif ($option->isSelectType()) <i class='glyphicon glyphicon-list'></i>
                @else <i class='glyphicon glyphicon-pencil'></i>
                @endif
                {{$index+1}}</span>
            </a>
        </li> 
    @endforeach
</ul>        
