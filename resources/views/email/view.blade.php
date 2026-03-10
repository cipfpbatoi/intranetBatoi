@extends('layouts.intranet')
@section('content')

    <form method="post" action="{{ route($action) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="route" value="{{ $route }}">
        <input type="hidden" name="register" value="{{ $register }}">
        <input type="hidden" name="class" value="{{ $class }}">
        <input type="hidden" name="editable" value="{{ $editable }}">
        <div class="form-group">
            <label for="from">De</label>
            <input name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="form-group">
            <label for="fromPerson">Nom</label>
            <input name="fromPerson" class="form-control" value="{{ $fromPerson }}">
        </div>
        <div class="form-group">
            <label for="to">Per a</label>
            <textarea name="to" class="resizable_textarea form-control">{{ $to }}</textarea>
        </div>
        <div class="form-group">
            <label for="toPeople">Adreçat a</label>
            <input name="toPeople" class="form-control" value="{{ $toPeople }}">
        </div>
        <div class="form-group">
            <label for="subject">Assumpte</label>
            <input name="subject" class="form-control" value="{{ $subject }}">
        </div>
        <div class="form-group">
            @if (!$editable)
                <label for="contenido">Exemple</label>
                <textarea id="contenido" class="form-control" style="display:none" name="contenido">{{ $template }}</textarea>
            @else
                <label for="content">Contingut</label>
                <textarea id="content" class="form-control" style="display:none" name="contenido">{{ $contenido }}</textarea>
            @endif
        </div>
    <div class="btn-toolbar editor" data-role="editor-toolbar" data-target="#area">
        <div class="btn-group">
            <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font Size"><i class="fa fa-text-height"></i>
                &nbsp<b class="caret"></b></a>
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
            <a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)">
                <i class="fa fa-align-left"></i>
            </a>
            <a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)">
                <i class="fa fa-align-center"></i>
            </a>
            <a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)">
                <i class="fa fa-align-right"></i>
            </a>
            <a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)">
                <i class="fa fa-align-justify"></i>
            </a>
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
    <div id="area" class="editor-wrapper">{!! $contenido !!} </div>
    <div class="form-group">
            <label for="file">Adjunt</label>
            <input type="file" name="file[]" multiple>
    </div>
    <button type="submit" class="btn btn-info">Submit</button>
    </form>
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
