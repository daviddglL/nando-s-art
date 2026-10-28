import { describe, expect, it } from 'vitest';
import { anteriorObra, errorCarga, obrasCargadas, seleccionarEstilo, siguienteObra } from '../../resources/js/galeria/intents.js';
import { reducer } from '../../resources/js/galeria/reducer.js';
import { estadoInicial, tarjetasVisibles } from '../../resources/js/galeria/state.js';

const obras = (n) => Array.from({ length: n }, (_, i) => ({ id: i + 1, name: `Obra ${i + 1}` }));
const conObras = (n, estilo = 'acuarela') =>
    reducer(reducer(estadoInicial, seleccionarEstilo(estilo)), obrasCargadas(estilo, obras(n)));

describe('reducer de la galería', () => {
    it('al elegir un estilo vacía las obras y marca que está cargando', () => {
        const estado = reducer({ ...conObras(3), error: 'x' }, seleccionarEstilo('oleo'));

        expect(estado).toMatchObject({ estilo: 'oleo', obras: [], indice: 0, cargando: true, error: null });
    });

    it('al recibir las obras empieza por la del centro', () => {
        const estado = conObras(5);

        expect(estado.cargando).toBe(false);
        expect(estado.obras).toHaveLength(5);
        expect(estado.indice).toBe(2);
    });

    it('descarta la respuesta de un estilo que ya no está seleccionado', () => {
        let estado = reducer(estadoInicial, seleccionarEstilo('acuarela'));
        estado = reducer(estado, seleccionarEstilo('oleo'));
        const despues = reducer(estado, obrasCargadas('acuarela', obras(3)));

        expect(despues).toBe(estado);
        expect(despues.cargando).toBe(true);
    });

    it('guarda el error de carga del estilo actual', () => {
        const estado = reducer(reducer(estadoInicial, seleccionarEstilo('oleo')), errorCarga('oleo', 'Falló'));

        expect(estado).toMatchObject({ cargando: false, error: 'Falló', obras: [] });
    });

    it('siguiente y anterior dan la vuelta al llegar a los extremos', () => {
        let estado = conObras(3); // índice 1
        estado = reducer(estado, siguienteObra());
        expect(estado.indice).toBe(2);
        estado = reducer(estado, siguienteObra());
        expect(estado.indice).toBe(0);
        estado = reducer(estado, anteriorObra());
        expect(estado.indice).toBe(2);
    });

    it('sin obras, las flechas no cambian nada', () => {
        const estado = conObras(0);

        expect(reducer(estado, siguienteObra())).toBe(estado);
        expect(reducer(estado, anteriorObra())).toBe(estado);
    });

    it('ignora intents desconocidos y nunca modifica el estado anterior', () => {
        const estado = Object.freeze(conObras(2));

        expect(reducer(estado, { type: 'otra-cosa' })).toBe(estado);
        expect(() => reducer(estado, siguienteObra())).not.toThrow();
    });
});

describe('tarjetasVisibles', () => {
    it('muestra como mucho 5 tarjetas alrededor de la central', () => {
        const tarjetas = tarjetasVisibles(conObras(8)); // índice 4

        expect(tarjetas.map((t) => t.posicion)).toEqual([-2, -1, 0, 1, 2]);
        expect(tarjetas.map((t) => t.obra.id)).toEqual([3, 4, 5, 6, 7]);
    });

    it('da la vuelta al principio y al final de la lista', () => {
        const estado = { ...conObras(6), indice: 0 };

        expect(tarjetasVisibles(estado).map((t) => t.obra.id)).toEqual([5, 6, 1, 2, 3]);
    });

    it('no repite obras cuando hay menos de 5', () => {
        const ids = tarjetasVisibles(conObras(4)).map((t) => t.obra.id);

        expect(ids).toHaveLength(4);
        expect(new Set(ids).size).toBe(4);
    });

    it('sin obras no hay tarjetas', () => {
        expect(tarjetasVisibles(estadoInicial)).toEqual([]);
    });
});
