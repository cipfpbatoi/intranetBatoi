<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$id}}" class="well profile_view">
        <div  class="col-sm-12">
            <h4 class="brief">
                <em class='fa fa-calendar'></em> {{ $cab1 }} -
                @if (strlen($cab2 > 5))
                    <em class='fa fa-calendar'></em>
                @endif
                {{ $cab2 }}
            </h4>
            <h6>{!! $title !!}</h6>
            @isset($inside)
                <h6><em class="fa fa-clock-o"></em>
                    {{$inside}}
                </h6>
            @endisset
            <div class="left col-xs-12">
                <h5>{!! $subtitle !!}</h5>
                <ul class="list-unstyled">
                    {{ $slot }}
                </ul>
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-4 emphasis">
                <p class="ratings">
                    {{ $rattings }}
                </p>
            </div>
            <div class="col-xs-12 col-sm-8 emphasis">
                {{ $botones }}
            </div>
        </div>
    </div>
</div>
