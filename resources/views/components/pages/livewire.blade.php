@props([
    'title',
    'component',
    'params' => [],

])

@component('components.layouts.app', ['title' => $title])
    @push('styles')
        <style>
            table, th, td {
                border: 1px solid;
            }
        </style>
        @livewireStyles
    @endpush

    <div class="container mx-auto p-4">
        @if (!empty($component))
            @if (!empty($params))
                @livewire($component, $params)
            @else
                @livewire($component)
            @endif
        @else
            <div class="alert alert-warning">⚠️ Cap component Livewire indicat.</div>
        @endif
    </div>

    @push('scripts')
        @livewireScripts
    @endpush
@endcomponent
