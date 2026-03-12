'use strict';

(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var formFct = document.getElementById('formFct');
        if (!formFct) {
            return;
        }

        document.addEventListener('click', function (event) {
            var trigger = event.target.closest('.fa-plus');
            if (!trigger) {
                return;
            }

            var fct = trigger.id;
            if (!fct) {
                return;
            }

            formFct.setAttribute('action', '/fct/' + fct + '/alumnoCreate');
        });
    });
})();
