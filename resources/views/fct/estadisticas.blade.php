<x-layouts.app  title="Estadistiques FCT {{curso()}}">
    <!-- page content -->
    <div class="x_content">
        <h2>Estadístiques FCT {{curso()}}</h2>
        <p>
            <a class="btn btn-sm btn-primary" href="{{ route('fct.stat.xlsx') }}">
                Exporta a Excel
            </a>
        </p>
        @php
            $bloques = [
                [
                    'title' => 'Estadístiques 1r',
                    'grupos' => $grupos1,
                    'ciclos' => $ciclos1,
                ],
                [
                    'title' => 'Estadístiques 2n',
                    'grupos' => $grupos2,
                    'ciclos' => $ciclos2,
                ],
            ];
        @endphp

        @foreach ($bloques as $bloque)
            <h3>{{ $bloque['title'] }}</h3>
            <table
                id="datatable-stats-{{ $loop->iteration }}"
                class="table table-striped table-bordered"
                style="border: #00aeef 2px solid; border-collapse: separate; border-spacing: 5px;"
            >
                <thead>
                <tr>
                    <th scope="col" style="border: 1px solid #00aeef; padding: 5px;">Grup / Cicle</th>
                    <th scope="col" style="border: 1px solid #00aeef; padding: 5px;">Matriculats</th>
                    <th scope="col" style="border: 1px solid #00aeef; padding: 5px;">Fcts</th>
                    <th scope="col" style="border: 1px solid #00aeef; padding: 5px;">Exempts</th>
                    <th scope="col" style="border: 1px solid #00aeef; padding: 5px;">Projecte</th>
                    <th scope="col" style="border: 1px solid #00aeef; padding: 5px;">Inserció</th>
                    <th scope="col" style="border: 1px solid #00aeef; padding: 5px;">Acta</th>
                    <th scope="col" style="border: 1px solid #00aeef; padding: 5px;">Qualitat</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($bloque['grupos'] as $grupo)
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
                    @foreach ($bloque['ciclos'] as $key => $resCiclo)
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
                </tbody>
            </table>
        @endforeach
    </div>

</x-layouts.app>
