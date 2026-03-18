@foreach ($messages as $msg)
    <div class="alert alert-{{ $msg['type'] ?? 'info' }} alert-dismissible fade show">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        {!! $msg['message'] ?? '' !!}
    </div>
@endforeach
