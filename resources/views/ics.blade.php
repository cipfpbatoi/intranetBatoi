@php
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="cal.ics"');
@endphp
{{ $vCalendar->render() }}