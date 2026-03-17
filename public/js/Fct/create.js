(function () {
    'use strict';

    function getApiAuth() {
        return window.intranetApiAuth || {};
    }

    function apiGet(url) {
        var apiAuth = getApiAuth();
        if (typeof apiAuth.apiGet === 'function') {
            return apiAuth.apiGet(url);
        }

        return Promise.reject(new Error('intranetApiAuth.apiGet no disponible'));
    }

    document.addEventListener('DOMContentLoaded', function () {
        var colaboracionSelect = document.getElementById('idColaboracion_id');
        var instructorSelect = document.getElementById('idInstructor_id');

        if (!colaboracionSelect || !instructorSelect) {
            return;
        }

        colaboracionSelect.addEventListener('change', function () {
            var idColaboracion = colaboracionSelect.value;

            apiGet('/api/colaboracion/instructores/' + idColaboracion)
                .then(function (result) {
                    var newOptions = result.data || [];
                    instructorSelect.innerHTML = '';

                    newOptions.forEach(function (value) {
                        var option = document.createElement('option');
                        option.value = value.dni;
                        option.textContent = value.name + ' ' + value.surnames;
                        instructorSelect.appendChild(option);
                    });
                })
                .catch(function () {
                    console.log('La solicitud no se ha podido completar.');
                });
        });
    });
})();
