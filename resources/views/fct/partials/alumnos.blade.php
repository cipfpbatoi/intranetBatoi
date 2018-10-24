<ul class="messages fct">
    @foreach($fct->Alumnos as $alumno)
        <li>
            <div class="message_date">
                <h4 class="text-info">Tutor: - @foreach ($alumno->Tutor as $tutor) {{$tutor->FullName}} - @endforeach</h4>
            </div>
            <div class="message_wrapper">
                 <h4 class="text-info">{{$alumno->FullName}}</h4>
                 <h4 class="text-info"><i class="fa fa-phone user-profile-icon"></i> {{$alumno->telef1}} <i class="fa fa-envelope user-profile-icon"></i> {{$alumno->email}}</h4>
            </div>
        </li>    
    @endforeach
    <form action='/fct/{!!$fct->id!!}/alumnoCreate' method='post'>
        @csrf
        <select name='idAlumno'>
           @foreach (hazArray(\Intranet\Entities\Alumno::misAlumnos()->orderBy('apellido1')->orderBy('apellido2')->get(),'nia',['NameFull','horasFct'],'-') as $key => $alumno)
           <option value="{{ $key }}"> {{ $alumno }}</option>
           @endforeach 
        </select>
        <input type="submit" class="btn btn-secondary" value="@lang('messages.generic.anadir') @lang('models.modelos.Alumno')"></input>
    </form>    
</ul>


