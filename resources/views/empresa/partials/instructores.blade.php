@if ($existeColaboracion)
    <div class="accordion" id="accordion0{{$centro->id}}">
        <div class="card">
            <a class="d-block collapsed"
               id="headingOne0{{$centro->id}}"
               data-bs-toggle="collapse"
               data-bs-parent="#accordion0{{$centro->id}}"
               href="#collapseOne0{{$centro->id}}"
               aria-expanded="false"
               aria-controls="collapseOne0{{$centro->id}}">
                <h4 class="card-title">- Instructors -</h4>
            </a>
            <div id="collapseOne0{{$centro->id}}"
                 class="collapse"
                aria-labelledby="headingOne0{{$centro->id}}">
                <div class="card-body">
                    <a href="{{ route('instructor.create', ['centro' => $centro->id]) }}">Nou Instructor</a>
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
                                <a href="{{ route('instructor.edita', ['id' => $instructor->dni, 'centro' => $centro->id]) }}"><em
                                            class="fa fa-edit"></em></a>
                                <a href="{{ route('instructor.destroy', ['id' => $instructor->dni, 'centro' => $centro->id]) }}"
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
