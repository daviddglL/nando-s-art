import { crearEfectos } from './effects.js';
import { anteriorObra, seleccionarEstilo, siguienteObra } from './intents.js';
import { reducer } from './reducer.js';
import { estadoInicial } from './state.js';
import { crearStore } from './store.js';
import { render } from './view.js';

// Punto de entrada: conecta los botones de la vista con dispatch() y pinta cada estado nuevo
document.addEventListener('DOMContentLoaded', () => {
    const main = document.getElementById('galeria-main');
    if (!main) return;

    // Colores y estilo inicial: salen del enum App\Enums\Style (ver galeria.blade.php)
    const config = JSON.parse(document.getElementById('galeria-config').textContent);

    const dom = {
        main,
        botones: [...document.querySelectorAll('.filtro-estilo')],
        carrusel: document.getElementById('contenedor-muestras'),
        anterior: document.getElementById('prev'),
        siguiente: document.getElementById('next'),
    };

    const store = crearStore(reducer, estadoInicial, crearEfectos());
    store.subscribe((estado) => render(estado, dom, config));

    dom.botones.forEach((boton) => {
        boton.addEventListener('click', () => store.dispatch(seleccionarEstilo(boton.dataset.style)));
    });
    dom.anterior.addEventListener('click', () => store.dispatch(anteriorObra()));
    dom.siguiente.addEventListener('click', () => store.dispatch(siguienteObra()));

    // Flechas del teclado, salvo cuando se está escribiendo en un campo
    document.addEventListener('keydown', (evento) => {
        if (evento.target.closest?.('input, textarea, select')) return;
        if (evento.key === 'ArrowLeft') store.dispatch(anteriorObra());
        if (evento.key === 'ArrowRight') store.dispatch(siguienteObra());
    });

    store.dispatch(seleccionarEstilo(config.estiloInicial));
});
