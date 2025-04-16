@foreach ($panel->getLastPestanaWithModals() as $include)
    @include('intranet.partials.modal.' . $include)
@endforeach

