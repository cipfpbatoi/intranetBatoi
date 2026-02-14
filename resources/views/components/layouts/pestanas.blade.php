@foreach ($pestanas as $pestana)
    @php($nom = $pestana->getNombre())
    @section($nom)
        @include('intranet.partials.components.loadBefores')
         <x-botones :panel="$panel" tipo="index" :elemento="$elemento ?? null" />
         @if (!in_array($nom, ['grid', 'profile']))
            <x-botones :panel="$panel" tipo="{{ $nom }}" :elemento="$elemento ?? null" />
         @endif
         @include($pestana->getVista(), $pestana->getFiltro())
    @endsection
@endforeach