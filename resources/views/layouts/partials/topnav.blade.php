<!-- top navigation -->
<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>
            
            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        {{ AuthUser()->nombre}} {{AuthUser()->apellido1}}
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu pull-right"> 
                        @include('layouts.partials.topmenu')
                    </ul>
                </li>
                @if (!isset(AuthUser()->nia))
                    <li class="">
                        <a href="{{url('/ficha')}}">
<!--                            <a href="{{url('/api/doficha?dni='.AuthUser()->dni.'&api_token='.AuthUser()->api_token)}}">-->
                            @if ( estaDentro())
                                {!! Html::image('img/clock-icon.png' ,'reloj',array('class' => 'iconomediano', 'id' => 'imgFitxar')) !!}
                            @else
                                {!! Html::image('img/clock-icon-rojo.png' ,'reloj',array('class' => 'iconomediano', 'id' => 'imgFitxar')) !!}
                            @endif
                        </a>
                    </li>
                @endif
                <li role="presentation" class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-envelope-o"></i>
                        <span class="badge bg-green">@if (count(AuthUser()->unreadNotifications)){{ count(AuthUser()->unreadNotifications) }} @endif</span>
                    </a>
                    <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                    @foreach (AuthUser()->unreadNotifications()->paginate(6) as $notifications)
                        <li id='{{$notifications->id}}'>
                            <a class="papelera" href="/notification/{{$notifications->id}}/delete">
                                <span class="image"><img src="/img/delete.png" alt="Marcar como leida" class="iconopequeno" /></span>
                            </a>
                            <a href="{{$notifications->data['enlace']}}">
                                <span>
                                    <span>{{$notifications->data['emissor']}}</span>
                                    <span class="time">{{$notifications->data['data']}}</span>
                                </span>
                                <span class="message">
                                    {{$notifications->data['motiu']}}
                                </span>
                            </a>
                        </li>
                    @endforeach
                            <div class="text-center">
                                <a href="/notification">
                                    <strong>@lang("messages.buttons.seeAll")</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->

