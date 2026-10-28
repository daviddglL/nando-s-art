import { describe, expect, it, vi } from 'vitest';
import { cargarObras, crearEfectos } from '../../resources/js/galeria/effects.js';
import { ERROR_CARGA, OBRAS_CARGADAS, seleccionarEstilo, siguienteObra } from '../../resources/js/galeria/intents.js';
import { reducer } from '../../resources/js/galeria/reducer.js';
import { estadoInicial } from '../../resources/js/galeria/state.js';
import { crearStore } from '../../resources/js/galeria/store.js';

const respuesta = (body, ok = true, status = 200) => Promise.resolve({ ok, status, json: () => Promise.resolve(body) });

describe('store', () => {
    it('aplica el reducer y avisa a los suscriptores solo si el estado cambia', () => {
        const store = crearStore(reducer, estadoInicial);
        const suscriptor = vi.fn();
        store.subscribe(suscriptor);

        store.dispatch(siguienteObra()); // sin obras: no cambia nada
        expect(suscriptor).not.toHaveBeenCalled();

        store.dispatch(seleccionarEstilo('oleo'));
        expect(suscriptor).toHaveBeenCalledOnce();
        expect(store.getState().estilo).toBe('oleo');
    });

    it('deja de avisar al darse de baja', () => {
        const store = crearStore(reducer, estadoInicial);
        const suscriptor = vi.fn();
        const baja = store.subscribe(suscriptor);
        baja();

        store.dispatch(seleccionarEstilo('oleo'));
        expect(suscriptor).not.toHaveBeenCalled();
    });

    it('al elegir un estilo carga las obras de la API y las guarda en el estado', async () => {
        const fetchFn = vi.fn(() => respuesta({ data: [{ id: 1 }, { id: 2 }, { id: 3 }] }));
        const store = crearStore(reducer, estadoInicial, crearEfectos(fetchFn));

        await store.dispatch(seleccionarEstilo('acuarela'));

        expect(fetchFn).toHaveBeenCalledWith('/api/products?style=acuarela', expect.any(Object));
        expect(store.getState()).toMatchObject({ estilo: 'acuarela', cargando: false, indice: 1 });
        expect(store.getState().obras).toHaveLength(3);
    });
});

describe('cargarObras', () => {
    it('codifica el estilo en la URL', async () => {
        const fetchFn = vi.fn(() => respuesta({ data: [] }));

        await cargarObras('a&b', fetchFn);

        expect(fetchFn.mock.calls[0][0]).toBe('/api/products?style=a%26b');
    });

    it('devuelve OBRAS_CARGADAS con el array data de la API', async () => {
        const intent = await cargarObras('oleo', () => respuesta({ data: [{ id: 7 }] }));

        expect(intent).toEqual({ type: OBRAS_CARGADAS, estilo: 'oleo', obras: [{ id: 7 }] });
    });

    it('convierte un error HTTP o de red en ERROR_CARGA', async () => {
        vi.spyOn(console, 'error').mockImplementation(() => {});

        expect((await cargarObras('oleo', () => respuesta({}, false, 500))).type).toBe(ERROR_CARGA);
        expect((await cargarObras('oleo', () => Promise.reject(new Error('sin red')))).type).toBe(ERROR_CARGA);
    });
});
