@props(['panel', 'pestana' => null])
@php($rejilla = ($pestana && $pestana->getRejilla()) ? $pestana->getRejilla() : $panel->getRejilla())
<tr>
    @foreach ($rejilla as $item)
        <th>
            @php($label = __("validation.attributes." . trim($item, 'X')))
            {{ str_contains($label, 'alidation.') ? ucwords($item) : $label }}
        </th>
    @endforeach
    <th>@lang("validation.attributes.operaciones")</th>
    {{ $slot }} {{-- Permet injectar columnes extra --}}
</tr>
