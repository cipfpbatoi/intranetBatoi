<x-layouts.app title="Historial d'importacions">
    <h3>Importacions recents</h3>

    @if (isset($runs) && count($runs))
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipus</th>
                    <th>Estat</th>
                    <th>Progrés</th>
                    <th>Missatge</th>
                    <th>Inici</th>
                    <th>Fi</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($runs as $run)
                    <tr>
                        <td>{{ $run->id }}</td>
                        <td>{{ $run->type }}</td>
                        <td>{{ $run->status }}</td>
                        <td>{{ $run->progress }}%</td>
                        <td>{{ $run->message }}</td>
                        <td>{{ optional($run->started_at)->format('d/m/Y H:i:s') }}</td>
                        <td>{{ optional($run->finished_at)->format('d/m/Y H:i:s') }}</td>
                        <td>
                            <a href="{{ route('import.status', ['importRunId' => $run->id]) }}" target="_blank">JSON</a>
                        </td>
                    </tr>
                    @if (!empty($run->error))
                        <tr>
                            <td colspan="8" class="text-danger"><strong>Error:</strong> {{ $run->error }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hi ha importacions registrades.</p>
    @endif

    <p><a href="{{ route('import.create') }}">Tornar a importació general</a></p>
    <p><a href="{{ route('teacherImport.create') }}">Tornar a importació professorat</a></p>
</x-layouts.app>
