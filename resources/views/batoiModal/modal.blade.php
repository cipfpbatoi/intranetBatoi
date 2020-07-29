<div id="{{$name}}" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">{{$title}}</h4>
            </div>
            <div class="modal-body">
                <form id="form{{ucwords($name)}}" action="{{$action}}" method="POST">
                    {{ csrf_field() }}
                    {{ $slot }}
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="form{{ucwords($name)}}" class="btn btn-primary">{{$message}}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>