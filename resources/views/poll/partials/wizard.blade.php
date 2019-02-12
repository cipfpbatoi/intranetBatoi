@foreach ($poll->options as $question => $option)
 <div id="step-{{$question+1}}">
    <h1 class="StepTitle">{{ $option->question }}</h1>
    @php  $profe=0; @endphp
    @foreach ($modulos as $modulo)
        @foreach ($modulo['profesores'] as $profesores)
            @foreach ($profesores as $dni)
                @php $profe++; $profesor = Intranet\Entities\Profesor::find($dni) @endphp
                <div class="row grid_slider">
                    <div class="col-md-3 col-sm-3 col-xs-12" >
                        <img src="/{{ $profesor->foto }}" alt="{{$profesor->fullName}}"  height="80" width="80"><br/>
                        {{$profesor->fullName}}
                    </div>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="demo-container">
                            @if ($option->scala != 0)
                            <div class="demo">
                                <input type="text" class="js-range-slider" name="option{{$question+1}}_{{$profe}}" value=""
                                       data-min="0"
                                       data-max="{{$option->scala}}"
                                       data-from="0"
                                       data-
                                       />
                            </div>
                            <div class="demo">
                                <span id="option{{$question+1}}_{{$profe}}" class="btn btn-danger btn-sm">No Avaluat</span>
                            </div>
                            @else
                            <div class="demo">
                                <textarea name="option{{$question+1}}_{{$profe}}" rows="3" cols="150"></textarea>
                            </div>
                            @endif
                            <p>{{ $modulo['modulo']->ModuloCiclo->Modulo->literal}}</p>
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