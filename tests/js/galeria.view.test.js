// @vitest-environment jsdom
import { beforeEach, describe, expect, it } from 'vitest';
import { estadoInicial } from '../../resources/js/galeria/state.js';
import { render } from '../../resources/js/galeria/view.js';

const config = {
    fondos: { acuarela: 'bg-blue-100', oleo: 'bg-pink-100' },
    coloresBoton: { acuarela: 'bg-blue-100 text-blue-900', oleo: 'bg-pink-100 text-pink-900' },
};

const obra = (id, extra = {}) => ({
    id,
    name: `Obra ${id}`,
    description: 'Descripción',
    height: 30,
    width: 40,
    category_label: 'Paisaje',
    imagen_url: `/storage/obras/${id}.webp`,
    ...extra,
});

let dom;

beforeEach(() => {
    document.body.innerHTML = `
        <main id="galeria-main"></main>
        <button class="filtro-estilo" data-style="acuarela"></button>
        <button class="filtro-estilo" data-style="oleo"></button>
        <div id="contenedor-muestras"></div>
        <button id="prev"></button><button id="next"></button>`;
    dom = {
        main: document.getElementById('galeria-main'),
        botones: [...document.querySelectorAll('.filtro-estilo')],
        carrusel: document.getElementById('contenedor-muestras'),
        anterior: document.getElementById('prev'),
        siguiente: document.getElementById('next'),
    };
});

describe('vista de la galería', () => {
    it('marca el estilo seleccionado y pinta su fondo', () => {
        render({ ...estadoInicial, estilo: 'oleo', cargando: true }, dom, config);

        expect(dom.main.className).toContain('bg-pink-100');
        expect(dom.botones[1].getAttribute('aria-pressed')).toBe('true');
        expect(dom.botones[0].getAttribute('aria-pressed')).toBe('false');
        expect(dom.carrusel.textContent).toContain('Cargando obras');
    });

    it('muestra un aviso si el estilo no tiene obras o hay un error', () => {
        render({ ...estadoInicial, estilo: 'oleo' }, dom, config);
        expect(dom.carrusel.textContent).toContain('Todavía no hay obras de este estilo');
        expect(dom.siguiente.disabled).toBe(true);

        render({ ...estadoInicial, estilo: 'oleo', error: 'No se han podido cargar' }, dom, config);
        expect(dom.carrusel.textContent).toContain('No se han podido cargar');
    });

    it('pinta las tarjetas con la central marcada', () => {
        const obras = [1, 2, 3].map((id) => obra(id));
        render({ ...estadoInicial, estilo: 'acuarela', obras, indice: 1 }, dom, config);

        const tarjetas = dom.carrusel.querySelectorAll('article');
        expect(tarjetas).toHaveLength(3);
        const central = dom.carrusel.querySelector('[data-posicion="0"]');
        expect(central.querySelector('h3').textContent).toBe('Obra 2');
        expect(central.querySelector('img').getAttribute('src')).toBe('/storage/obras/2.webp');
        expect(central.textContent).toContain('Categoría: Paisaje');
        expect(dom.siguiente.disabled).toBe(false);
    });

    it('añade un hueco invisible cuando hay un número par de tarjetas', () => {
        const obras = [1, 2].map((id) => obra(id));
        render({ ...estadoInicial, estilo: 'acuarela', obras, indice: 1 }, dom, config);

        expect(dom.carrusel.children).toHaveLength(3);
        expect(dom.carrusel.lastElementChild.className).toContain('invisible');
    });

    it('nunca interpreta como HTML el texto de una obra', () => {
        const maliciosa = obra(1, { name: '<img src=x onerror=alert(1)>' });
        render({ ...estadoInicial, estilo: 'acuarela', obras: [maliciosa], indice: 0 }, dom, config);

        expect(dom.carrusel.querySelector('h3').textContent).toBe('<img src=x onerror=alert(1)>');
        expect(dom.carrusel.querySelectorAll('img')).toHaveLength(1);
    });

    it('enlaza cada obra con su ficha, enfocable solo en la tarjeta central', () => {
        const obras = [1, 2, 3].map((id) => obra(id, { url: `/obras/obra-${id}` }));
        render({ ...estadoInicial, estilo: 'acuarela', obras, indice: 1 }, dom, config);

        const enlaces = [...dom.carrusel.querySelectorAll('a')];
        expect(enlaces.map((a) => a.getAttribute('href'))).toEqual(['/obras/obra-1', '/obras/obra-2', '/obras/obra-3']);
        expect(enlaces.map((a) => a.tabIndex)).toEqual([-1, 0, -1]);
    });
});
