'use strict';

(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    function setFieldVisibility(show) {
        var field = byId('field_adquiridosNO_id');
        if (!field) {
            return;
        }

        field.style.display = show ? '' : 'none';
    }

    function isEvaluacionTres() {
        var evaluacion = byId('evaluacion_id');
        return !!(evaluacion && String(evaluacion.value) === '3');
    }

    function updateAdquiridosField() {
        setFieldVisibility(isEvaluacionTres());
    }

    document.addEventListener('DOMContentLoaded', function () {
        var evaluacion = byId('evaluacion_id');
        if (evaluacion) {
            evaluacion.addEventListener('change', updateAdquiridosField);
        }
        updateAdquiridosField();
    });

    window.postModal = function () {
        updateAdquiridosField();
    };
})();
