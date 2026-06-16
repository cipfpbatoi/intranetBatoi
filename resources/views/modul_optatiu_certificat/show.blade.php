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
            <label for="denominacio">Nom del mòdul optatiu impartit</label>
            <input
                id="denominacio"
                name="denominacio"
                class="form-control"
                value="{{ old('denominacio', $certificat->denominacio) }}"
                placeholder="Nom real del mòdul optatiu d'este grup"
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
                <th style="width: 140px;">PDF</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($alumnes as $alumne)
                @php
                    $resultat = $resultats->get($alumne->nia);
                    $estat = $estats->get($alumne->nia);
                    $pdfDisponible = (bool) ($pdfDisponibles->get($alumne->nia) ?? false);
                    $notaActual = (int) old("notes.{$alumne->nia}", $resultat->nota ?? 0);
                    $notaActual = $notaActual > 0 && $notaActual < 5 ? 4 : $notaActual;
                @endphp
                <tr>
                    <td>{{ $alumne->fullName }}</td>
                    <td>
                        <select name="notes[{{ $alumne->nia }}]" class="form-control">
                            @foreach ($notes as $key => $label)
                                <option value="{{ $key }}" @selected($notaActual === (int) $key)>
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
                    <td>
                        @if ($pdfDisponible)
                            <a
                                class="btn btn-default btn-sm"
                                href="{{ route('modulOptatiuCertificat.pdf', ['certificat' => $certificat->id, 'alumne' => $alumne->nia]) }}"
                                target="_blank"
                            >
                                <i class="fa fa-file-pdf-o"></i> Certificat
                            </a>
                        @else
                            <button type="button" class="btn btn-default btn-sm" disabled>
                                <i class="fa fa-file-pdf-o"></i> Certificat
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Guardar notes</button>
    </form>

    @if ($potEmetre)
        <form method="post" action="{{ route('modulOptatiuCertificat.emit', ['certificat' => $certificat->id]) }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-success">Emetre certificats PDF</button>
        </form>
    @else
        <div class="alert alert-danger mt-3" role="alert">
            S'ha d'avaluar tot l'alumnat i guardar les notes per poder emetre els certificats.
        </div>
    @endif
@endsection
