@extends('layouts.intranet')

@section('titulo', 'Certificats de mòduls optatius')

@section('content')
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>Grup</th>
            <th>Mòdul</th>
            <th>Accions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($moduls as $modul)
            <tr>
                <td>{{ $modul->XGrupo }}</td>
                <td>{{ $modul->XModulo }}</td>
                <td>
                    <a class="btn btn-primary btn-sm" href="{{ route('modulOptatiuCertificat.show', ['moduloGrupo' => $modul->id]) }}">
                        Gestionar
                    </a>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
@endsection
