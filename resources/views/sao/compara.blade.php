@extends('layouts.intranet')
@section('css')
    <title>Compara Dades Intranet/SAO</title>
@endsection
@section('content')
    <div class='x-content'>
        <div class='form_box'>
            <form method="POST" action='/sao/compara' class='form-horizontal form-label-left'>
                <table>
                    <tr>
                       <th style="text-align: center;background-color: #CCC">Camp</th>
                       <th style="text-align: center;background-color: #CCC">Intranet</th>
                       <th style="text-align: center;background-color: #CCC">SAO</th>
                    </tr>
                {{ csrf_field() }}
                @foreach ($dades as $keyFct => $fct)
                    <tr>
                        <td colspan="3" style="text-align: center;background-color: #333">
                            Dades empresa {{$fct['nameEmpresa']}}
                        </td>
                    </tr>
                    @php($idSao = $fct['empresa']['idEmpresa'])
                    @foreach($fct['empresa'] as $field => $value)
                        @if (is_array($value))
                            <tr>
                                <td><strong>{{ucfirst($field)}}</strong>: </td>
                                <td>
                                    <input class="form-check-input" type="radio"
                                           name="empresa_{{$field}}" value="intranet">
                                    <label class="form-check-label" >
                                        {{$value['intranet']}}
                                    </label>
                                </td>
                                <td>
                                    <label class="form-check-label" >
                                        {{$value['sao']}}
                                    </label>
                                    <input class="form-check-input" type="radio"
                                       name="empresa_{{$field}}" value="sao" >
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="3" style="text-align: center;background-color: #333">
                            Dades centre treball {{$fct['nameCentro']}}
                        </td>
                    </tr>
                    @foreach($fct['centro'] as $field => $value)
                        @if (is_array($value))
                            <tr>
                                <td><strong>{{ucfirst($field)}}</strong>: </td>
                                <td>
                                    <input class="form-check-input" type="radio"
                                           name="centro_{{$field}}" value="intranet">
                                    <label class="form-check-label">
                                        {{$value['intranet']}}
                                    </label>
                                </td>
                                <td>
                                    <label class="form-check-label">
                                        {{$value['sao']}}
                                    </label>
                                    <input class="form-check-input" type="radio"
                                           name="centro_{{$field}}" value="sao" >
                                </td>

                            </tr>
                        @endif
                    @endforeach
                @endforeach
                <br/>
                <input type='submit' class='btn btn-success'value='Enviar'/>
                <a href="{{route('alumnofct.index')}}" class='btn btn-danger'>Cancelar</a>
                </table>
            </form>

        </div>
    </div>
@endsection
@section('titulo')
    Gestió Importació SAO
@endsection
