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
    <img src="{{url('img/pdf/insti.jpg')}}" alt="logo" style="width:353px;height:369px" /><br/>
</p>
<p align="center" class="normal"><br/><br/><br/></p>
<p class="normal" style="font-weight:bold;margin-top: 0.14cm; margin-bottom: 0cm; line-height: 100%;font-family:Miriam Libre;font-size: 26pt">
Curs 2019-20</p>
<p align="center" class="normal" style="page-break-before: always"><br/></p>
<p class="title"> _____DADES IDENTIFICATIVES__________________________________</p>
<p class="normal"><br/></p>
<p class="left"><b>NIA</b>:</p>
<p class="left"><b>COGNOMS I NOM</b>:</p>
<p class="left"><b>CICLE FORMATIU:</b></p>
<p class="left"><b>CURS:</b></p>
<p class="normal"><br/><br/><br/></p>
<p class="title">_____DADES ACADÈMIQUES___________________________________</p>
<p class="normal"><br/></p>
<p class="left"><b>Desenvolupament general de les capacitats</b></p>
<p class="left">Escriure les 4  i marcar la seleccionada (per a poder comparar entre les
    opcions que hi havien disponibles on es troba l’alumne)</p>
<p class="normal"><br/></p>
<p class="left"><b>Adequació global entre capacitats, nivell d’aprenentatge, competències i
        interessos d’acord amb l’observació i la valoració de l’equip
        docent</b></p>
<p class="left">Escriure les 4 i marcar la seleccionada  (per a poder comparar entre les
    opcions que hi havien disponibles on es troba l’alumne)</p>
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
<p class="normal"><br/><br><br/><br/></p>
<p class="title">_____INFORMACIÓ DELS MÒDULS______________________________</p>
<p class="normal"><br/></p>
<p class="left"><b>A continuació es detallen els continguts no impartits durant el 3r
        trimestre en el teu grup</b></p>
<p class="left">Part d’observacions que agafem del seguiment</p>
<p class="normal"><br/></p>
<p class="left"><b>Valoració del teu treball durant el 3r trimestre</b></p>
<p class="left">Agafar per als diferents mòduls de la llista desplegable que cada
    professor/a ha introduït en el seguiment.</p>
<p class="normal"><br/><br/><br/></p>
<p style="margin-top: 0.14cm; margin-bottom: 0cm; line-height: 100%; page-break-before: always"><br/></p>
<p class="normal"><br/><br/><br/></p>
<p class="title">_____ALTRES DADES D’INTERÉS________________________________</p>
<p class="normal"><br/></p>
<p class="left">Agafar les observacions introduïdes pel professorat per a cada mòdul de
    l’alumne/a en qüestió.</p>
<p class="normal"><br/><br/><br/></p>
<p class="title">_____DECISIÓ DE L’EQUIP EDUCATIU___________________________</p>
<p class="normal"><br/></p>
<p class="left">Reunits l’equip educatiu en sessió d’avaluació, es decideix la
    <em style="color:#f00">promoció/ o no promoció</em> de curs.</p>
<p class="normal"><br/><br/><br/><br/><br/></p>
<p align="center" class="normal">
    Alcoi a __13___ de ___Juny________ de 2020</p>
<p align="center" class="normal"><br/><br/><br/><br/></p>
<div class="displayed" style="float:left">
    El tutor o tutora <br/><br/><br/><br/><br/><br/>
    Nom i cognoms del tutor/a
</div>
<div class="displayed" style="float:right">
    Segell del centre <br/><br/>
    <img src="{{url('img/pdf/segell.png')}}" alt="logo" style="width:114px;height:108px" />
</div>
<div title="footer"><p align="right" style="margin-top: 0.13cm; margin-bottom: 0cm; line-height: 100%"><br/>
    </p>
</div>
@endsection