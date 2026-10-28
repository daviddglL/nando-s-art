// Pide confirmación antes de enviar los formularios con data-confirmar="¿Seguro…?".
// Sustituye a los onsubmit="return confirm(…)" en línea, que la Content-Security-Policy bloquea.
export function iniciarConfirmaciones(raiz = document) {
    raiz.addEventListener('submit', (evento) => {
        const mensaje = evento.target.dataset?.confirmar;
        if (mensaje && !window.confirm(mensaje)) evento.preventDefault();
    });
}
