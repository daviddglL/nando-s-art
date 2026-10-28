// @vitest-environment jsdom
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { iniciarConfirmaciones } from '../../resources/js/confirmar.js';
import { iniciarLightbox } from '../../resources/js/lightbox.js';
import { iniciarMenu } from '../../resources/js/menu.js';

describe('menú plegable', () => {
    beforeEach(() => {
        document.body.innerHTML = `
            <button data-menu-boton aria-controls="menu" aria-expanded="false" aria-label="Abrir menú"></button>
            <ul id="menu" class="hidden md:flex"></ul>`;
        iniciarMenu();
    });

    it('abre y cierra el menú y lo anuncia', () => {
        const boton = document.querySelector('[data-menu-boton]');
        const menu = document.getElementById('menu');

        boton.click();
        expect(menu.classList.contains('hidden')).toBe(false);
        expect(boton.getAttribute('aria-expanded')).toBe('true');
        expect(boton.getAttribute('aria-label')).toBe('Cerrar menú');

        boton.click();
        expect(menu.classList.contains('hidden')).toBe(true);
        expect(boton.getAttribute('aria-expanded')).toBe('false');
    });

    it('no falla en páginas sin menú', () => {
        document.body.innerHTML = '';
        expect(() => iniciarMenu()).not.toThrow();
    });
});

describe('lightbox', () => {
    let dialogo;

    beforeEach(() => {
        document.body.innerHTML = `
            <button data-lightbox-abrir="visor">Ampliar</button>
            <dialog id="visor"><button data-lightbox-cerrar>Cerrar</button><img alt="obra"></dialog>`;
        dialogo = document.getElementById('visor');
        // jsdom no implementa <dialog>: se simula lo justo
        dialogo.showModal = vi.fn(() => dialogo.setAttribute('open', ''));
        dialogo.close = vi.fn(() => dialogo.removeAttribute('open'));
        iniciarLightbox();
    });

    it('abre el visor al pulsar la imagen', () => {
        document.querySelector('[data-lightbox-abrir]').click();
        expect(dialogo.showModal).toHaveBeenCalledOnce();
    });

    it('se cierra con el botón o pulsando fuera de la imagen, pero no al pulsar la imagen', () => {
        dialogo.querySelector('img').click();
        expect(dialogo.close).not.toHaveBeenCalled();

        dialogo.querySelector('[data-lightbox-cerrar]').click();
        dialogo.click();
        expect(dialogo.close).toHaveBeenCalledTimes(2);
    });
});

describe('lightbox con varias imágenes', () => {
    let dialogo;
    const visibles = () => [...dialogo.querySelectorAll('[data-lightbox-imagen]')].filter((img) => !img.hidden).map((img) => img.alt);
    const contador = () => dialogo.querySelector('[data-lightbox-contador]').textContent;

    beforeEach(() => {
        document.body.innerHTML = `
            <button data-lightbox-abrir="visor" data-lightbox-indice="0">Principal</button>
            <button data-lightbox-abrir="visor" data-lightbox-indice="2">Tercera</button>
            <dialog id="visor">
                <img data-lightbox-imagen alt="1"><img data-lightbox-imagen alt="2" hidden><img data-lightbox-imagen alt="3" hidden>
                <button data-lightbox-anterior>‹</button><button data-lightbox-siguiente>›</button>
                <p data-lightbox-contador></p>
            </dialog>`;
        dialogo = document.getElementById('visor');
        dialogo.showModal = vi.fn();
        dialogo.close = vi.fn();
        iniciarLightbox();
    });

    it('cada miniatura abre el visor en su imagen', () => {
        document.querySelectorAll('[data-lightbox-abrir]')[1].click();
        expect(visibles()).toEqual(['3']);
        expect(contador()).toBe('3 / 3');
        expect(dialogo.showModal).toHaveBeenCalledOnce();
    });

    it('las flechas pasan de imagen dando la vuelta, sin cerrar el visor', () => {
        document.querySelector('[data-lightbox-abrir]').click();

        dialogo.querySelector('[data-lightbox-siguiente]').click();
        expect(visibles()).toEqual(['2']);
        dialogo.querySelector('[data-lightbox-anterior]').click();
        dialogo.querySelector('[data-lightbox-anterior]').click();
        expect(visibles()).toEqual(['3']);
        expect(dialogo.close).not.toHaveBeenCalled();
    });

    it('también con las flechas del teclado y deslizando el dedo', () => {
        document.querySelector('[data-lightbox-abrir]').click();

        dialogo.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowRight' }));
        expect(contador()).toBe('2 / 3');
        dialogo.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowLeft' }));
        expect(contador()).toBe('1 / 3');

        const toque = (tipo, x) => {
            const evento = new Event(tipo);
            evento.changedTouches = [{ clientX: x, clientY: 0 }];
            dialogo.dispatchEvent(evento);
        };
        toque('touchstart', 300);
        toque('touchend', 100);
        expect(visibles()).toEqual(['2']);
    });
});

describe('confirmación de formularios (sin JS en línea, por la CSP)', () => {
    beforeEach(() => {
        document.body.innerHTML = `
            <form data-confirmar="¿Seguro?"><button>Borrar</button></form>
            <form id="normal"><button>Guardar</button></form>`;
    });

    const enviar = (form) => {
        const evento = new Event('submit', { bubbles: true, cancelable: true });
        form.dispatchEvent(evento);
        return evento;
    };

    it('cancela el envío si el usuario no confirma y lo deja pasar si confirma', () => {
        iniciarConfirmaciones(document.body);
        const form = document.querySelector('[data-confirmar]');

        window.confirm = vi.fn(() => false);
        expect(enviar(form).defaultPrevented).toBe(true);
        expect(window.confirm).toHaveBeenCalledWith('¿Seguro?');

        window.confirm = vi.fn(() => true);
        expect(enviar(form).defaultPrevented).toBe(false);
    });

    it('no pregunta en los formularios sin data-confirmar', () => {
        iniciarConfirmaciones(document.body);
        window.confirm = vi.fn(() => false);

        expect(enviar(document.getElementById('normal')).defaultPrevented).toBe(false);
        expect(window.confirm).not.toHaveBeenCalled();
    });
});
