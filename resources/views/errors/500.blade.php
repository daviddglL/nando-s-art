{{-- Sin layout ni Vite a propósito: si algo ha fallado, esta página no debe depender de nada más --}}
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Algo ha fallado · Nando's Art</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center;
               font-family: system-ui, sans-serif; color: #1f2937; background: linear-gradient(135deg, #dbeafe, #f3e8ff, #fce7f3); padding: 1rem; }
        p.codigo { font-size: 4rem; font-weight: 800; color: #d1d5db; margin: 0; }
        a { display: inline-block; margin-top: 1.5rem; background: #2563eb; color: #fff; padding: .5rem 1.25rem; border-radius: .5rem; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
    <main>
        <p class="codigo">500</p>
        <h1>Algo ha fallado</h1>
        <p>Estamos en ello. Prueba de nuevo dentro de un rato.</p>
        <a href="/">Volver al inicio</a>
    </main>
</body>
</html>
