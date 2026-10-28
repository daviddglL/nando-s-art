document.addEventListener('DOMContentLoaded', function () {
    const fraseContainer = document.getElementById('frase-del-dia');

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
            fraseContainer.innerHTML = '<p class="text-red-500">No se pudo cargar la frase del día.</p>';
        });
});
