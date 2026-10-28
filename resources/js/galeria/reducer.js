import { ANTERIOR_OBRA, ERROR_CARGA, OBRAS_CARGADAS, SELECCIONAR_ESTILO, SIGUIENTE_OBRA } from './intents.js';

// Reducer: función pura (estado, intent) => nuevo estado. No toca el DOM ni hace peticiones.
export function reducer(estado, intent) {
    switch (intent.type) {
        case SELECCIONAR_ESTILO:
            return { ...estado, estilo: intent.estilo, obras: [], indice: 0, cargando: true, error: null };

        case OBRAS_CARGADAS:
            // Si el usuario ya ha cambiado de estilo, la respuesta antigua se descarta
            if (intent.estilo !== estado.estilo) return estado;
            return {
                ...estado,
                obras: intent.obras,
                indice: Math.floor(intent.obras.length / 2), // empieza centrado
                cargando: false,
                error: null,
            };

        case ERROR_CARGA:
            if (intent.estilo !== estado.estilo) return estado;
            return { ...estado, obras: [], indice: 0, cargando: false, error: intent.mensaje };

        case SIGUIENTE_OBRA:
            return mover(estado, 1);

        case ANTERIOR_OBRA:
            return mover(estado, -1);

        default:
            return estado;
    }
}

// Avanza o retrocede en bucle; sin obras no hace nada
function mover(estado, paso) {
    const total = estado.obras.length;
    if (total === 0) return estado;

    return { ...estado, indice: (estado.indice + paso + total) % total };
}
