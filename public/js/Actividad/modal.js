'use strict';

document.addEventListener('DOMContentLoaded', function () {
    var fueraCentro = document.getElementById('fueraCentro_id');
    var transport = document.getElementById('transport_id');
    if (!fueraCentro || !transport) {
        return;
    }

    fueraCentro.addEventListener('change', function () {
        if (!fueraCentro.checked) {
            transport.checked = false;
            transport.disabled = true;
        } else {
            transport.disabled = false;
        }
    });
});
