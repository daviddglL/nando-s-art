import { CAMBIOS_DE_FILTRO, errorCarga, obrasCargadas } from './intents.js';
import { claveFiltros, parametrosApi } from './state.js';

// Pide a la API las obras que cumplen los filtros y lo traduce a un intent (nunca lanza)
export async function cargarObras(filtros, fetchFn = fetch) {
    const clave = claveFiltros(filtros);
    try {
        const respuesta = await fetchFn(`/api/products?${parametrosApi(filtros)}`, {
            headers: { Accept: 'application/json' },
        });
        if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);

        const { data } = await respuesta.json();
        return obrasCargadas(clave, data);
    } catch (error) {
        console.error('Error al cargar las obras:', error);
        return errorCarga(clave, 'No se han podido cargar las obras. Inténtalo de nuevo.');
    }
}

// Efectos: los intents que necesitan hablar con el exterior. El resultado vuelve como otro intent.
export function crearEfectos(fetchFn = fetch) {
    return async (intent, dispatch, getState) => {
        // Solo si el reducer ha aceptado el cambio de filtro (si no, sigue sin estar cargando)
        if (CAMBIOS_DE_FILTRO.includes(intent.type) && getState().cargando) {
            dispatch(await cargarObras(getState(), fetchFn));
        }
    };
}
