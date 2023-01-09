@extends('layouts.intranet')
    @section('content')
        @foreach ($items as $nombre => $item)
            <div class='col-lg-2 col-md-2 col-sm-3 col-xs-6'>
                <div class='borderedondo enlaceimagen centrado'>
                    @if (isset($item['full-url']))
                    <a target='_blank' href="{{ $item['full-url'] }}">
                    @endif
                    @if (isset($item['url']))
                    <a  href="{{ $item['url'] }}" >
                    @endif
                    @if (isset($item['img']))
                        {!! Html::image('img/'.$item['img'] ,$nombre,['data-toogle'=>'tooltip','title'=>trans('messages.menu.'.ucwords($nombre)),'class'=>'iconofit']) !!}
                    @else
                        {!! Html::image('img/'.$item['url'].'.png' ,$nombre,['data-toogle'=>'tooltip','title'=>trans('messages.menu.'.ucwords($nombre)),'class'=>'iconofit']) !!}
                    @endif
                    </a>
                </div>
            </div>
        @endforeach
    @endsection
    @section('titulo')
        PANEL DE CONTROL
    @endsection

