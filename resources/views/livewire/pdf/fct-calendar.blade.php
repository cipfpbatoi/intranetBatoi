<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Calendari FCT</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 100%; }
        .month-table { width: 22%; display: inline-block; vertical-align: top; margin-right: 2%; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; font-size: 12px; }
        th { background-color: #f2f2f2; }
        h3 { text-align: center; background-color: #ddd; padding: 5px; }
        .festiu { background-color: #ffcccc; color: red; font-weight: bold; }
        .page-break { page-break-after: always; }
    </style>
 </head>
<body>

<h2>Calendari de FCT de {{ $alumnoFct->fullName }} a {{ $alumnoFct->Fct->Colaboracion->Centro->nombre}}</h2>
<p>Total d'hores previstes: <strong>{{ $totalHours }}</strong></p>

<div class="container">
    @php $count = 0; @endphp

    @foreach($monthlyCalendar as $month => $days)
        @php
            // Convertir el mes a valencià
            $monthName = \Carbon\Carbon::createFromFormat('F', $month)->locale('ca')->translatedFormat('F');
        @endphp

        <div class="month-table">
            <h3>{{ ucfirst($monthName) }}</h3>
            <table>
                <thead>
                <tr>
                    <th>Dia</th>
                    <th>Hores Previstes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($days as $day)
                    @php
                        $date = \Carbon\Carbon::parse($day['dia']);
                        $isFestiu = \Intranet\Entities\CalendariEscolar::esFestiu($date);
                    @endphp
                    <tr class="{{ $isFestiu ? 'festiu' : '' }}">
                        <td>{{ $day['dia_numero'] }}</td>
                        <td>{{ $day['hores_previstes'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @php $count++; @endphp

                <!-- Salt de pàgina cada 3 mesos -->
        @if ($count % 4 == 0)
            <div class="page-break"></div>
            <div class="container">
                @endif

                @endforeach
            </div>

</body>
</html>