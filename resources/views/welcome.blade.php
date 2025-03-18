<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <!-- Incluye Tailwind CSS para estilos -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-gradient-to-r from-gray-900 via-gray-700 to-red-600 flex flex-col items-center justify-center text-gray-100">
    <div class="text-center">
        <h1 class="text-5xl font-extrabold mb-4 text-gray-100">
            ¡Bienvenido a nuestra aplicación!
        </h1>
        <p class="text-xl mb-8 text-gray-300">
            Explora nuestros servicios y comienza a gestionar tus proyectos y tareas de manera eficiente.
        </p>
        <!-- Botón de inicio de sesión -->
        <a href="{{ route('login') }}" class="bg-gray-800 text-red-500 hover:bg-red-600 hover:text-gray-100 px-6 py-3 rounded-full text-lg font-semibold transition duration-300 ease-in-out">
            Iniciar sesión
        </a>
    </div>
</body>

</html>