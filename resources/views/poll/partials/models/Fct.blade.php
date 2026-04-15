@foreach ($options as $question => $option)
 <div id="step-{{$question+1}}">
    <h1 class="StepTitle">{{ $option->question }}</h1>
    @foreach ($quests as $quest)
        <div class="row grid_slider">
            <div class="col-md-3 col-sm-3 col-xs-12" >
                {{$quest['option1']->Instructor->Nombre}} de {{$quest['option1']->Colaboracion->Empresa}}
            </div>
            <div class="col-md-7 col-sm-7 col-xs-12">
                <div class="demo-container">
                    @include('poll.partials.answerInput', ['fieldName' => 'option'.($question+1).'_'.$quest['option1']->id])
                </div>
            </div>

        </div>
                <hr/>
    @endforeach
 </div>
@endforeach  
<!-- End SmartWizard Content -->
