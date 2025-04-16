<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$id}}" class="well profile_view">
        <div  class="col-sm-12">
            <h4 class="brief"><em>{{ $cab1 }}</em></h4>
            <div class="left col-xs-8">
                <p><em>{{ $cab2 }}</em></p>
                <ul class="list-unstyled">
                    {{ $slot }}
                </ul>
            </div>
            <div class="right col-xs-4 text-center">
                <img src="{{$title}}" alt="" height="90px" width="70px"
                     class="img-circle img-responsive">
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-4 emphasis">
                <p class="ratings">
                    {{ $rattings }}
                </p>
            </div>
            <div class="col-xs-12 col-sm-6 emphasis">
                {{ $botones }}
            </div>
        </div>
    </div>
</div>
