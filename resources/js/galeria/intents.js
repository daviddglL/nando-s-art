// Intents: lo que el usuario (o una respuesta de la API) quiere que pase en la galería.
// Son objetos planos { type, ... }; nadie cambia el estado si no es a través de un intent.

export const SELECCIONAR_ESTILO = 'galeria/seleccionar-estilo';
export const SELECCIONAR_CATEGORIA = 'galeria/seleccionar-categoria';
export const BUSCAR = 'galeria/buscar';
export const OBRAS_CARGADAS = 'galeria/obras-cargadas';
export const ERROR_CARGA = 'galeria/error-carga';
export const SIGUIENTE_OBRA = 'galeria/siguiente-obra';
export const ANTERIOR_OBRA = 'galeria/anterior-obra';

// Intents que cambian los filtros y, por tanto, vuelven a pedir las obras a la API
export const CAMBIOS_DE_FILTRO = [SELECCIONAR_ESTILO, SELECCIONAR_CATEGORIA, BUSCAR];

export const seleccionarEstilo = (estilo) => ({ type: SELECCIONAR_ESTILO, estilo });
export const seleccionarCategoria = (categoria) => ({ type: SELECCIONAR_CATEGORIA, categoria });
export const buscar = (texto) => ({ type: BUSCAR, texto });
// clave: los filtros con los que se pidieron las obras (claveFiltros en state.js)
export const obrasCargadas = (clave, obras) => ({ type: OBRAS_CARGADAS, clave, obras });
export const errorCarga = (clave, mensaje) => ({ type: ERROR_CARGA, clave, mensaje });
export const siguienteObra = () => ({ type: SIGUIENTE_OBRA });
export const anteriorObra = () => ({ type: ANTERIOR_OBRA });
