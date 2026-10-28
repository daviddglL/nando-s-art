document.addEventListener('DOMContentLoaded', function () {
    const fraseContainer = document.getElementById('frase-del-dia');
    // Solo la landing tiene el hueco para la frase
    if (!fraseContainer) return;

    fetch('/frase-proxy')
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            // textContent en vez de innerHTML: el texto viene de un servicio externo
            const cita = document.createElement('blockquote');
            cita.className = 'italic text-lg text-gray-700 mb-2';
            cita.textContent = `"${data.phrase}"`;

            const autor = document.createElement('p');
            autor.className = 'text-right text-sm text-gray-600';
            autor.textContent = `— ${data.author ?? 'Anónimo'}`;

            fraseContainer.replaceChildren(cita, autor);
        })
        .catch(error => {
            console.error('Error al obtener la frase:', error);
            const aviso = document.createElement('p');
            aviso.className = 'text-red-500';
            aviso.textContent = 'No se pudo cargar la frase del día.';
            fraseContainer.replaceChildren(aviso);
        });
});
