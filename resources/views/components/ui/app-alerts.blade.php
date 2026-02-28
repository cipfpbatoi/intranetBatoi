@foreach ($messages as $msg)
    <div class="alert alert-block alert-{{ $msg['type'] ?? 'info' }} fade in">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {!! $msg['message'] ?? '' !!}
    </div>
@endforeach
