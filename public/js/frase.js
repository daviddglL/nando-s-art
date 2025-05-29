document.addEventListener('DOMContentLoaded', function () {
    const fraseContainer = document.getElementById('frase-del-dia');

    fetch('/frase-proxy')
        .then(response => response.json())
        .then(data => {
            fraseContainer.innerHTML = `
                <blockquote class="italic text-lg text-gray-700 mb-2">"${data.phrase}"</blockquote>
                <p class="text-right text-sm text-gray-600">— ${data.author}</p>
            `;
        })
        .catch(error => {
            console.error('Error al obtener la frase:', error);
            fraseContainer.innerHTML = '<p class="text-red-500">No se pudo cargar la frase del día.</p>';
        });
});
