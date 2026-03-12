'use strict';

(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    function updateClass(element, className) {
        if (element) {
            element.className = className;
        }
    }

    function setValue(id, value) {
        var element = byId(id);
        if (element) {
            element.value = value;
        }
    }

    function setDisabled(id, disabled) {
        var element = byId(id);
        if (element) {
            element.disabled = disabled;
        }
    }

    function isChecked(id) {
        var element = byId(id);
        return !!(element && element.checked);
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

    function updateFctFields() {
        if (isChecked('fct_id')) {
            setValue('servicio_id', 'Visita empreses FCT:');
            setValue('alojamiento_id', 0);
            setValue('comida_id', 0);
            updateClass(byId('field_servicio_id'), 'form-group item hidden');
            updateClass(byId('field_alojamiento_id'), 'form-group item hidden');
            updateClass(byId('field_comida_id'), 'form-group item hidden');
        } else {
            setValue('servicio_id', 'Visita empreses');
            updateClass(byId('field_servicio_id'), 'form-group item');
            updateClass(byId('field_alojamiento_id'), 'form-group item');
            updateClass(byId('field_comida_id'), 'form-group item');
        }

        updateItinerarioState();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var kilometraje = byId('kilometraje_id');
        var itinerario = byId('itinerario_id');
        var fct = byId('fct_id');
        var createModal = byId('create');

        updateFctFields();
        updateItinerarioState();

        if (kilometraje) {
            kilometraje.addEventListener('input', updateItinerarioState);
            kilometraje.addEventListener('change', updateItinerarioState);
        }

        if (fct) {
            fct.addEventListener('change', updateFctFields);
        }

        if (createModal) {
            createModal.addEventListener('shown.bs.modal', function () {
                updateFctFields();
                updateItinerarioState();
            });
        }

        var alertShown = false;
        if (itinerario) {
            itinerario.placeholder = "L'itinerari comença i acaba al centre";
        }

        if (kilometraje) {
            kilometraje.addEventListener('focus', function () {
                if (!alertShown) {
                    alert("Recorda que el quilometratge s'ha de comptar des del centre.");
                    alertShown = true;
                }
            });
        }
    });
})();
