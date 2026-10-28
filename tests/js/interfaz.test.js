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
