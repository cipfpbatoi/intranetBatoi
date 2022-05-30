<div id="{{$name}}" class="modal fade" role="dialog">
    <div class="modal-dialog {{$clase}}">
        <div class="modal-content">
            <div class="modal-header">
                @if ($dismiss)
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                @endif
                <h4 class="modal-title">{{$title}}</h4>
            </div>
            <div class="modal-body">
                <form id="form{{ucwords($name)}}" action="{{$action}}" method="POST">
                    {{ csrf_field() }}
                    {{ $slot }}
                </form>
            </div>
            <div class="modal-footer">
                @if (strlen($message))
                    <button type="submit" form="form{{ucwords($name)}}" class="btn btn-primary">{{$message}}</button>
                @endif
                <button type="button" class="btn btn-default" data-dismiss="modal">{{$cancel}}</button>
            </div>
        </div>
    </div>
</div>