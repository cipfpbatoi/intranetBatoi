<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Calendari FCT</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 100%; }
        .month-table { width: 45%; display: inline-block; vertical-align: top; margin-right: 2%; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; font-size: 10px; }
        th { background-color: #f2f2f2; font-size: xx-small   }
        h2 { text-align: center; margin-bottom: 20px; }
        .festiu { background-color: #ffcccc; color: red; font-weight: bold; }
        .hores-zero { background-color: #fff2cc; color: #d39e00; font-weight: bold; }
        .page-break { page-break-after: always; }
        .month-header { background-color: #ddd; padding: 5px; text-align: center; }
    </style>
</head>
<body>

<h2>Calendari de FCT de {{ $alumnoFct->fullName }} a {{ $alumnoFct->Fct->Colaboracion->Centro->nombre}}</h2>
<p>Total d'hores previstes: <strong>{{ $totalHours }}</strong></p>

<div class="container">
    @php
        $count = 0;
    @endphp

    @foreach($monthlyCalendar as $month => $days)
        @php
            $monthName = \Carbon\Carbon::createFromFormat('F', $month)->locale('ca')->translatedFormat('F');
            $firstDayOfMonth = \Carbon\Carbon::parse("first day of $month")->startOfMonth();
            $lastDayOfMonth = \Carbon\Carbon::parse("last day of $month")->endOfMonth();
            $startWeekday = $firstDayOfMonth->dayOfWeek; // 0 = diumenge, 1 = dilluns...
        @endphp

        <div class="month-table">
            <h3 class="month-header">{{ ucfirst($monthName) }}</h3>
            <table>
                <thead>
                <tr>
                    <th>Dl.</th>
                    <th>Dt.</th>
                    <th>Dm.</th>
                    <th>Dj.</th>
                    <th>Dv.</th>
                    <th>Ds.</th>
                    <th>Dg.</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $day = 1;
                @endphp

                @while ($day <= $lastDayOfMonth->day)
                    <tr>
                        @for ($i = 1; $i <= 7; $i++)
                            @if (($day == 1 && $i < $startWeekday) || $day > $lastDayOfMonth->day)
                                <td></td>
                            @else
                                @php
                                    $currentDate = \Carbon\Carbon::parse($firstDayOfMonth->format('Y-m') . '-' . str_pad($day, 2, '0', STR_PAD_LEFT));
                                    $dayData = collect($days)->firstWhere('dia_numero', $day);
                                    $isFestiu = $dayData ? \Intranet\Entities\CalendariEscolar::esFestiu($currentDate) : false;
                                    $horesPrevistes = isset($dayData['hores_previstes']) ?  $dayData['hores_previstes'] : null;
                                @endphp
                                <td class="{{ $isFestiu ? 'festiu' : '' }} {{ !$isFestiu && $horesPrevistes ==  0 ? 'hores-zero' : '' }}">
                                    {{ $day }}<br>
                                    {{ $horesPrevistes ?? '' }}
                                </td>
                                @php
                                    $day++;
                                @endphp
                            @endif
                        @endfor
                    </tr>
                @endwhile
                </tbody>
            </table>
        </div>

        @php
            $count++;
        @endphp

                <!-- Salt de pàgina cada 2 mesos -->
        @if ($count % 6 == 0)
            <div class="page-break"></div>
        @endif

    @endforeach
</div>

</body>
</html>
