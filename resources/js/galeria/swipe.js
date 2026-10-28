// Gesto de deslizar en pantallas táctiles: no toca el estado, solo traduce el gesto a un intent.

// Distancia mínima (px) para que un deslizamiento cuente y no se confunda con un toque
export const UMBRAL_SWIPE = 50;

// Dirección del gesto: 'siguiente' al deslizar hacia la izquierda, 'anterior' hacia la derecha
// y null si es corto o más vertical que horizontal (el usuario está haciendo scroll).
export function direccionSwipe(inicio, fin, umbral = UMBRAL_SWIPE) {
    const dx = fin.x - inicio.x;
    const dy = fin.y - inicio.y;

    if (Math.abs(dx) < umbral || Math.abs(dx) <= Math.abs(dy)) return null;

    return dx < 0 ? 'siguiente' : 'anterior';
}

export function escucharSwipe(elemento, alDeslizar) {
    let inicio = null;

    elemento.addEventListener('touchstart', (evento) => {
        const toque = evento.changedTouches[0];
        inicio = { x: toque.clientX, y: toque.clientY };
    }, { passive: true });

    elemento.addEventListener('touchend', (evento) => {
        if (!inicio) return;
        const toque = evento.changedTouches[0];
        const direccion = direccionSwipe(inicio, { x: toque.clientX, y: toque.clientY });
        inicio = null;
        if (direccion) alDeslizar(direccion);
    });
}
