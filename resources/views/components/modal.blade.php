<div id="{{$name}}" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog {{$clase}}">
        <div class="modal-content">
            <div class="modal-header">
                @if ($dismiss)
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                @endif
                <h5 class="modal-title">{{$title}}</h5>
            </div>
            <div class="modal-body">
                <form id="form{{ucwords($name)}}" action="{{$action}}" enctype="multipart/form-data" method="POST">
                    {{ csrf_field() }}
                    {{ $slot }}
                </form>
            </div>
            <div class="modal-footer">
                @if (strlen($message))
                    <button
                            type="submit"
                            form="form{{ucwords($name)}}"
                            class="submit btn btn-primary">
                        {{$message}}
                    </button>
                @endif
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{$cancel}}</button>
            </div>
        </div>
    </div>
</div>
