@extends('layouts.intranet')
@section('content')
    
    {!! Form::open(['url' =>  'myMail','enctype'=> 'multipart/form-data' ]) !!}
    
        {!! Form::hidden('route',$route) !!}
        {!! Form::hidden('register',$register) !!}
        {!! Form::hidden('class',$class) !!}
        <div class="form-group">
            {!! Form::label('from', 'De') !!}
            {!! Form::text('from', $from, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('fromPerson', 'Nom') !!}
            {!! Form::text('fromPerson', $fromPerson, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('to', 'Per a') !!}
            {!! Form::textarea('to', $to, ['class' => 'resizable_textarea form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('toPeople', 'Adreçat a') !!}
            {!! Form::text('toPeople', $toPeople, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('subject', 'Assumpte') !!}
            {!! Form::text('subject', $subject, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('contenido', 'Contingut') !!}
            {!! Form::textarea('contenido',$contenido, ['id'=>'contenido','class' => 'form-control','style'=>'display:none']) !!}
        </div>

        <div class="btn-toolbar editor" data-role="editor-toolbar" data-target="#area">
        <div class="btn-group">
            <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font Size"><i class="fa fa-text-height"></i>&nbsp;<b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li>
                    <a data-edit="fontSize 5">
                        <p style="font-size:17px">Huge</p>
                    </a>
                </li>
                <li>
                    <a data-edit="fontSize 3">
                        <p style="font-size:14px">Normal</p>
                    </a>
                </li>
                <li>
                    <a data-edit="fontSize 1">
                        <p style="font-size:11px">Small</p>
                    </a>
                </li>
            </ul>
        </div>

        <div class="btn-group">
            <a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>
            <a class="btn" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><i class="fa fa-italic"></i></a>
            <a class="btn" data-edit="strikethrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></a>
            <a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>
        </div>

        <div class="btn-group">
            <a class="btn" data-edit="insertunorderedlist" title="Bullet list"><i class="fa fa-list-ul"></i></a>
            <a class="btn" data-edit="insertorderedlist" title="Number list"><i class="fa fa-list-ol"></i></a>
            <a class="btn" data-edit="outdent" title="Reduce indent (Shift+Tab)"><i class="fa fa-dedent"></i></a>
            <a class="btn" data-edit="indent" title="Indent (Tab)"><i class="fa fa-indent"></i></a>
        </div>

        <div class="btn-group">
            <a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><i class="fa fa-align-left"></i></a>
            <a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><i class="fa fa-align-center"></i></a>
            <a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><i class="fa fa-align-right"></i></a>
            <a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><i class="fa fa-align-justify"></i></a>
        </div>

        <div class="btn-group">
            <a class="btn dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><i class="fa fa-link"></i></a>
            <div class="dropdown-menu input-append">
                <input class="span2" placeholder="URL" type="text" data-edit="createLink" />
                <button class="btn" type="button">Add</button>
            </div>
            <a class="btn" data-edit="unlink" title="Remove Hyperlink"><i class="fa fa-cut"></i></a>
        </div>

        <div class="btn-group">
            <a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><i class="fa fa-undo"></i></a>
            <a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><i class="fa fa-repeat"></i></a>
        </div>
    </div>
    <div id="area" class="editor-wrapper">{!! $contenido  !!} </div>
    <div class="form-group">
            {!! Form::label('file', 'Adjunt') !!}
            {!! Form::file('file') !!}
    </div>
    {!! Form::submit('Submit', ['class' => 'btn btn-info']) !!}
    {!! Form::close() !!}
@endsection
@section('titulo')
    Enviar correu
@endsection
@section('scripts')
    {{ Html::script('js/MyMail/create.js') }}
    @if (!$editable)
        {{ Html::script('js/MyMail/block.js') }}
    @endif
@endsection
