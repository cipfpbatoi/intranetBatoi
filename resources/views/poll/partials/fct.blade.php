@foreach ($poll->Plantilla->options as $question => $option)
 <div id="step-{{$question+1}}">
    <h1 class="StepTitle">{{ $option->question }}</h1>
    @foreach ($modulos as $modulo)
        <div class="row grid_slider">
            <div class="col-md-3 col-sm-3 col-xs-12" >
                {{$modulo->Instructor->Nombre}} de {{$modulo->Colaboracion->Empresa}}
            </div>
            <div class="col-md-7 col-sm-7 col-xs-12">
                <div class="demo-container">
                    @if ($option->scala != 0)
                    <div class="demo">
                        <input type="text" class="js-range-slider" name="option{{$question+1}}_{{$modulo->id}}" value=""
                               data-min="0"
                               data-max="{{$option->scala}}"
                               data-from="0"
                               data-
                               />
                    </div>
                    <div class="demo">
                        <span id="option{{$question+1}}_{{$modulo->id}}" class="btn btn-danger btn-sm">No Avaluat</span>
                    </div>
                    @else
                    <div class="demo">
                        <textarea name="option{{$question+1}}_{{$modulo->id}}" rows="3" cols="150"></textarea>
                    </div>
                    @endif

                </div>
            </div>

        </div>
                <hr/>
    @endforeach
 </div>
@endforeach  
<!-- End SmartWizard Content -->