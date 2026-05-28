@foreach ($options as $question => $option)
 <div id="step-{{$question+1}}">
    <h1 class="StepTitle">{{ $question + 1 }}. {{ $option->question }}</h1>
    @php  $profe=0; @endphp
    @foreach ($quests as $quest)
        @foreach ($quest['option2'] as $profesores)
            @foreach ($profesores as $dni)
                @php $profe++; $profesor = Intranet\Entities\Profesor::find($dni) @endphp
                <div class="row grid_slider">
                    <div class="col-md-3 col-sm-3 col-xs-12" >
                        <img src="/storage/fotos/{{ $profesor->foto }}" alt="{{$profesor->fullName}}"  height="80" width="80"><br/>
                        {{$profesor->fullName}}
                    </div>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="demo-container">
                            @include('poll.partials.answerInput', ['fieldName' => 'option'.($question+1).'_'.$profe])
                            <p>{{ $quest['option1']->ModuloCiclo->Modulo->literal}}</p>
                        </div>
                    </div>
                    
                </div>
                <hr/>
            @endforeach
        @endforeach
    @endforeach
 </div>
@endforeach  
<!-- End SmartWizard Content -->
