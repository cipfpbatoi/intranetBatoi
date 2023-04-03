@extends('layouts.intranet')
@section('css')
    <title>{{ trans("models.Profesor.files") }}</title>
@endsection
@section('content')
    <table>
        <tr>
            <th>Imatge</th>
            <th>Rúbrica</th>
            <th>Peu email</th>
            <th>Certificat Digital</th>
            <th></th>
        </tr>
        <tr>
            <td>
                <img style="max-width: 50%" src="{{'/storage/'.$profesor->foto}}"  alt="foto"/>
            </td>
            <td>
                <img style="max-width: 50%" src="{{'/storage/signatures/'.$profesor->fileName.'.png'}}" alt="rubrica" />
            </td>
            <td>
                <img style="max-width: 50%" src="{{'/storage/peus/'.$profesor->fileName.'.png'}}" alt="peu" />
            </td>
            <td>
                @if(file_exists(storage_path('app/certificats/'.$profesor->fileName.'.tmp')))
                   <img src="/img/certificat_digital.png" alt="digital carregat" />
                @endif
            </td>
            <td></td>
        </tr>
        <tr>
            <form id="formulari" method="post" action="{{route('files.update')}}" enctype="multipart/form-data">
                {{csrf_field()}}
                @method('PUT')
                <td>
                    <input type="file" name="foto" />
                </td>
                <td>
                    <input type="file" name="signatura" />
                </td>
                <td>
                    <input type="file" name="peu" />
                </td>
                <td>
                    <input id='certificat' type="file" name="certificat_digital" />
                    <label for="password">Login usuari:</label>
                    <input type="password" id="password" name="password" />
                </td>
                <td>
                    <input type="submit" value="Enviar" />
                </td>
            </form>
        </tr>
        <tr>
        <td colspan="5">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </tr>
    </table>
@endsection

@section('titulo')
    {{trans("models.profesor.files")}}
@endsection
@section('scripts')
    {{ Html::script("/js/profesor/files.js") }}
@endsection
