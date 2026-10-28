import { errorCarga, obrasCargadas, SELECCIONAR_ESTILO } from './intents.js';

// Pide a la API las obras de un estilo y lo traduce a un intent (nunca lanza)
export async function cargarObras(estilo, fetchFn = fetch) {
    try {
        const respuesta = await fetchFn(`/api/products?style=${encodeURIComponent(estilo)}`, {
            headers: { Accept: 'application/json' },
        });
        if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);

        const { data } = await respuesta.json();
        return obrasCargadas(estilo, data);
    } catch (error) {
        console.error('Error al cargar las obras:', error);
        return errorCarga(estilo, 'No se han podido cargar las obras. Inténtalo de nuevo.');
    }
}

// Efectos: los intents que necesitan hablar con el exterior. El resultado vuelve como otro intent.
export function crearEfectos(fetchFn = fetch) {
    return async (intent, dispatch) => {
        if (intent.type === SELECCIONAR_ESTILO) {
            dispatch(await cargarObras(intent.estilo, fetchFn));
        }
    };
}
