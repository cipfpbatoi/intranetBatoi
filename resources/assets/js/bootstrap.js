import _ from 'lodash';
import $ from 'jquery';
import * as bootstrap from 'bootstrap';
import axios from 'axios';

window._ = _;
window.$ = window.jQuery = $;
window.bootstrap = bootstrap;

function normalizeLegacyBootstrapAttributes(root = document) {
    const legacyAttributeMap = {
        'data-toggle': 'data-bs-toggle',
        'data-target': 'data-bs-target',
        'data-dismiss': 'data-bs-dismiss',
        'data-placement': 'data-bs-placement',
        'data-container': 'data-bs-container',
        'data-original-title': 'data-bs-title',
    };

    Object.entries(legacyAttributeMap).forEach(([legacyName, modernName]) => {
        root.querySelectorAll('[' + legacyName + ']').forEach((element) => {
            if (!element.hasAttribute(modernName)) {
                element.setAttribute(modernName, element.getAttribute(legacyName) || '');
            }
        });
    });
}

function registerJQueryBootstrapBridge(pluginName, Constructor, defaultMethod = null) {
    if (!Constructor || !$.fn) {
        return;
    }

    $.fn[pluginName] = function (config, ...args) {
        return this.each(function () {
            const instance = Constructor.getOrCreateInstance(this, typeof config === 'object' ? config : undefined);

            if (typeof config === 'string') {
                if (typeof instance[config] === 'function') {
                    instance[config](...args);
                }
                return;
            }

            if (config === undefined && defaultMethod && typeof instance[defaultMethod] === 'function') {
                instance[defaultMethod]();
            }
        });
    };

    $.fn[pluginName].Constructor = Constructor;
}

function initTabBridge(root = document) {
    const selector = '[data-bs-toggle="tab"], [data-toggle="tab"]';
    const getTabSelector = (link) => link.getAttribute('data-bs-target') || link.getAttribute('data-target') || link.getAttribute('href');

    const activateTabLink = (link) => {
        const tabSelector = getTabSelector(link);
        if (!tabSelector || !tabSelector.startsWith('#')) {
            return;
        }

        const tabList = link.closest('.nav-tabs, [role="tablist"]');
        if (!tabList) {
            return;
        }

        tabList.querySelectorAll(selector).forEach((tabLink) => {
            tabLink.classList.remove('active');
            tabLink.setAttribute('aria-selected', 'false');
            const item = tabLink.closest('.nav-item, li');
            if (item) {
                item.classList.remove('active');
            }
        });

        const contentContainer = (() => {
            const controlsId = link.getAttribute('aria-controls');
            if (controlsId) {
                const directContainer = root.getElementById ? root.getElementById(controlsId) : document.getElementById(controlsId);
                if (directContainer && directContainer.classList.contains('tab-pane')) {
                    return directContainer.parentElement;
                }
            }

            const targetPane = root.querySelector(tabSelector);
            return targetPane ? targetPane.parentElement : null;
        })();

        if (contentContainer) {
            contentContainer.querySelectorAll('.tab-pane').forEach((pane) => {
                pane.classList.remove('active', 'show', 'in');
            });
        }

        link.classList.add('active');
        link.setAttribute('aria-selected', 'true');
        const item = link.closest('.nav-item, li');
        if (item) {
            item.classList.add('active');
        }

        const targetPane = root.querySelector(tabSelector);
        if (targetPane) {
            targetPane.classList.add('active', 'show', 'in');
        }

        if (bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(link).show();
        }
    };

    root.addEventListener('click', (event) => {
        const link = event.target.closest(selector);
        if (!link || !root.contains(link)) {
            return;
        }

        event.preventDefault();
        activateTabLink(link);
    });

    const activateInitialTab = () => {
        const hash = window.location.hash;
        const hashLink = hash ? root.querySelector('[data-bs-toggle="tab"][href="' + hash + '"], [data-toggle="tab"][href="' + hash + '"], [data-bs-toggle="tab"][data-bs-target="' + hash + '"], [data-toggle="tab"][data-target="' + hash + '"]') : null;
        const activeLink = hashLink || root.querySelector('.nav-tabs .nav-link.active[data-bs-toggle="tab"], .nav-tabs .nav-link.active[data-toggle="tab"], .nav-tabs li.active [data-bs-toggle="tab"], .nav-tabs li.active [data-toggle="tab"]');

        if (activeLink) {
            activateTabLink(activeLink);
        }
    };

    activateInitialTab();
    window.addEventListener('hashchange', activateInitialTab);
}

function initModalBridge(root = document) {
    const syncLegacyModalState = (modal, isVisible) => {
        if (!modal) {
            return;
        }

        modal.classList.toggle('in', isVisible);

        document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
            backdrop.classList.toggle('in', isVisible);
        });

        if (isVisible) {
            document.body.classList.add('modal-open');
            return;
        }

        const hasVisibleModals = document.querySelector('.modal.show, .modal.in');
        if (!hasVisibleModals) {
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
                if (!backdrop.classList.contains('show')) {
                    backdrop.remove();
                }
            });
        }
    };

    root.addEventListener('show.bs.modal', (event) => {
        syncLegacyModalState(event.target, true);
    });

    root.addEventListener('shown.bs.modal', (event) => {
        syncLegacyModalState(event.target, true);
    });

    root.addEventListener('hide.bs.modal', (event) => {
        event.target.classList.remove('in');
    });

    root.addEventListener('hidden.bs.modal', (event) => {
        syncLegacyModalState(event.target, false);
    });
}

normalizeLegacyBootstrapAttributes();
initTabBridge();
initModalBridge();

document.addEventListener('DOMContentLoaded', () => {
    normalizeLegacyBootstrapAttributes();
});

registerJQueryBootstrapBridge('alert', bootstrap.Alert, 'close');
registerJQueryBootstrapBridge('button', bootstrap.Button);
registerJQueryBootstrapBridge('collapse', bootstrap.Collapse);
registerJQueryBootstrapBridge('dropdown', bootstrap.Dropdown, 'toggle');
registerJQueryBootstrapBridge('modal', bootstrap.Modal, 'toggle');
registerJQueryBootstrapBridge('offcanvas', bootstrap.Offcanvas, 'toggle');
registerJQueryBootstrapBridge('popover', bootstrap.Popover);
registerJQueryBootstrapBridge('scrollspy', bootstrap.ScrollSpy);
registerJQueryBootstrapBridge('tab', bootstrap.Tab);
registerJQueryBootstrapBridge('toast', bootstrap.Toast, 'show');
registerJQueryBootstrapBridge('tooltip', bootstrap.Tooltip);

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
