import { tarjetasVisibles } from './state.js';

const CLASES_MAIN = 'flex flex-col items-center min-h-screen transition-colors duration-500 px-4';
const CLASES_BOTON = 'px-4 py-2 font-semibold rounded-sm filtro-estilo';
const BOTON_INACTIVO = 'bg-gray-200 text-gray-800 hover:bg-gray-300';

// Aspecto de cada tarjeta según lo lejos que esté del centro.
// En pantallas pequeñas se ocultan las de los lados para que el carrusel no se desborde.
const CLASES_POSICION = {
    0: 'scale-105 z-20 shadow-2xl',
    1: 'hidden sm:flex scale-95 blur-[1px] z-10',
    2: 'hidden lg:flex scale-90 blur-[2px] opacity-60 z-0',
};

const crearElemento = (etiqueta, clases, texto) => {
    const el = document.createElement(etiqueta);
    el.className = clases;
    // textContent y nunca innerHTML: el texto de las obras no se interpreta como HTML
    if (texto !== undefined) el.textContent = texto;
    return el;
};

export function crearTarjeta({ obra, posicion }) {
    const tarjeta = crearElemento(
        'article',
        `w-72 sm:w-80 h-[520px] bg-white p-6 rounded-2xl border border-gray-300 shrink-0 flex flex-col justify-between transition-all duration-500 ease-in-out ${CLASES_POSICION[Math.abs(posicion)]}`,
    );
    tarjeta.dataset.posicion = String(posicion);
    if (posicion !== 0) tarjeta.setAttribute('aria-hidden', 'true');

    const imagen = document.createElement('img');
    imagen.src = obra.imagen_url;
    imagen.alt = obra.name;
    imagen.loading = 'lazy';
    imagen.className = 'w-full h-48 object-cover rounded-sm mb-2';

    tarjeta.append(
        crearElemento('h3', 'text-xl font-bold text-gray-800 mb-2', obra.name),
        imagen,
        crearElemento('p', 'text-gray-600 line-clamp-6', obra.description),
        crearElemento('p', 'text-sm text-gray-500 mt-1', `Tamaño: ${obra.height} x ${obra.width} cm`),
        crearElemento('p', 'text-sm text-gray-500', `Categoría: ${obra.category_label}`),
    );

    return tarjeta;
}

// Pinta el estado completo. Solo lee el estado: los cambios llegan siempre como intents.
export function render(estado, dom, config) {
    dom.main.className = `${CLASES_MAIN} ${config.fondos[estado.estilo] ?? 'bg-gray-100'}`;

    dom.botones.forEach((boton) => {
        const activo = boton.dataset.style === estado.estilo;
        boton.className = `${CLASES_BOTON} ${activo ? config.coloresBoton[estado.estilo] : BOTON_INACTIVO}`;
        boton.setAttribute('aria-pressed', String(activo));
    });

    const sinObras = estado.obras.length < 2;
    dom.anterior.disabled = sinObras;
    dom.siguiente.disabled = sinObras;
    dom.carrusel.setAttribute('aria-busy', String(estado.cargando));

    if (estado.cargando) {
        dom.carrusel.replaceChildren(crearElemento('p', 'w-full text-center text-gray-600 py-12', 'Cargando obras…'));
        return;
    }

    if (estado.error) {
        dom.carrusel.replaceChildren(crearElemento('p', 'w-full text-center text-red-600 py-12', estado.error));
        return;
    }

    const tarjetas = tarjetasVisibles(estado);
    if (tarjetas.length === 0) {
        dom.carrusel.replaceChildren(
            crearElemento('p', 'w-full text-center text-gray-600 py-12', 'Todavía no hay obras de este estilo.'),
        );
        return;
    }

    const elementos = tarjetas.map(crearTarjeta);
    // Con un número par de tarjetas falta una a la derecha: un hueco invisible mantiene la central en el centro
    if (tarjetas.length % 2 === 0) {
        const hueco = crearElemento('div', `w-72 sm:w-80 shrink-0 ${CLASES_POSICION[2]} invisible`);
        hueco.setAttribute('aria-hidden', 'true');
        elementos.push(hueco);
    }
    dom.carrusel.replaceChildren(...elementos);
}
