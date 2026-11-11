import { tarjetasVisibles } from './state.js';

const CLASES_MAIN = 'transition-colors duration-700';
const CLASES_BOTON = 'filtro-estilo btn btn-pequeno';
const BOTON_INACTIVO = 'border border-tinta/15 bg-papel-claro/70 text-tinta-suave hover:border-tinta hover:text-tinta';

// Aspecto de cada tarjeta según lo lejos que esté del centro.
// En pantallas pequeñas se ocultan las de los lados para que el carrusel no se desborde.
const CLASES_POSICION = {
    0: 'z-20 scale-100',
    1: 'hidden sm:flex z-10 scale-[0.86] opacity-70 saturate-[0.85]',
    2: 'hidden lg:flex z-0 scale-[0.74] opacity-35 saturate-50',
};

const crearElemento = (etiqueta, clases, texto) => {
    const el = document.createElement(etiqueta);
    el.className = clases;
    // textContent y nunca innerHTML: el texto de las obras no se interpreta como HTML
    if (texto !== undefined) el.textContent = texto;
    return el;
};

// Medidas sin ceros de más: 30 x 40 cm, 29,7 x 42 cm
const medida = (valor) => String(Number(valor)).replace('.', ',');

export function crearTarjeta({ obra, posicion }) {
    const central = posicion === 0;
    const tarjeta = crearElemento(
        'article',
        `w-64 sm:w-72 shrink-0 flex flex-col transition-all duration-500 ease-out ${CLASES_POSICION[Math.abs(posicion)]}`,
    );
    tarjeta.dataset.posicion = String(posicion);
    if (!central) tarjeta.setAttribute('aria-hidden', 'true');

    // La obra enmarcada, como colgada en la pared
    const marco = crearElemento('div', `marco transition-shadow duration-500 ${central ? 'shadow-flotante' : ''}`);
    const imagen = document.createElement('img');
    imagen.src = obra.imagen_url;
    // Con miniaturas WebP el navegador elige la más pequeña que llene la tarjeta (unos 300 px de ancho)
    if (obra.srcset) {
        imagen.setAttribute('srcset', obra.srcset);
        imagen.setAttribute('sizes', '320px');
    }
    imagen.alt = obra.name;
    imagen.loading = 'lazy';
    imagen.className = 'w-full aspect-[4/5] object-cover bg-papel-oscuro';
    marco.append(imagen);

    // Cartela: título, técnica y tema, medidas y precio (o «Vendida»)
    const cartela = crearElemento('div', 'mt-5 px-1');
    const detalles = [obra.style_label, obra.category_label].filter(Boolean).join(' · ');
    cartela.append(
        crearElemento('h3', 'font-display text-2xl leading-tight text-tinta', obra.name),
        crearElemento('p', 'mt-1 text-sm text-tinta-suave', detalles),
        crearElemento('p', 'text-sm text-tinta-tenue', `${medida(obra.height)} × ${medida(obra.width)} cm`),
    );

    const pie = crearElemento('div', 'mt-4 flex items-center justify-between gap-3');
    if (obra.available === false) {
        pie.append(crearElemento('span', 'chip chip-vendida', 'Vendida'));
    } else {
        pie.append(crearElemento('span', 'text-sm font-semibold text-tinta', obra.precio_texto ?? ''));
    }

    // Enlace a la ficha de la obra (/obras/{slug}); solo se puede enfocar en la tarjeta central
    if (obra.url) {
        const enlace = crearElemento('a', 'enlace text-sm', 'Ver obra →');
        enlace.href = obra.url;
        enlace.setAttribute('aria-label', `Ver la ficha de ${obra.name}`);
        if (!central) enlace.tabIndex = -1;
        pie.append(enlace);
    }
    cartela.append(pie);

    tarjeta.append(marco, cartela);
    return tarjeta;
}

// Pinta el estado completo. Solo lee el estado: los cambios llegan siempre como intents.
export function render(estado, dom, config) {
    dom.main.className = `${CLASES_MAIN} ${config.fondos[estado.estilo ?? ''] ?? 'bg-papel'}`;

    dom.botones.forEach((boton) => {
        const activo = boton.dataset.style === (estado.estilo ?? '');
        boton.className = `${CLASES_BOTON} ${activo ? (config.coloresBoton[estado.estilo ?? ''] ?? '') : BOTON_INACTIVO}`;
        boton.setAttribute('aria-pressed', String(activo));
    });

    const sinObras = estado.obras.length < 2;
    dom.anterior.disabled = sinObras;
    dom.siguiente.disabled = sinObras;
    dom.carrusel.setAttribute('aria-busy', String(estado.cargando));

    if (estado.cargando) {
        dom.carrusel.replaceChildren(crearElemento('p', 'w-full text-center text-tinta-tenue py-12', 'Cargando obras…'));
        return;
    }

    if (estado.error) {
        dom.carrusel.replaceChildren(crearElemento('p', 'w-full text-center text-red-700 py-12', estado.error));
        return;
    }

    const tarjetas = tarjetasVisibles(estado);
    if (tarjetas.length === 0) {
        const aviso = estado.categoria || estado.busqueda
            ? 'No hay obras que cumplan estos filtros.'
            : estado.estilo
              ? 'Todavía no hay obras de este estilo.'
              : 'Todavía no hay obras en la galería.';
        dom.carrusel.replaceChildren(crearElemento('p', 'w-full text-center text-tinta-tenue py-12', aviso));
        return;
    }

    const elementos = tarjetas.map(crearTarjeta);
    // Con un número par de tarjetas falta una a la derecha: un hueco invisible mantiene la central en el centro
    if (tarjetas.length % 2 === 0) {
        const hueco = crearElemento('div', `w-64 sm:w-72 shrink-0 ${CLASES_POSICION[2]} invisible`);
        hueco.setAttribute('aria-hidden', 'true');
        elementos.push(hueco);
    }
    dom.carrusel.replaceChildren(...elementos);
}
