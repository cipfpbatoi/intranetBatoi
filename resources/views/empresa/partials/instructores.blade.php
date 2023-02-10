@if ($existeColaboracion)
    <div class="accordion" id="accordion0{{$centro->id}}" role="tablist" aria-multiselectable="true">
        <div class="panel">
            <a class="panel-heading collapsed"
               role="tab"
               id="headingOne0{{$centro->id}}"
               data-toggle="collapse"
               data-parent="#accordion0{{$centro->id}}"
               href="#collapseOne0{{$centro->id}}"
               aria-expanded="true"
               aria-controls="collapseOne"
            >
                <h4 class="panel-title">- Instructors -</h4>
            </a>
            <div id="collapseOne0{{$centro->id}}"
                 class="panel-collapse collapse"
                 role="tabpanel"
                 aria-labelledby="headingOne"
            >
                <div class="panel-body">
                    <a href='/instructor/{!!$centro->id!!}/create'>Nou Instructor</a>
                    @foreach ($centro->instructores->sortBy('departamento')->groupBy('departamento') as $departament)
                        @if (!empty($departament->first()->departamento))
                            <small>-{{$departament->first()->departamento}}-</small><br/>
                        @endif
                        @foreach($departament->sortBy('surnames') as $instructor)
                            <h6>
                                <em class="fa fa-user"></em> {{$instructor->nombre}}
                                <em class="fa fa-credit-card"></em> {{$instructor->dni}}
                                <em class="fa fa-envelope"></em> {{$instructor->email}}
                                <em class="fa fa-phone"></em> {{$instructor->telefono}}
                                <a href='/instructor/{!!$instructor->dni!!}/edit/{!!$centro->id!!}'><em
                                            class="fa fa-edit"></em></a>
                                <a href="/instructor/{!!$instructor->dni!!}/delete/{!!$centro->id!!}"
                                   class="delGrupo instructor"><em class="fa fa-trash"></em></a>
                            </h6>

                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <hr/>
@endif
