import {
    ANTERIOR_OBRA,
    BUSCAR,
    ERROR_CARGA,
    OBRAS_CARGADAS,
    SELECCIONAR_CATEGORIA,
    SELECCIONAR_ESTILO,
    SIGUIENTE_OBRA,
} from './intents.js';
import { claveFiltros } from './state.js';

// Reducer: función pura (estado, intent) => nuevo estado. No toca el DOM ni hace peticiones.
export function reducer(estado, intent) {
    switch (intent.type) {
        case SELECCIONAR_ESTILO:
            return filtrar(estado, { estilo: intent.estilo });

        case SELECCIONAR_CATEGORIA:
            return filtrar(estado, { categoria: intent.categoria ?? '' });

        case BUSCAR: {
            const busqueda = (intent.texto ?? '').trim();
            // Escribir un espacio de más no vuelve a pedir las obras
            if (busqueda === estado.busqueda) return estado;
            return filtrar(estado, { busqueda });
        }

        case OBRAS_CARGADAS:
            // Si el usuario ya ha cambiado los filtros, la respuesta antigua se descarta
            if (intent.clave !== claveFiltros(estado)) return estado;
            return {
                ...estado,
                obras: intent.obras,
                indice: Math.floor(intent.obras.length / 2), // empieza centrado
                cargando: false,
                error: null,
            };

        case ERROR_CARGA:
            if (intent.clave !== claveFiltros(estado)) return estado;
            return { ...estado, obras: [], indice: 0, cargando: false, error: intent.mensaje };

        case SIGUIENTE_OBRA:
            return mover(estado, 1);

        case ANTERIOR_OBRA:
            return mover(estado, -1);

        default:
            return estado;
    }
}

// Cambia un filtro: vacía las obras y marca que hay que cargarlas de nuevo
function filtrar(estado, cambios) {
    return { ...estado, ...cambios, obras: [], indice: 0, cargando: true, error: null };
}

// Avanza o retrocede en bucle; sin obras no hace nada
function mover(estado, paso) {
    const total = estado.obras.length;
    if (total === 0) return estado;

    return { ...estado, indice: (estado.indice + paso + total) % total };
}
