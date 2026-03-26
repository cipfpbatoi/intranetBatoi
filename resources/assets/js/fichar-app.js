import { createApp, h } from 'vue';

import ControlResumenRangoView from './components/fichar/ControlResumenRangoView.vue';
import ControlSemanaView from './components/fichar/ControlSemanaView.vue';

function parseJsonDataset(value, fallback) {
    if (typeof value !== 'string' || value.trim() === '') {
        return fallback;
    }

    try {
        return JSON.parse(value);
    } catch (error) {
        return fallback;
    }
}

const mountNode = document.getElementById('app');

if (mountNode) {
    const page = mountNode.dataset.page || '';
    const profes = parseJsonDataset(mountNode.dataset.profes, []);

    const pages = {
        controlSemana: {
            component: ControlSemanaView,
            props: { profes },
        },
        resumenRango: {
            component: ControlResumenRangoView,
            props: { profes },
        },
    };

    const resolved = pages[page];

    if (resolved) {
        createApp({
            render() {
                return h(resolved.component, resolved.props);
            },
        }).mount(mountNode);
    }
}
