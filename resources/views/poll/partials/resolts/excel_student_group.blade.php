@php($groupOptions = $sheet['options'])
<table>
    <thead>
    <tr>
        <th>Nom i cognoms</th>
        @if ($groupOptions->count() === 1)
            <th>Opció triada</th>
        @else
            @foreach ($groupOptions as $item)
                <th>{{ $item->question_label }}</th>
            @endforeach
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach ($sheet['rows'] as $row)
        <tr>
            <td>{{ $row['student_name'] }}</td>
            @if ($groupOptions->count() === 1)
                <td>{{ $row['choices'][$groupOptions->first()->id] ?? '' }}</td>
            @else
                @foreach ($groupOptions as $item)
                    <td>{{ $row['choices'][$item->id] ?? '' }}</td>
                @endforeach
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
