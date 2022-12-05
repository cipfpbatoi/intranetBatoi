<!-- Modal Nou -->
<x-modal name="AddAlumno" title='{{ trans("models.modelos.Alumno")}}' clase="form-horizontal form-label-left"
         message='{{ trans("messages.generic.anadir") }} {{ trans("models.modelos.Alumno") }}'>
    <input type="hidden" id='idColaboracion' name="idColaboracion" value=""/>
    <input type="hidden" name="asociacion" value="1"/>
    <div id="idAlumno" class="form-group">
        <label for="idAlumno"
               class="control-label col-md-3 col-sm-3 col-xs-12"> @lang("validation.attributes.alumno")</label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <select name='idAlumno'>
                @foreach (hazArray(
    \Intranet\Entities\Alumno::misAlumnos()->orderBy('apellido1')->orderBy('apellido2')->get(),
    'nia',
    ['NameFull','horasFct'],
    '-')
     as $key => $alumno)
                    <option value="{{ $key }}"> {{ $alumno }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div id="desde" class="form-group">
        <label for="desde"
               class="control-label col-md-3 col-sm-3 col-xs-12"> @lang("validation.attributes.desde")</label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <input type='text' class="date" name='desde' value={{hoy()}} />
        </div>
    </div>
    <div id="hasta" class="form-group">
        <label for="hasta"
               class="control-label col-md-3 col-sm-3 col-xs-12"> @lang("validation.attributes.hasta")</label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <input type='text' class="date" name='hasta' value=''/>
        </div>
    </div>
    <div id="instructor" class="form-group">
        <label for="instructor"
               class="control-label col-md-3 col-sm-3 col-xs-12"> @lang("validation.attributes.instructor")</label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <select name='idInstructor' id="idInstructor">
            </select>
        </div>
    </div>
    <div id="horas" class="form-group">
        <label for="horas"
               class="control-label col-md-3 col-sm-3 col-xs-12"> @lang("validation.attributes.horas")</label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <input type='text' name='horas' value=''/>
        </div>
    </div>
    </form>
</x-modal>
