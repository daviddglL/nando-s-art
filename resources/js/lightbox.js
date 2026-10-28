import { escucharSwipe } from './galeria/swipe.js';

// Lightbox de la ficha de una obra: un <dialog> nativo a pantalla completa.
// Escape lo cierra el propio navegador; aquí se añaden el botón de cerrar y el clic fuera de la imagen.
// Si la obra tiene varias imágenes ([data-lightbox-imagen]), se pasa de una a otra con las flechas
// (botones o teclado) o deslizando el dedo, y cada disparador abre la suya (data-lightbox-indice).
export function iniciarLightbox(raiz = document) {
    const preparados = new Set();

    raiz.querySelectorAll('[data-lightbox-abrir]').forEach((disparador) => {
        const dialogo = raiz.querySelector(`#${disparador.dataset.lightboxAbrir}`);
        if (!dialogo) return;

        disparador.addEventListener('click', () => {
            mostrarImagen(dialogo, Number(disparador.dataset.lightboxIndice ?? 0));
            dialogo.showModal();
        });

        if (preparados.has(dialogo)) return;
        preparados.add(dialogo);

        const pasar = (paso) => mostrarImagen(dialogo, Number(dialogo.dataset.indice ?? 0) + paso);

        dialogo.addEventListener('click', (evento) => {
            if (evento.target.closest('[data-lightbox-anterior]')) return pasar(-1);
            if (evento.target.closest('[data-lightbox-siguiente]')) return pasar(1);
            // El clic en el fondo llega con el propio <dialog> como destino
            if (evento.target === dialogo || evento.target.closest('[data-lightbox-cerrar]')) {
                dialogo.close();
            }
        });

        dialogo.addEventListener('keydown', (evento) => {
            if (evento.key === 'ArrowLeft') pasar(-1);
            if (evento.key === 'ArrowRight') pasar(1);
        });

        escucharSwipe(dialogo, (direccion) => pasar(direccion === 'siguiente' ? 1 : -1));
    });
}

// Enseña solo la imagen `indice` (dando la vuelta al llegar al final) y actualiza el contador "2 / 5"
export function mostrarImagen(dialogo, indice) {
    const imagenes = [...dialogo.querySelectorAll('[data-lightbox-imagen]')];
    if (imagenes.length === 0) return;

    const actual = ((indice % imagenes.length) + imagenes.length) % imagenes.length;
    imagenes.forEach((imagen, i) => {
        imagen.hidden = i !== actual;
    });
    dialogo.dataset.indice = String(actual);

    const contador = dialogo.querySelector('[data-lightbox-contador]');
    if (contador) contador.textContent = `${actual + 1} / ${imagenes.length}`;
}
