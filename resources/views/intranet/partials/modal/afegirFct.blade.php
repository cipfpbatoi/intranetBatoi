<!-- Modal Nou -->
<x-modal name="AddAlumno" title='{{ trans("models.modelos.Alumno")}}'
         message='{{ trans("messages.generic.anadir") }} {{ trans("models.modelos.Alumno") }}'>
    @lang("validation.attributes.Alumno") :
    <select name='idAlumno'>
        @foreach (hazArray(\Intranet\Entities\Alumno::misAlumnos()->orderBy('apellido1')->orderBy('apellido2')->get(),'nia',['NameFull','horasFct'],'-') as $key => $alumno)
            <option value="{{ $key }}"> {{ $alumno }}</option>
        @endforeach
    </select><br/>
    <input type="hidden" id='idColaboracion' name="idColaboracion" value="" />
    <input type="hidden" name="asociacion" value="1" />
    @lang("validation.attributes.desde") : <input type='text' class="date" name='desde' value=''/><br/>
    @lang("validation.attributes.hasta") :<input type='text' class="date" name='hasta' value=''/><br/>
    @lang("validation.attributes.instructor") :
    <select id='idInstructor' name='idInstructor'>
    </select><br/>
    @lang("messages.generic.horas") :<input type='text' name='horas' value=''/><br/><br/>
</x-modal>
