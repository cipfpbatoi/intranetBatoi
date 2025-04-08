<x-layouts.app  title="Estadistiques FCT {{curso()}}">
    <!-- page content -->
    <div class="x_content">
        <h2>Estadístiques FCT {{curso()}}</h2>
        <table style="border: #00aeef 2px solid; border-collapse: separate; border-spacing: 5px;">
            <thead>
            <tr>
                <td style="border: 1px solid #00aeef; padding: 5px;">Grup / Cicle</td>
                <td style="border: 1px solid #00aeef; padding: 5px;">Matriculats</td>
                <td style="border: 1px solid #00aeef; padding: 5px;">Fcts</td>
                <td style="border: 1px solid #00aeef; padding: 5px;">Exempts</td>
                <td style="border: 1px solid #00aeef; padding: 5px;">Projecte</td>
                <td style="border: 1px solid #00aeef; padding: 5px;">Inserció</td>
                <td style="border: 1px solid #00aeef; padding: 5px;">Acta</td>
                <td style="border: 1px solid #00aeef; padding: 5px;">Qualitat</td>
            </tr>
            </thead>
            @foreach ($grupos as $grupo)
                <tr>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{$grupo->nombre}}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{$grupo->matriculados}}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{$grupo->resFct}}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{$grupo->exentos}}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{$grupo->respro}}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{$grupo->resempresa}}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{$grupo->acta}}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{$grupo->calidad}}</td>
                </tr>
            @endforeach
            @foreach ($ciclos as $key => $resCiclo)
                <tr>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{\Intranet\Entities\Ciclo::find($key)->ciclo}}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{ $resCiclo['matriculados'] }}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{ $resCiclo['resfct']  }} de {{ $resCiclo['avalfct'] }}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{ $resCiclo['exentos'] }}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{ $resCiclo['respro'] }} de {{ $resCiclo['avalpro'] }}</td>
                    <td style="border: 1px solid #00aeef; padding: 5px;">{{ $resCiclo['resempresa'] }} de {{ $resCiclo['resfct'] }}</td>
                    <td colspan="2" style="border: 1px solid #00aeef; padding: 5px;"></td>
                </tr>
            @endforeach
        </table>
    </div>

</x-layouts.app>

