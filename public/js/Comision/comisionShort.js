'use strict';

/**
 * @deprecated Fitxer legacy sense ús actual.
 * El flux de Comissió carrega `public/js/Comision/modal.js`.
 * Mantingut temporalment per compatibilitat històrica.
 */
(function () {
    var managedIds = [
        'servicio_id',
        'alojamiento_id',
        'comida_id',
        'gastos_id',
        'kilometraje_id',
        'medio_id',
        'marca_id',
        'matricula_id',
        'otros_id'
    ];

    function byId(id) {
        return document.getElementById(id);
    }

    function setDisabled(id, disabled) {
        var element = byId(id);
        if (element) {
            element.disabled = disabled;
        }
    }

    function updateItinerarioState() {
        var kilometraje = byId('kilometraje_id');
        var itinerario = byId('itinerario_id');

        if (!kilometraje || !itinerario) {
            return;
        }

        var kilometrajeRaw = (kilometraje.value || '').toString().trim().replace(',', '.');
        var kilometrajeValue = Number(kilometrajeRaw);
        var hasValidKilometraje = kilometrajeRaw !== '' && !Number.isNaN(kilometrajeValue) && kilometrajeValue > 0;

        if (kilometraje.disabled || !hasValidKilometraje) {
            itinerario.value = '';
            itinerario.disabled = true;
            return;
        }

        itinerario.disabled = false;
    }

    function setFieldsByFct(isFct) {
        for (var i = 0; i < managedIds.length; i += 1) {
            setDisabled(managedIds[i], isFct);
        }

        updateItinerarioState();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var fct = byId('fct_id');
        var kilometraje = byId('kilometraje_id');

        setFieldsByFct(fct ? fct.checked : true);

        if (fct) {
            fct.addEventListener('change', function () {
                setFieldsByFct(fct.checked);
            });
        }

        if (kilometraje) {
            kilometraje.addEventListener('input', updateItinerarioState);
            kilometraje.addEventListener('change', updateItinerarioState);
        }
    });
})();
