// Store: guarda el estado, aplica el reducer a cada intent, avisa a la vista y lanza los efectos.
// Flujo en un solo sentido: vista → intent → reducer → estado → vista.
export function crearStore(reducer, estadoInicial, efectos = () => {}) {
    let estado = estadoInicial;
    const suscriptores = new Set();

    const getState = () => estado;

    const dispatch = (intent) => {
        const anterior = estado;
        estado = reducer(estado, intent);
        if (estado !== anterior) {
            suscriptores.forEach((suscriptor) => suscriptor(estado));
        }

        return efectos(intent, dispatch, getState);
    };

    const subscribe = (suscriptor) => {
        suscriptores.add(suscriptor);
        return () => suscriptores.delete(suscriptor);
    };

    return { getState, dispatch, subscribe };
}
