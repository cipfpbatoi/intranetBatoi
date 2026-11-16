'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

/**
 * DOM mínim per executar els scripts legacy sense dependències de navegador.
 */
class FakeElement {
    constructor(tagName, options = {}) {
        this.tagName = tagName.toUpperCase();
        this.id = options.id || '';
        this.className = options.className || '';
        this.attributes = new Map(Object.entries(options.attributes || {}));
        this.children = [];
        this.parentElement = null;
        this.style = {};
        this.value = options.value || '';
        this._textContent = options.textContent || '';
        this._innerHTML = '';

        if (this.id) {
            this.attributes.set('id', this.id);
        }
    }

    get textContent() {
        return this._textContent;
    }

    set textContent(value) {
        this._textContent = String(value);
    }

    get innerHTML() {
        return this._innerHTML;
    }

    set innerHTML(value) {
        this._innerHTML = String(value);
        this._textContent = stripTags(this._innerHTML);

        if (this.id === 'modal-body' && this._innerHTML.includes('datamateriales')) {
            this.replaceChildren(...parseMaterialsTable(this._innerHTML));
        }
    }

    get firstElementChild() {
        return this.children[0] || null;
    }

    get name() {
        return this.getAttribute('name') || '';
    }

    set name(value) {
        this.setAttribute('name', value);
    }

    get previousElementSibling() {
        if (!this.parentElement) {
            return null;
        }

        const index = this.parentElement.children.indexOf(this);
        return index > 0 ? this.parentElement.children[index - 1] : null;
    }

    appendChild(child) {
        child.parentElement = this;
        this.children.push(child);
        return child;
    }

    replaceChildren(...children) {
        this.children = [];
        children.forEach((child) => this.appendChild(child));
    }

    insertAdjacentElement(position, child) {
        if (position !== 'afterend' || !this.parentElement) {
            throw new Error('Posició no suportada: ' + position);
        }

        const siblings = this.parentElement.children;
        const index = siblings.indexOf(this);
        child.parentElement = this.parentElement;
        siblings.splice(index + 1, 0, child);
        return child;
    }

    remove() {
        if (!this.parentElement) {
            return;
        }

        const siblings = this.parentElement.children;
        siblings.splice(siblings.indexOf(this), 1);
        this.parentElement = null;
    }

    setAttribute(name, value) {
        const normalised = String(value);
        this.attributes.set(name, normalised);
        if (name === 'id') {
            this.id = normalised;
        }
        if (name === 'class') {
            this.className = normalised;
        }
    }

    getAttribute(name) {
        if (name === 'id' && this.id) {
            return this.id;
        }
        if (name === 'class' && this.className) {
            return this.className;
        }
        return this.attributes.has(name) ? this.attributes.get(name) : null;
    }

    querySelector(selector) {
        return this.querySelectorAll(selector)[0] || null;
    }

    querySelectorAll(selector) {
        return descendantsOf(this).filter((element) => matchesSelector(element, selector));
    }

    closest(selector) {
        let current = this;
        while (current) {
            if (matchesSelector(current, selector)) {
                return current;
            }
            current = current.parentElement;
        }
        return null;
    }
}

class FakeDocument {
    constructor() {
        this.body = new FakeElement('body');
        this.listeners = new Map();
    }

    createElement(tagName) {
        return new FakeElement(tagName);
    }

    addEventListener(type, callback) {
        const listeners = this.listeners.get(type) || [];
        listeners.push(callback);
        this.listeners.set(type, listeners);
    }

    getElementById(id) {
        return [this.body, ...descendantsOf(this.body)].find((element) => element.id === id) || null;
    }

    querySelector(selector) {
        return this.querySelectorAll(selector)[0] || null;
    }

    querySelectorAll(selector) {
        return descendantsOf(this.body).filter((element) => matchesSelector(element, selector));
    }

    fire(type, target = null) {
        const event = {
            target,
            defaultPrevented: false,
            preventDefault() {
                this.defaultPrevented = true;
            },
        };

        (this.listeners.get(type) || []).forEach((listener) => listener(event));
        return event;
    }
}

function descendantsOf(element) {
    return element.children.flatMap((child) => [child, ...descendantsOf(child)]);
}

function stripTags(value) {
    return String(value).replace(/<[^>]*>/g, '');
}

function hasClass(element, className) {
    return element.className.split(/\s+/).includes(className);
}

function matchesSimple(element, selector) {
    const attributeMatch = selector.match(/\[([^=\]]+)(?:=([^\]]+))?\]/);
    const attributeName = attributeMatch ? attributeMatch[1] : null;
    const attributeValue = attributeMatch && attributeMatch[2] !== undefined
        ? attributeMatch[2].replace(/^["']|["']$/g, '')
        : null;
    const base = selector.replace(/\[[^\]]+\]/g, '');

    if (attributeName && element.getAttribute(attributeName) === null) {
        return false;
    }
    if (attributeValue !== null && element.getAttribute(attributeName) !== attributeValue) {
        return false;
    }

    if (base.startsWith('#')) {
        return element.id === base.slice(1);
    }

    const match = base.match(/^([a-zA-Z]+)?((?:\.[\w-]+)*)$/);
    if (!match) {
        return false;
    }

    const [, tagName, classes] = match;
    if (tagName && element.tagName !== tagName.toUpperCase()) {
        return false;
    }

    return classes
        .split('.')
        .filter(Boolean)
        .every((className) => hasClass(element, className));
}

function matchesSelector(element, selector) {
    const parts = selector.trim().split(/\s+/);
    if (!matchesSimple(element, parts.at(-1))) {
        return false;
    }

    let ancestor = element.parentElement;
    for (let index = parts.length - 2; index >= 0; index -= 1) {
        while (ancestor && !matchesSimple(ancestor, parts[index])) {
            ancestor = ancestor.parentElement;
        }
        if (!ancestor) {
            return false;
        }
        ancestor = ancestor.parentElement;
    }

    return true;
}

function element(tagName, options = {}) {
    return new FakeElement(tagName, options);
}

function parseMaterialsTable(html) {
    const rowId = html.match(/<tr id="([^"]+)">/)[1];
    const spans = [...html.matchAll(/<span class="([^"]+)"(?: name="([^"]+)")?>([\s\S]*?)<\/span>/g)];
    const table = element('table', {
        id: 'datamateriales',
        attributes: { name: 'material' },
    });
    const row = element('tr', { id: rowId });
    const idCell = element('td', { textContent: rowId });
    row.appendChild(idCell);

    spans.slice(0, 4).forEach((match) => {
        const cell = element('td');
        const source = element('span', {
            className: match[1],
            textContent: stripTags(match[3]),
            attributes: match[2] ? { name: match[2] } : {},
        });
        cell.appendChild(source);
        row.appendChild(cell);
    });

    const actionsCell = element('td');
    const actions = element('span', { className: 'botones' });
    actions.appendChild(element('a', { className: 'edit' }));
    actionsCell.appendChild(actions);
    row.appendChild(actionsCell);
    table.appendChild(row);

    return [table];
}

async function settle() {
    for (let index = 0; index < 8; index += 1) {
        await new Promise((resolve) => setImmediate(resolve));
    }
}

test('ull → llapis → espai → guardat envia un únic PUT sense reobrir el diàleg', async () => {
    const document = new FakeDocument();
    const dialog = element('div', { id: 'dialogo' });
    const title = element('div', { className: 'modal-title' });
    const body = element('div', { id: 'modal-body', className: 'modal-body' });
    const footer = element('div', { className: 'modal-footer' });
    footer.appendChild(element('button', { attributes: { type: 'submit' } }));
    footer.appendChild(element('button', { attributes: { type: 'button' } }));
    dialog.appendChild(title);
    dialog.appendChild(body);
    dialog.appendChild(footer);
    document.body.appendChild(dialog);

    const mainTable = element('table');
    const mainRow = element('tr');
    mainRow.appendChild(element('td', { textContent: '50' }));
    const operationCell = element('td');
    const viewButton = element('a', { className: 'imgButton' });
    operationCell.appendChild(viewButton);
    mainRow.appendChild(operationCell);
    mainTable.appendChild(mainRow);
    document.body.appendChild(mainTable);

    const calls = [];
    const context = {
        URLSearchParams,
        console,
        document,
        fetch: async (url, options) => {
            calls.push({ url, options });
            if (url === '/api/espacio') {
                return {
                    ok: true,
                    json: async () => ({ data: [{ aula: 'AULA1', descripcion: 'Aula 1' }] }),
                };
            }
            if (url === '/api/articuloLote/50/materiales') {
                return {
                    ok: true,
                    json: async () => ({
                        data: [{
                            id: 501,
                            nserieprov: 'S-501',
                            marca: 'Marca',
                            modelo: 'Model',
                            espacio: 'INVENT',
                            descripcion: 'Projector',
                        }],
                    }),
                };
            }
            if (url === '/api/material/501') {
                return {
                    ok: true,
                    json: async () => ({ success: true }),
                };
            }
            throw new Error('Petició inesperada: ' + url);
        },
        intranetUiHelpers: {
            showModal() {},
            hideModal() {},
        },
    };
    context.window = context;
    const sandbox = vm.createContext(context);
    const projectRoot = path.resolve(__dirname, '..', '..');

    [
        'public/js/common/api-auth.js',
        'public/js/ArticuloLote/index.js',
        'public/js/tabledit.js',
    ].forEach((relativePath) => {
        vm.runInContext(
            fs.readFileSync(path.join(projectRoot, relativePath), 'utf8'),
            sandbox,
            { filename: relativePath }
        );
    });

    document.fire('DOMContentLoaded');
    document.fire('click', viewButton);
    await settle();

    const row = document.getElementById('501');
    const editButton = row.querySelector('a.edit');
    document.fire('click', editButton);

    const selector = row.querySelector('select[name="espacio"]');
    assert.ok(selector);
    assert.deepEqual(selector.children.map((option) => option.value), ['AULA1']);
    selector.value = 'AULA1';

    document.fire('click', row.querySelector('.edit-ok'));
    await settle();

    assert.deepEqual(calls.map((call) => call.url), [
        '/api/espacio',
        '/api/articuloLote/50/materiales',
        '/api/material/501',
    ]);
    assert.equal(calls[2].options.method, 'PUT');
    assert.match(calls[2].options.body, /espacio=AULA1/);
    assert.equal(row.querySelector('[name="espacio"]').textContent, 'AULA1');
    assert.equal(row.querySelectorAll('span.editando').length, 0);
});
