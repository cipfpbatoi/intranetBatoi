'use strict';

(function () {
    function showModal(id) {
        if (window.intranetUiHelpers && typeof window.intranetUiHelpers.showModal === 'function') {
            window.intranetUiHelpers.showModal(id);
            return;
        }

        var modalElement = document.getElementById(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }
    }

    function fillColaboracionShowModal(html, title) {
        var campos = document.getElementById('campos');
        var modal = document.getElementById('show');
        if (!campos || !modal) {
            return;
        }

        var dialog = modal.querySelector('.modal-dialog');
        if (dialog) {
            dialog.className = 'modal-dialog modal-xl';
        }

        var titleNode = modal.querySelector('.modal-title');
        if (titleNode) {
            titleNode.textContent = title || 'Detall col·laboració';
        }

        campos.innerHTML = html;
        showModal('show');
    }

    document.addEventListener('DOMContentLoaded', function () {
        var formFct = document.getElementById('formFct');

        document.addEventListener('click', function (event) {
            var showTrigger = event.target.closest('.js-colaboracion-show');
            if (showTrigger) {
                event.preventDefault();

                var url = showTrigger.getAttribute('href');
                if (!url) {
                    return;
                }

                var separator = url.indexOf('?') === -1 ? '?' : '&';
                fetch(url + separator + 'modal=1', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error("No s'ha pogut carregar el detall");
                        }
                        return response.text();
                    })
                    .then(function (html) {
                        fillColaboracionShowModal(
                            html,
                            showTrigger.getAttribute('data-show-title')
                        );
                    })
                    .catch(function (error) {
                        console.error(error);
                        window.location.href = url;
                    });

                return;
            }

            var trigger = event.target.closest('.fa-plus');
            if (!trigger || !formFct) {
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
