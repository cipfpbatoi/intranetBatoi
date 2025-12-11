<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Calendari FCT</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 100%; text-align: center; }
        .month-table { display: inline-block; width: 45%; margin: 0 2% 12px 2%; vertical-align: top; page-break-inside: avoid; text-align: left; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; font-size: 10px; }
        th { background-color: #f2f2f2; font-size: xx-small   }
        h2 { text-align: center; margin-bottom: 20px; }
        .festiu { background-color: #ffcccc; color: red; font-weight: bold; }
        .hores-zero { background-color: #fff2cc; color: #d39e00; font-weight: bold; }
        .page-break { page-break-after: always; }
        .month-header { background-color: #ddd; padding: 5px; text-align: center; }
        .legend { margin: 10px 0 20px 0; font-size: 11px; }
        .legend-item { display: inline-flex; align-items: center; margin-right: 12px; }
        .legend-color { width: 12px; height: 12px; display: inline-block; margin-right: 6px; border: 1px solid #000; }
    </style>
<body>
<h2>{{ $titol }}</h2>
<p>Total d'hores previstes: <strong>{{ $totalHours }}</strong></p>

@if(!empty($legend))
    <div class="legend">
        @foreach($legend as $item)
            <span class="legend-item">
                <span class="legend-color" style="background-color: {{ $item['color'] }}"></span>
                {{ $item['nom'] }}
            </span>
        @endforeach
    </div>
@endif

<div class="container">
    @php $count = 0; @endphp
    @foreach($monthlyCalendar as $month => $days)
        @php
            $monthDate = \Carbon\Carbon::createFromFormat('Y-m-d', $month . '-01')->locale('ca');
            $monthName = $monthDate->translatedFormat('F Y');
            $firstDayOfMonth = $monthDate->copy()->startOfMonth();
            $lastDayOfMonth = $monthDate->copy()->endOfMonth();
            $startWeekday = $firstDayOfMonth->dayOfWeekIso; // 1 = dilluns ... 7 = diumenge
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
                                    $currentDate = $firstDayOfMonth->copy()->day($day);
                                    $dayData = collect($days)->firstWhere('dia_numero', $day);
                                    $isFestiu = $dayData ? \Intranet\Entities\CalendariEscolar::esFestiu($currentDate) : false;
                                    $horesPrevistes = isset($dayData['hores_previstes']) ?  $dayData['hores_previstes'] : null;
                                    $color = $dayData['color'] ?? null;
                                @endphp
                                <td class="{{ $isFestiu ? 'festiu' : '' }} {{ !$isFestiu && $horesPrevistes ==  0 ? 'hores-zero' : '' }}" style="{{ !$isFestiu && $color ? 'background-color: '.$color : '' }}">
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
        @php $count++; @endphp
        @if ($count % 6 === 0 && !$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</div>

</body>
</html>
