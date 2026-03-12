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

    function updateFaltaState() {
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

        setDisabled('hora_ini_id', true);
        setDisabled('hora_fin_id', true);
        updateFaltaState();

        if (diaCompleto) {
            diaCompleto.addEventListener('change', updateFaltaState);
        }

        if (baja) {
            baja.addEventListener('change', updateFaltaState);
        }
    });
})();
