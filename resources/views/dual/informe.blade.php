@extends('layouts.intranet')
@section('css')
    {{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.css') }}
    {{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.min.css') }}
    <title>@lang("models.dual.imprime")</title>
@endsection
@section('content')
    <div class='x-content'>

        <div class='form_box'>
            <form method="POST" action='/dual/{{$id}}/informe' class='form-horizontal form-label-left'>
                {{ csrf_field() }}
                <div style="float: left" class="col-md-6 col-sm-6 col-xs-12">

                    <h4>Signatura conveni</h4>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Annex IV:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='annexiv' name='annexiv' />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Document COVID alumne:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='covid' name='covid' />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Declaració responsable:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='declaracio' name='declaracioResponsable' />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Document Beca:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='beca' name='beca'   />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Document 1:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='document1' name='DOC1'   />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Document 2:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='document2' name='DOC2'  />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Document 3 {{ curso() }} :</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='document3a' name='DOC3a' />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Document 3 {{ cursoAnterior() }} :</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='document3b' name='DOC3b'  />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Justificant Doc 3 Alumne:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='justAl' name='justAl'  />
                        </div>
                    </div><div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Justificant Doc 3 Empresa:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='justEm' name='justEm'  />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Document 4:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='document4' name='DOC4'   />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Document 5:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='document5' name='DOC5'   />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Annex XII:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='annexii' name='annexii'   />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Annex V:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='annexv' name='annexv'  />
                        </div>
                    </div>
                </div>
                <div style="float: left" class="col-md-6 col-sm-6 col-xs-12">
                    <h4>Formació empresa</h4>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Annex VII:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='annexvii' name='annexevii'  />
                        </div>
                    </div>
                    <h4>Final cicle</h4>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Annex Va:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='annexva' name='annexva'  />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Annex Vb:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='annexvb' name='annexvb'  />
                        </div>
                    </div>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Annex xiii:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='checkbox' class="form-control" id='annexiii' name='annexiii'  />
                        </div>
                    </div>
                    <h4>Altres Dades</h4>
                    <div class="form-group item">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Data documentació:</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <input type='text' class="date form-control" id='data' name='data'/></div>
                        </div>
                    </div>
                </div>
                <div style="float: left;clear: both">
                    <input type='submit' class='btn btn-success'value='Enviar'/>
                </div>

            </form>
        </div>
    </div>
@endsection
@section('titulo')
    @lang("models.dual.imprime")
@endsection
@section('scripts')
    {{ Html::script('/assets/moment.js') }}
    {{ Html::script('/assets/datetimepicker/js/bootstrap-datetimepicker.min.js') }}
    {{ Html::script("/js/datepicker.js") }}
@endsection
