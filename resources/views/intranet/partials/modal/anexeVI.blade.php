<!-- Modal -->
@php
    $empresas = Intranet\Entities\Fct::misFcts(null,true)->esDual()->count();
    $grupo = $panel->getElementos($pestana)->first()->Alumno->Grupo->first();
    $alumnos = $grupo->Alumnos->where('sexo','H')->count();
    $alumnas = $grupo->Alumnos->where('sexo','M')->count();
    $dualH = 0; $fctH = 0; $OKH = 0; $NOH = 0; $exeH = 0;
    $dualM = 0; $fctM = 0; $OKM = 0; $NOM = 0; $exeM = 0;
    foreach ($panel->getElementos($pestana) as $dual){
            if ($dual->Alumno->sexo == 'H') $dualH++; else $dualM++;
        }
    $fcts = \Intranet\Entities\AlumnoFct::misFcts($grupo->tutor)->esAval()->get();

    foreach ($fcts as $fct){

        if ($fct->Alumno->sexo == 'H'){
            if ($fct->FCT->asociacion == 2) $exeH++;
            else {
                $fctH++;
                if ($dual->calificacion) $OKH++; else $NOH++;
            }
        }  else {
            if ($fct->FCT->asociacion == 2) $exeM++;
            else {
                $fctM++;
                if ($dual->calificacion) $OKM++; else $NOM++;
            }
        }
    }


@endphp
<div id="anexo" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title">{{ trans('models.Resultado.llenar')}}</h4>
            </div>
            <div class="modal-body">
                <form id='formAnexo' class="form-horizontal" action="/dual/anexeVI" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group item">
                        <label for="empresas_id" class="control-label col-md3 col-sm-3 col-xs-12" style="margin-left: 5px;">Empreses</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input id="empresas_id" class="form-control col-sm-3 col-xs-12 text" type="text" name="empresas" value="{{$empresas}}">
                        </div>
                    </div>
                    <div class="form-group item">
                        <table>
                            <thead>
                                <tr>
                                    <th>DUAL</th>
                                    <th>Total</th>
                                    <th>Dual</th>
                                    <th>Contracte</th>
                                    <th>Beca</th>
                                    <th>Altres</th>
                                    <th>Superen Empresa</th>
                                    <th>Superen Cicle</th>
                                    <th>Abandonen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Alumnes</td>
                                    <td><input size="2" type="text" name="alumnos" placeholder="Total" value="{{$alumnos}}"></td>
                                    <td><input size="2" type="text" name="dualH" placeholder="Dual" value="{{$dualH}}"></td>
                                    <td><input size="2" type="text" name="contratoH" placeholder="Contracte" value=""></td>
                                    <td><input size="2" type="text" name="becaH" placeholder="Bequa" value="{{$dualH}}"></td>
                                    <td><input size="2" type="text" name="altresH" placeholder="Altres" value=""></td>
                                    <td><input size="2" type="text" name="superenEH" placeholder="Superen Empresa" value="{{$dualH}}"></td>
                                    <td><input size="2" type="text" name="superenCH" placeholder="Superen Cicle" value="{{$dualH}}"></td>
                                    <td><input size="2" type="text" name="abandonenH" placeholder="Abandonen" value="0"></td>

                                </tr>
                                <tr>
                                    <td>Alumnas</td>
                                    <td><input size="2" type="text" name="alumnas" placeholder="Total" value="{{$alumnas}}"></td>
                                    <td><input size="2" type="text" name="dualM" placeholder="Dual" value="{{$dualM}}"></td>
                                    <td><input size="2" type="text" name="contratoM" placeholder="Contracte" value=""></td>
                                    <td><input size="2" type="text" name="becaM" placeholder="Bequa" value="{{$dualM}}"></td>
                                    <td><input size="2" type="text" name="altresM" placeholder="Altres" value=""></td>
                                    <td><input size="2" type="text" name="superenEM" placeholder="Superen Empresa" value="{{$dualM}}"></td>
                                    <td><input size="2" type="text" name="superenCM" placeholder="Superen Cicle" value="{{$dualM}}"></td>
                                    <td><input size="2" type="text" name="abandonenM" placeholder="Abandonen" value="0"></td>

                                </tr>
                            </tbody>
                       </table>
                    </div>
                    <div class="form-group item">
                        <table>
                            <thead>
                            <tr>
                                <th>FCT</th>
                                <th>Accedixen</th>
                                <th>Superen</th>
                                <th>NO</th>
                                <th>Exempts</th>
                                <th>Renuncien</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Alumnes</td>
                                <td><input size="2" type="text" name="accedixenH" placeholder="Accedixen" value="{{$fctH}}"></td>
                                <td><input size="2" type="text" name="superenH" placeholder="Superen" value="{{$OKH}}"></td>
                                <td><input size="2" type="text" name="NOH" placeholder="NO" value="{{$NOH}}"></td>
                                <td><input size="2" type="text" name="exemptsH" placeholder="Exempts" value="{{$exeH}}"></td>
                                <td><input size="2" type="text" name="renuncienH" placeholder="Renuncien" value=""></td>
                            </tr>
                            <tr>
                                <td>Alumnas</td>
                                <td><input size="2" type="text" name="accedixenM" placeholder="Accedixen" value="{{$fctM}}"></td>
                                <td><input size="2" type="text" name="superenM" placeholder="Superen" value="{{$OKM}}"></td>
                                <td><input size="2" type="text" name="NOM" placeholder="NO" value="{{$NOM}}"></td>
                                <td><input size="2" type="text" name="exemptsM" placeholder="Exempts" value="{{$exeM}}"></td>
                                <td><input size="2" type="text" name="renuncienM" placeholder="Renuncien" value=""></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="formAviso" class="btn btn-primary">@lang("messages.buttons.init")</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang("messages.buttons.cancel")</button>
            </div>
        </div>
    </div>
</div>
