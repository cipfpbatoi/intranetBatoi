<div id="MapaCentro" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tancar"></button>
                <h5 class="modal-title">Mapa del centre de treball</h5>
            </div>
            <div class="modal-body">
                <p id="mapaCentroDireccio" class="text-muted"></p>
                <div id="mapaCentroMissatge" class="alert alert-info" style="display: none;"></div>
                <div class="mapa-centro-contenidor">
                    <iframe
                        id="mapaCentroFrame"
                        class="mapa-centro-frame"
                        title="Mapa del centre de treball"
                        loading="lazy"
                    ></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <a
                    id="mapaCentroEnllac"
                    href="#"
                    target="_blank"
                    rel="noopener"
                    class="btn btn-default"
                >
                    <em class="fa fa-external-link"></em> Obrir en OpenStreetMap
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tancar</button>
            </div>
        </div>
    </div>
</div>
