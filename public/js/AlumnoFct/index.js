'use strict';

(function () {
    function openModal(id) {
        var modalElement = document.getElementById(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
        }

        if (window.jQuery) {
            window.jQuery(modalElement).modal('show');
        }
    }

    function hideModal(id) {
        var modalElement = document.getElementById(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            return;
        }

        if (window.jQuery) {
            window.jQuery(modalElement).modal('hide');
        }
    }

    function updateActaButton(activeTab) {
        var buttons = document.querySelectorAll('.fct-acta-btn');
        if (!buttons.length) {
            return;
        }

        buttons.forEach(function (button) {
            button.style.display = 'none';
        });

        if (activeTab === '#tab_LOE') {
            document.querySelectorAll('#tab_LOE .fct-acta-btn').forEach(function (button) {
                button.style.display = '';
            });
        }
    }

    function getActiveTabHref() {
        var activeLink = document.querySelector('.nav-tabs li.active a[data-toggle="tab"], .nav-tabs li.active a[data-bs-toggle="tab"]');
        return activeLink ? activeLink.getAttribute('href') : null;
    }

    function activateTabByHref(href) {
        if (!href) {
            return false;
        }

        var selector = 'a[href="' + href + '"][data-toggle="tab"], a[href="' + href + '"][data-bs-toggle="tab"]';
        var link = document.querySelector(selector);
        if (!link) {
            return false;
        }

        if (window.bootstrap && window.bootstrap.Tab) {
            window.bootstrap.Tab.getOrCreateInstance(link).show();
            return true;
        }

        if (window.jQuery && window.jQuery(link).tab) {
            window.jQuery(link).tab('show');
            return true;
        }

        return false;
    }

    function initAvalTabs() {
        var path = window.location.pathname || '';
        if (path.indexOf('/avalFct') === -1) {
            return;
        }

        var storageKey = 'alumnofct_aval_tab';
        var storedTab = localStorage.getItem(storageKey);

        if (storedTab && activateTabByHref(storedTab)) {
            updateActaButton(storedTab);
        } else {
            updateActaButton(getActiveTabHref());
        }

        document.querySelectorAll('a[data-toggle="tab"], a[data-bs-toggle="tab"]').forEach(function (link) {
            link.addEventListener('shown.bs.tab', function (event) {
                var href = event.target ? event.target.getAttribute('href') : null;
                if (href) {
                    localStorage.setItem(storageKey, href);
                    updateActaButton(href);
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAvalTabs();

        document.querySelectorAll('.download').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                button.setAttribute('href', '');
                openModal('password');
            });
        });

        document.querySelectorAll('#seleccion .submit').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                var checkAll = document.getElementById('checkall');
                if (checkAll) {
                    checkAll.checked = false;
                }

                var formSeleccion = document.getElementById('formSeleccion');
                if (formSeleccion) {
                    formSeleccion.submit();
                }
            });
        });

        var mostraDiv = document.getElementById('mostraDiv');
        var divSignatura = document.getElementById('divSignatura');
        if (mostraDiv && divSignatura) {
            mostraDiv.addEventListener('change', function () {
                divSignatura.style.display = mostraDiv.checked ? '' : 'none';
            });
        }

        document.querySelectorAll('#password .submit').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                localStorage.setItem('cur_modal', '#password');
                hideModal('password');

                var formPassword = document.getElementById('formPassword');
                if (formPassword) {
                    formPassword.submit();
                }

                button.setAttribute('href', '');
                openModal('loading');
            });
        });
    });
})();
