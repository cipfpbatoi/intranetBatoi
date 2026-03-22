<x-layouts.app title="Resultats de la importació">
    @if (isset($importRunId))
        <h4>Importació en segon pla</h4>
        <p><strong>ID:</strong> {{ $importRunId }}</p>
        <p id="import-status-text">Estat: en cua</p>
        <p id="import-status-message"></p>
        <p id="import-status-error" class="text-danger"></p>

        <p>
            Pots tancar esta finestra i tornar més tard.
        </p>

        <a class="btn btn-primary" href="{{ route('import.status', ['importRunId' => $importRunId]) }}" target="_blank">
            Veure JSON d'estat
        </a>

        @push('scripts')
            <script>
                (function () {
                    var importRunId = @json($importRunId);
                    var statusText = document.getElementById('import-status-text');
                    var statusMessage = document.getElementById('import-status-message');
                    var statusError = document.getElementById('import-status-error');

                    function refreshStatus() {
                        fetch('/import/status/' + importRunId, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                            .then(function (response) {
                                if (!response.ok) {
                                    throw new Error('No s\\'ha pogut consultar l\\'estat');
                                }
                                return response.json();
                            })
                            .then(function (payload) {
                                statusText.textContent = 'Estat: ' + payload.status + ' (' + payload.progress + '%)';
                                statusMessage.textContent = payload.message || '';
                                statusError.textContent = payload.error || '';

                                if (payload.status === 'done' || payload.status === 'failed') {
                                    clearInterval(intervalId);
                                }
                            })
                            .catch(function (error) {
                                statusError.textContent = error.message;
                            });
                    }

                    refreshStatus();
                    var intervalId = setInterval(refreshStatus, 3000);
                })();
            </script>
        @endpush
    @else
        <h4>Importació completada</h4>
        <p>Revisa els missatges del sistema per al detall.</p>
    @endif
</x-layouts.app>
