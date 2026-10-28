// @vitest-environment jsdom
import { describe, expect, it, vi } from 'vitest';
import { direccionSwipe, escucharSwipe, UMBRAL_SWIPE } from '../../resources/js/galeria/swipe.js';

describe('swipe del carrusel', () => {
    it('deslizar a la izquierda pasa a la siguiente obra y a la derecha a la anterior', () => {
        expect(direccionSwipe({ x: 300, y: 100 }, { x: 100, y: 110 })).toBe('siguiente');
        expect(direccionSwipe({ x: 100, y: 100 }, { x: 300, y: 90 })).toBe('anterior');
    });

    it('ignora los toques cortos y el scroll vertical', () => {
        expect(direccionSwipe({ x: 100, y: 100 }, { x: 100 + UMBRAL_SWIPE - 1, y: 100 })).toBeNull();
        expect(direccionSwipe({ x: 100, y: 100 }, { x: 180, y: 300 })).toBeNull();
    });

    it('avisa con la dirección al terminar el gesto', () => {
        const carrusel = document.createElement('div');
        const alDeslizar = vi.fn();
        escucharSwipe(carrusel, alDeslizar);

        const tocar = (tipo, x) => {
            const evento = new Event(tipo);
            evento.changedTouches = [{ clientX: x, clientY: 200 }];
            carrusel.dispatchEvent(evento);
        };

        tocar('touchstart', 300);
        tocar('touchend', 120);
        tocar('touchstart', 120);
        tocar('touchend', 125);

        expect(alDeslizar).toHaveBeenCalledTimes(1);
        expect(alDeslizar).toHaveBeenCalledWith('siguiente');
    });
});
