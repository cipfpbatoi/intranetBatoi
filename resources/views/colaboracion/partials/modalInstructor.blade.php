<!-- Modal -->
<div class="modal fade" id="AddAlumno" tabindex="-1" role="dialog" aria-labelledby="AddAlumnoTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="AddInstructorTitle">@lang("models.modelos.Alumno")
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h4>
            </div>
            <form action='/fct/fctalumnoCreate' method='post' class="form-horizontal form-label-left">
                <div class="modal-body">
                    @csrf
                    <div id="idAlumno" class="form-group">
                        <label for="idAlumno"
                               class="control-label col-md-3 col-sm-3 col-xs-12"> @lang("validation.attributes.alumno")</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select name='idAlumno'>
                                @foreach (hazArray(\Intranet\Entities\Alumno::misAlumnos()->orderBy('apellido1')->orderBy('apellido2')->get(),'nia',['NameFull','horasFct'],'-') as $key => $alumno)
                                    <option value="{{ $key }}"> {{ $alumno }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="idColaboracion" value="{{$elemento->id}}"/>
                    <input type="hidden" name="asociacion" value="1"/>
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
                            <select name='idInstructor'>
                                @foreach (hazArray($elemento->Centro->Instructores,'dni','nombre') as $dni => $nombre)
                                    <option value="{{ $dni }}"> {{ $nombre }}</option>
                                @endforeach
                            </select><br/>
                        </div>
                    </div>
                    <div id="horas" class="form-group">
                        <label for="horas"
                               class="control-label col-md-3 col-sm-3 col-xs-12"> @lang("validation.attributes.horas")</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type='text' name='horas' value=''/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input id="submit" class="boton" type="submit"
                           value="@lang('messages.generic.anadir') @lang('models.modelos.Alumno')">
                </div>
            </form>
        </div>
    </div>
</div>
