// Lightbox de la ficha de una obra: un <dialog> nativo a pantalla completa.
// Escape lo cierra el propio navegador; aquí se añaden el botón de cerrar y el clic fuera de la imagen.
export function iniciarLightbox(raiz = document) {
    raiz.querySelectorAll('[data-lightbox-abrir]').forEach((disparador) => {
        const dialogo = raiz.querySelector(`#${disparador.dataset.lightboxAbrir}`);
        if (!dialogo) return;

        disparador.addEventListener('click', () => dialogo.showModal());

        dialogo.addEventListener('click', (evento) => {
            // El clic en el fondo llega con el propio <dialog> como destino
            if (evento.target === dialogo || evento.target.closest('[data-lightbox-cerrar]')) {
                dialogo.close();
            }
        });
    });
}
