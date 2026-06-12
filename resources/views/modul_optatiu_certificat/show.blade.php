@extends('layouts.intranet')

@section('titulo', 'Certificat de mòdul optatiu')

@section('content')
    <div class="mb-3">
        <a class="btn btn-default btn-sm" href="{{ route('modulOptatiuCertificat.index') }}">Tornar</a>
    </div>

    <form method="post" action="{{ route('modulOptatiuCertificat.update', ['moduloGrupo' => $modul->id]) }}">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="denominacio">Denominació del mòdul optatiu</label>
            <input
                id="denominacio"
                name="denominacio"
                class="form-control"
                value="{{ old('denominacio', $certificat->denominacio) }}"
                maxlength="200"
                required
            >
        </div>

        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Alumne/a</th>
                <th style="width: 180px;">Nota</th>
                <th style="width: 220px;">Estat</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($alumnes as $alumne)
                @php
                    $resultat = $resultats->get($alumne->nia);
                    $estat = $estats->get($alumne->nia);
                @endphp
                <tr>
                    <td>{{ $alumne->fullName }}</td>
                    <td>
                        <select name="notes[{{ $alumne->nia }}]" class="form-control">
                            @foreach ($notes as $key => $label)
                                <option value="{{ $key }}" @selected((string) old("notes.{$alumne->nia}", $resultat->nota ?? 0) === (string) $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        @if ($estat?->registrat_at)
                            Registrat i enviat
                        @else
                            Pendent
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Guardar notes</button>
    </form>

    <form method="post" action="{{ route('modulOptatiuCertificat.emit', ['certificat' => $certificat->id]) }}" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-success">Emetre certificats PDF</button>
    </form>
@endsection
