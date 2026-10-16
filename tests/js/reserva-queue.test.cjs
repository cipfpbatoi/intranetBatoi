const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');

// Executa el client real amb xarxa i rellotge simulats.
function client(statuses) {
    let time = 0;
    const calls = [];
    const buttons = { reservar: {}, liberar: {} };
    const context = vm.createContext({
        URLSearchParams,
        document: {
            addEventListener() {},
            querySelector() { return null; },
            getElementById(id) { return buttons[id] || null; }
        },
        setTimeout(resolve, ms) { time += ms; resolve(); },
        fetch: async (url, options) => {
            calls.push({ time, body: options.body });
            assert.equal(buttons.reservar.disabled, true);
            assert.equal(buttons.liberar.disabled, true);
            const status = statuses.shift() || 200;
            if (status === 'network') throw new Error('Connexió interrompuda');
            return {
                ok: status === 200, status,
                headers: { get: () => '2' },
                text: async () => JSON.stringify({ success: true })
            };
        }
    });
    vm.runInContext(fs.readFileSync(path.join(__dirname, '../../public/js/Reserva/edit.js'), 'utf8'), context);
    return { context, calls, buttons };
}

test('regula el ritme i reprén només la reserva rebutjada amb 429', async () => {
    const { context, calls, buttons } = client([200, 429, 200, 200]);
    const pending = vm.runInContext('processaReserves([1, 2, 3], id => apiRequest("POST", "api/reserva", { id }))', context);
    assert.equal(vm.runInContext('modDatos("reserva")', context), false);
    const results = await pending;
    assert.equal(results.length, 3);
    assert.deepEqual(calls.map(call => call.body), ['id=1', 'id=2', 'id=2', 'id=3']);
    for (let i = 1; i < calls.length; i++) assert.ok(calls[i].time - calls[i - 1].time >= 200);
    assert.ok(calls[2].time - calls[1].time >= 2000);
    assert.equal(buttons.reservar.disabled, false);
    assert.equal(buttons.liberar.disabled, false);
});

test('una fallada de xarxa atura la cua sense repetir una escriptura incerta', async () => {
    const { context, calls, buttons } = client([200, 'network']);
    const results = await vm.runInContext('processaReserves([1, 2, 3], id => apiRequest("POST", "api/reserva", { id }))', context);
    assert.equal(calls.length, 2);
    assert.equal(results[0].status, 'fulfilled');
    assert.equal(results[1].status, 'rejected');
    assert.equal(buttons.reservar.disabled, false);
});
