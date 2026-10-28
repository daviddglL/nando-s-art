// Menú plegable en móvil: el botón muestra u oculta la lista y lo anuncia con aria-expanded
export function iniciarMenu(raiz = document) {
    const boton = raiz.querySelector('[data-menu-boton]');
    const menu = boton && raiz.querySelector(`#${boton.getAttribute('aria-controls')}`);
    if (!menu) return;

    boton.addEventListener('click', () => {
        const abierto = boton.getAttribute('aria-expanded') === 'true';
        boton.setAttribute('aria-expanded', String(!abierto));
        boton.setAttribute('aria-label', abierto ? 'Abrir menú' : 'Cerrar menú');
        menu.classList.toggle('hidden', abierto);
    });
}
