<div class="container">
    <p><strong>COMUNICACIÓ A l'ALUMNAT SOBRE EL FINANÇAMENT PEL FONS SOCIAL
            EUROPEU DE LA FORMACIÓ PROFESSIONAL DE GRAU MITJÀ I GRAU SUPERIOR
            EN LA COMUNITAT VALENCIANA</strong></p>
    <table class="table table-bordered" style="width: 1000px">
        <colgroup>
            <col style="width:20%"/>
            <col style="width:50%"/>
            <col style="width:30%"/>
        </colgroup>
        <tr>
            <th colspan="3">Centre Educatiu</th>
        </tr>
        <tr>
            <th>Codi</th>
            <th>Denominació</th>
            <th>Municipi</th>
        </tr>
        <tr>
            <td><span style="font-size: 16px">{{ config('contacto.codi') }}</span></td>
            <td><span style="font-size: 16px">{{ config('contacto.nombre') }}</span></td>
            <td><span style="font-size: 16px">{{ config('contacto.poblacion') }}</span></td>
        </tr>
    </table>
    <br/><br/><br/>
    <table class="table table-bordered" style="width: 1000px">
        <colgroup>
            <col style="width: 15%">
            <col style="width: 35%">
            <col style="width: 50%">
        </colgroup>
        <tr>
            <td style="font-size:20px;text-align: left">Nom del cicle</td>
            <td style="font-size:20px;text-align: left"><strong>{{$grupo->Ciclo->vliteral}}</strong></td>
            <td style="font-size:20px;text-align: left">Grau:
                <strong>{{$grupo->Ciclo->tipo==1?'Mitjà':($grupo->Ciclo->tipo==2?'Superior':'Bàsic')}}</strong></td>
        </tr>
        <tr>
            <td style="font-size:20px;text-align: left">Curs/Grup</td>
            <td style="font-size:20px;text-align: left"><strong>{{$grupo->nombre}}</strong></td>
            <td style="font-size:20px;text-align: left">Data:<strong> {{hoy('d/m/Y')}} </strong></td>
        </tr>
        <tr>
            <td style="font-size:20px;text-align: left">Tutor/a</td>
            <td style="font-size:20px;text-align: left"><strong>{{$grupo->Tutor->FullName}}</strong></td>
            <td style="font-size:20px;text-align: left">
                Signatura: {!! Intranet\Services\SignaturaService::exec($grupo->Tutor->dni,'margin-left:100px',0.6,"<br/><br/><br/><br/>") !!}</td>
        </tr>
    </table>
    <p style="font-size: 20px">S'ha facilitat la informació sobre el finançament pel Fons Social Europeu dels cicles de
        Formació Professional de Grau Mitjà / Superior
        a la Comunitat Valenciana a l'alumnat següent: </p>
    <table class="table table-bordered" style="width:1000px">
        <colgroup>
            <col style="width: 50%">
            <col style="width: 50%">
        </colgroup>
        <tr>
            <th>Cognoms i Nom</th>
            <th>Cognoms i Nom</th>
        </tr>
        <tr>
            @foreach ($todos as $key => $elemento)
                <td>
                    <span style="font-size: 18px">{{$elemento->apellido1.' '.$elemento->apellido2.', '.$elemento->nombre}}</span>
                </td>
        @if ($key % 2 == 1)
            <tr/>
            <tr> @endif
                @endforeach
            </tr>
    </table>
</div>
