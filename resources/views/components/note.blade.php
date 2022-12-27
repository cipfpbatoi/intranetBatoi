<div class="col-md-4 col-sm-6 content-card">
<div class="card-big-shadow">
    <div class="card card-just-text" data-background="color" data-color="{{$color}}" data-radius="none">
        <div class="content">
            <h6 class="category">
                {{$name}}
                @if ($linkEdit != "#")
                    <a href="{{$linkEdit}}"><em class="fa fa-paperclip"> </em></a>
                @endif
            </h6>
            <a href="{{$linkShow}}">
                <h4 class="title">{{$title}}</h4>
                <p class="description">{{$message}}</p>
            </a>
        </div>
    </div> <!-- end card -->
</div>
</div>
