<div class="container" >
    <br/>
    <strong>Punts tractats:</strong>
    <ul style='list-style:none'>
        @foreach ($todos as $key => $elemento)
            <li>{{$key+1}}. <strong>{{$elemento->descripcion}}</strong>:</li>
            <li class="ident">@php echo($elemento->resumen) @endphp</li>
        @endforeach
    </ul>
</div>
