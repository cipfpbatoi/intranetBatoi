<table border="1" >
        @if ($datosInforme['orientacion']=='portrait')
            <colgroup><col witdh='90'><col width="599"/><col width="329"/></colgroup>
        @else    
            <colgroup><col witdh='90'><col width="899"/><col width="629"/></colgroup>
        @endif    
        <tr>
            <td style="text-align:left;width:3.283cm;"><img src="{{public_path('img/pdf/logo.png')}}" alt="logo" style="width:60px;height:60px" /></td>
            <td style="text-align:left;font-size: 1.2em;padding-left: 5px"><strong>{{ $datosInforme['descripcion'] }}</strong></td>
            <td style="font-size: 0.9em">Página 1 de 1</td>
        </tr>
</table>
