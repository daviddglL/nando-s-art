document.addEventListener("DOMContentLoaded", () => {
    const botones = document.querySelectorAll(".filtro-estilo");
    const body = document.getElementById("body-app");

    const fondos = {
        acuarela: "bg-blue-100",
        rotulador: "bg-yellow-100",
        óleo: "bg-pink-100",
        sketch: "bg-gray-100",
        papel: "bg-green-100",
        lienzo: "bg-purple-100"
    };

    const cargarEstilo = (estilo) => {
        body.className = "min-h-screen " + (fondos[estilo] || "bg-gray-100");
        // ...tu código para cargar productos...
    };

    botones.forEach(boton => {
        boton.addEventListener("click", () => {
            cargarEstilo(boton.dataset.style);
        });
    });

    // Inicial
    cargarEstilo("acuarela");
});