@foreach ($messages as $msg)
    <div class="alert alert-{{ $msg['type'] ?? 'info' }} alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {!! $msg['message'] ?? '' !!}
    </div>
@endforeach
