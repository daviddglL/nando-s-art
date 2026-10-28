// Intents: lo que el usuario (o una respuesta de la API) quiere que pase en la galería.
// Son objetos planos { type, ... }; nadie cambia el estado si no es a través de un intent.

export const SELECCIONAR_ESTILO = 'galeria/seleccionar-estilo';
export const OBRAS_CARGADAS = 'galeria/obras-cargadas';
export const ERROR_CARGA = 'galeria/error-carga';
export const SIGUIENTE_OBRA = 'galeria/siguiente-obra';
export const ANTERIOR_OBRA = 'galeria/anterior-obra';

export const seleccionarEstilo = (estilo) => ({ type: SELECCIONAR_ESTILO, estilo });
export const obrasCargadas = (estilo, obras) => ({ type: OBRAS_CARGADAS, estilo, obras });
export const errorCarga = (estilo, mensaje) => ({ type: ERROR_CARGA, estilo, mensaje });
export const siguienteObra = () => ({ type: SIGUIENTE_OBRA });
export const anteriorObra = () => ({ type: ANTERIOR_OBRA });
