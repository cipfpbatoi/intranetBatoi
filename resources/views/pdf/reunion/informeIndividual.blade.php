@extends('layouts.pdf')
@section('css')
    <style type="text/css">
        @page { size: 21cm 29.7cm; margin-left: 1.5cm; margin-right: 1.5cm; margin-bottom: 1.27cm }
        @page:first { }
        p { margin-bottom: 0.25cm; line-height: 115%; text-align: justify; background: transparent }
        p.normal { margin-top: 0.14cm; margin-bottom: 0cm; line-height: 100%; text-align: center}
        p.left { margin-top: 0.14cm; margin-bottom: 0cm; line-height: 100%; text-align: justify}
        a:link { color: #000080; so-language: zxx; text-decoration: underline }
        p.title {margin-top: 0.14cm; margin-bottom: 0cm; line-height: 100%;font-size: 16pt;color:#0000ff;text-align: center}
        div.displayed { display: block; margin-left: 100px; margin-right: 100px; width: 200px }
    </style>
@endsection
@section('content')
<p align="center" class="normal"><br/><br/><br/><br/></p>
<p align="center" class="normal" style="font-size: 36pt">
    <b>Informe individual </b>
</p>
<p class="normal"><br/><br/><br/></p>
<p class="normal">
    <img src="{{url('img/pdf/logo.png')}}" alt="logo" style="width:353px;height:369px" /><br/>
</p>
<p align="center" class="normal"><br/><br/><br/></p>
<p class="normal" style="font-weight:bold;margin-top: 0.14cm; margin-bottom: 0cm; line-height: 100%;font-family:Miriam Libre;font-size: 26pt">
Curs 2019-20</p>
<p align="center" class="normal" style="page-break-before: always"><br/></p>
<p class="title"> _______DADES IDENTIFICATIVES__________________________________</p>
<p class="normal"><br/></p>
<p class="left"><b>NIA:</b> {{ $todos->nia }}</p>
<p class="left"><b>COGNOMS I NOM:</b> {{ $todos->nameFull }}</p>
<p class="left"><b>CICLE FORMATIU:</b> {{$todos->Grupo->first()->Ciclo->literal}}</p>
<p class="left"><b>CURS:</b> {{$todos->Grupo->first()->curso}}</p>
<p class="normal"><br/><br/></p>
<p class="title">_______DADES ACADÈMIQUES___________________________________</p>
<p class="normal"><br/></p>
<p class="left"><b>Desenvolupament general de les capacitats</b></p>
<p class="normal"><br/></p>
@foreach(config('auxiliares.capacitats') as $index => $capacitat)
    @if ($todos->pivot->capacitats == $index )
        <p class="left" style="color: #00aeef"><b>{{$capacitat}}</b></p>
    @else
        <p class="left">{{$capacitat}}</p>
    @endif
@endforeach
<p class="normal"><br/></p>
<p class="left"><b>Adequació global entre capacitats, nivell d’aprenentatge, competències i
        interessos d’acord amb l’observació i la valoració de l’equip
        docent</b></p>
<p class="normal"><br/></p>
@foreach(config('auxiliares.promociona') as $index => $capacitat)
    @if ($todos->pivot->capacitats == $index )
        <p class="left" style="color: #00aeef"><b>{{$capacitat}}</b></p>
    @else
        <p class="left">{{$capacitat}}</p>
    @endif
@endforeach
<p class="normal"><br/></p>
<p class="left"><b>Qualificacions dels diferents mòduls</b></p>
<p class="left">Les qualificacions oficials poden consultar-se a la web familia en la
    següent direcció web: <a href="https://familia.edu.gva.es/" style="color:#1155cc"><u>https://familia.edu.gva.es/</u></a>.
    Si tenen qualsevol problema o dubte per a poder entrar a la mateixa,
    poden ficar-se en contacte amb la secretaria del centre mitjançant
    el telèfon 966 52 76 60 o als correus electrònics
    <a href="mailto:03012165.secret@gva.es" style="color:#1155cc"><u>03012165.secret@gva.es</u></a>
    o <a href="mailto:secretaria@cipfpbatoi.es" style="color:#1155cc"><u>secretaria@cipfpbatoi.es</u></a>
</p>
<p class="normal"><br/><br></p>
<p class="title">_______INFORMACIÓ DELS MÒDULS______________________________</p>
<p class="normal"><br/></p>
<p class="left"><b>A continuació es detallen els continguts no impartits durant el 3r
        trimestre en el teu grup</b></p>
<p class="normal"><br/></p>
@foreach ($todos->Grupo->first()->Modulos as $modulo)
    @foreach ($modulo->resultados->where('evaluacion',3) as $res)
        <p class="left">{{$modulo->Xmodulo}}: {{$res->adquiridosNO}}</p>
    @endforeach
@endforeach
<p style="margin-top: 0.14cm; margin-bottom: 0cm; line-height: 100%; page-break-before: always"><br/></p>
<p class="left"><b>Valoració del teu treball durant el 3r trimestre</b></p>
<p class="normal"><br/></p>
@foreach ($todos->AlumnoResultado as $resultado)
    <p class="left">{{$resultado->modulo}}: {{$resultado->valoracion}}</p>
@endforeach
<p class="normal"><br/><br/></p>
<p class="title">_______ALTRES DADES D’INTERÉS________________________________</p>
<p class="normal"><br/></p>
@foreach ($todos->AlumnoResultado as $resultado)
    @if ($resultado->observaciones)
        <p class="left">{{$resultado->modulo}}: {{$resultado->observaciones}}</p>
    @endif
@endforeach
<p class="normal"><br/><br/></p>
<p class="title">_______DECISIÓ DE L’EQUIP EDUCATIU___________________________</p>
<p class="normal"><br/></p>
<p class="left">Reunits l’equip educatiu en sessió d’avaluació, es decideix la
    @if ($todos->pivot->capacitats == 3)
        <em style="color:#f00">NO promoció </em>
    @else
        <em style="color:#f00">promoció </em>
    @endif
    de curs.</p>
<p class="normal"><br/><br/><br/><br/><br/></p>
<p align="center" class="normal">
    Alcoi a ___13___ de ____Juny_____ de 2020</p>
<p align="center" class="normal"><br/><br/><br/><br/></p>
<div class="displayed" style="float:left">
    El tutor o tutora <br/><br/><br/><br/><br/><br/>
    {{ $todos->Grupo()->first()->xTutor }}
</div>
<div class="displayed" style="float:right">
    Segell del centre <br/><br/>
    <img src="{{url('img/pdf/segell.png')}}" alt="logo" style="width:114px;height:108px" />
</div>
@endsection