// Estado (Model) de la galería: un único objeto que el reducer sustituye por otro nuevo.
export const estadoInicial = Object.freeze({
    estilo: null, // estilo seleccionado (valor de App\Enums\Style)
    categoria: '', // tema (valor de App\Enums\Category); '' = todos
    busqueda: '', // texto buscado en el nombre y la descripción; '' = sin buscar
    obras: [], // obras del estilo, tal como las da ProductResource
    indice: 0, // obra que está en el centro del carrusel
    cargando: false,
    error: null,
});

// Los filtros de un estado en forma de texto: sirve para saber si una respuesta de la API sigue valiendo
export const claveFiltros = ({ estilo, categoria, busqueda }) => JSON.stringify([estilo, categoria, busqueda]);

// Parámetros de la API (/api/products?style=…&category=…&q=…), sin los filtros vacíos
export function parametrosApi({ estilo, categoria, busqueda }) {
    const parametros = new URLSearchParams({ style: estilo });
    if (categoria) parametros.set('category', categoria);
    if (busqueda) parametros.set('q', busqueda);
    return parametros.toString();
}

// Parámetros de la URL de la página (/galeria?estilo=…&categoria=…&q=…), para compartir el filtro
export function parametrosUrl({ estilo, categoria, busqueda }) {
    const parametros = new URLSearchParams();
    if (estilo) parametros.set('estilo', estilo);
    if (categoria) parametros.set('categoria', categoria);
    if (busqueda) parametros.set('q', busqueda);
    return parametros.toString();
}

// Máximo de tarjetas que se ven a la vez (impar, para que haya una en el centro)
export const MAX_VISIBLES = 5;

// Tarjetas que se pintan, con su posición respecto al centro (-2..2). El carrusel da la vuelta.
export function tarjetasVisibles(estado, maxVisibles = MAX_VISIBLES) {
    const total = estado.obras.length;
    if (total === 0) return [];

    const visibles = Math.min(maxVisibles, total);
    const mitad = Math.floor(visibles / 2);
    const tarjetas = [];

    for (let posicion = -mitad; posicion <= mitad; posicion++) {
        // Con un número par de obras sobra una a la derecha: se recorta para no repetirla
        if (tarjetas.length === visibles) break;
        const indice = (((estado.indice + posicion) % total) + total) % total;
        tarjetas.push({ obra: estado.obras[indice], posicion });
    }

    return tarjetas;
}
