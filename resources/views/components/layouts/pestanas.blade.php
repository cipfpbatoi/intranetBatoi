@foreach ($pestanas as $pestana)
    @php($nom = $pestana->getNombre())
    @php($hideTopButtons = $panel->getModel() === 'Fct' && $nom === 'Finalizats')
    @section($nom)
        @include('intranet.partials.components.loadBefores')
         @if (!$hideTopButtons)
            <x-botones :panel="$panel" tipo="index" :elemento="$elemento ?? null" />
         @endif
         @if (!$hideTopButtons && !in_array($nom, ['grid', 'profile']))
            <x-botones :panel="$panel" tipo="{{ $nom }}" :elemento="$elemento ?? null" />
         @endif
         @include($pestana->getVista(), $pestana->getFiltro())
    @endsection
@endforeach
