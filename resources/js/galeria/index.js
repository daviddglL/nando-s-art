import { crearEfectos } from './effects.js';
import { anteriorObra, buscar, seleccionarCategoria, seleccionarEstilo, siguienteObra } from './intents.js';
import { reducer } from './reducer.js';
import { estadoInicial, parametrosUrl } from './state.js';
import { crearStore } from './store.js';
import { escucharSwipe } from './swipe.js';
import { render } from './view.js';

// Punto de entrada: conecta los botones de la vista con dispatch() y pinta cada estado nuevo
document.addEventListener('DOMContentLoaded', () => {
    const main = document.getElementById('galeria-main');
    if (!main) return;

    // Colores y filtros iniciales (los de la URL): salen de GaleriaController y del enum App\Enums\Style
    const config = JSON.parse(document.getElementById('galeria-config').textContent);

    const dom = {
        main,
        botones: [...document.querySelectorAll('.filtro-estilo')],
        carrusel: document.getElementById('contenedor-muestras'),
        anterior: document.getElementById('prev'),
        siguiente: document.getElementById('next'),
        categoria: document.getElementById('filtro-categoria'),
        busqueda: document.getElementById('filtro-busqueda'),
    };

    const inicial = { ...estadoInicial, categoria: config.categoriaInicial ?? '', busqueda: config.busquedaInicial ?? '' };
    const store = crearStore(reducer, inicial, crearEfectos());
    store.subscribe((estado) => render(estado, dom, config));

    // La URL guarda los filtros (/galeria?estilo=oleo&categoria=retrato) para compartirla o recargar sin perderlos
    store.subscribe((estado) => {
        const query = parametrosUrl(estado);
        const url = query ? `${location.pathname}?${query}` : location.pathname;
        if (url !== location.pathname + location.search) history.replaceState(null, '', url);
    });

    dom.botones.forEach((boton) => {
        boton.addEventListener('click', () => store.dispatch(seleccionarEstilo(boton.dataset.style)));
    });
    dom.categoria.addEventListener('change', () => store.dispatch(seleccionarCategoria(dom.categoria.value)));

    // La búsqueda espera a que se deje de escribir para no hacer una petición por letra
    let espera;
    dom.busqueda.addEventListener('input', () => {
        clearTimeout(espera);
        espera = setTimeout(() => store.dispatch(buscar(dom.busqueda.value)), 300);
    });
    dom.busqueda.form?.addEventListener('submit', (evento) => {
        evento.preventDefault();
        clearTimeout(espera);
        store.dispatch(buscar(dom.busqueda.value));
    });

    dom.anterior.addEventListener('click', () => store.dispatch(anteriorObra()));
    dom.siguiente.addEventListener('click', () => store.dispatch(siguienteObra()));

    // Flechas del teclado, salvo cuando se está escribiendo en un campo
    document.addEventListener('keydown', (evento) => {
        if (evento.target.closest?.('input, textarea, select')) return;
        if (evento.key === 'ArrowLeft') store.dispatch(anteriorObra());
        if (evento.key === 'ArrowRight') store.dispatch(siguienteObra());
    });

    // En móvil también se pasa de obra deslizando el dedo sobre el carrusel
    escucharSwipe(dom.carrusel, (direccion) => {
        store.dispatch(direccion === 'siguiente' ? siguienteObra() : anteriorObra());
    });

    store.dispatch(seleccionarEstilo(config.estiloInicial));
});
