'use strict';

(function () {
    function byId(id) {
        return document.getElementById(id);
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

    function isSubmittedFalta() {
        var method = byId('metodo');
        var estado = byId('estado_id');

        return method && method.value.toUpperCase() === 'PUT' && estado && Number(estado.value) >= 1;
    }

    function fieldContainer(element) {
        if (!element) {
            return null;
        }

        if ((element.getAttribute('type') || '').toLowerCase() === 'hidden') {
            return null;
        }

        return element.closest('.form-group') || element.closest('.item') || element.parentElement;
    }

    function setFieldEditable(id, editable) {
        var element = byId(id);
        var container = fieldContainer(element);

        if (container) {
            container.style.display = '';
        }

        if (element && id !== 'fichero_id') {
            element.disabled = !editable;
        }
    }

    function updateSubmittedFormState() {
        var onlyJustificant = isSubmittedFalta();
        var editableFields = [
            'idProfesor_id',
            'estado_id',
            'desde_id',
            'hasta_id',
            'baja_id',
            'dia_completo_id',
            'hora_ini_id',
            'hora_fin_id',
            'motivos_id',
            'observaciones_id'
        ];

        editableFields.forEach(function (id) {
            setFieldEditable(id, !onlyJustificant);
        });

        setFieldEditable('fichero_id', true);
    }

    function updateFaltaState() {
        if (isSubmittedFalta()) {
            updateSubmittedFormState();
            return;
        }

        updateSubmittedFormState();

        var baja = isChecked('baja_id');
        var diaCompleto = isChecked('dia_completo_id');

        if (baja) {
            setDisabled('hora_ini_id', true);
            setDisabled('hora_fin_id', true);
            setDisabled('hasta_id', true);
            setDisabled('dia_completo_id', true);
            return;
        }

        setDisabled('hasta_id', false);
        setDisabled('dia_completo_id', false);
        setDisabled('hora_ini_id', diaCompleto);
        setDisabled('hora_fin_id', diaCompleto);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var diaCompleto = byId('dia_completo_id');
        var baja = byId('baja_id');

        updateFaltaState();

        if (diaCompleto) {
            diaCompleto.addEventListener('change', updateFaltaState);
        }

        if (baja) {
            baja.addEventListener('change', updateFaltaState);
        }
    });

    window.postModal = function () {
        updateFaltaState();
    };
})();
