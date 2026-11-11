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
            cita.className = 'font-display italic text-2xl md:text-3xl leading-snug text-tinta';
            cita.textContent = `«${data.phrase}»`;

            const autor = document.createElement('p');
            autor.className = 'mt-4 text-sm text-tinta-tenue';
            autor.textContent = `— ${data.author ?? 'Anónimo'}`;

            fraseContainer.replaceChildren(cita, autor);
        })
        .catch(error => {
            console.error('Error al obtener la frase:', error);
            const aviso = document.createElement('p');
            aviso.className = 'text-tinta-tenue';
            aviso.textContent = 'No se pudo cargar la frase del día.';
            fraseContainer.replaceChildren(aviso);
        });
});
