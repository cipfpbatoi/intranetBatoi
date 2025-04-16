@props(['panel'])
<tr>
    @foreach ($panel->getRejilla() as $item)
        <th>
            @php($label = trans("validation.attributes." . trim($item, 'X')))
            {{ str_contains($label, 'alidation.') ? ucwords($item) : $label }}
        </th>
    @endforeach
    <th>@lang("validation.attributes.operaciones")</th>
    {{ $slot }} {{-- Permet injectar columnes extra --}}
</tr>