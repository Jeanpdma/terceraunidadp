@extends('layouts.app')

@section('title', 'Crear Proyecto')

@section('header')
<h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Crear Proyecto') }}
</h2>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <h2 class="font-semibold text-2xl text-indigo-600 dark:text-indigo-400 leading-tight mt-3 mb-6">
        {{ __('Crear Proyectos') }}
    </h2>

    <div class="max-w-2xl mx-auto mt-6 dark:bg-gray-800 shadow-md rounded-lg p-6">
        <form id="crear-proyecto-form" method="POST" action="{{ route('proyectos.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="max-w-xs">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del Proyecto</label>
                    <input type="text" id="nombre" name="nombre" class="mt-1 block w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    <span id="error-nombre" class="text-red-500 text-sm mt-1 hidden"></span>
                </div>
                <div class="max-w-xs">
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="mt-1 block w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="max-w-xs">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="mt-1 block w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500" rows="3" required></textarea>
                    <span id="error-descripcion" class="text-red-500 text-sm mt-1 hidden"></span>
                </div>
                <div class="max-w-xs">
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="mt-1 block w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                </div>
            </div>

            <!-- Lista desplegable de miembros del proyecto -->
            <div class="max-w-xs">
                <label for="usuario_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Agregar Miembros al Proyecto</label>
                <select id="usuario_select" class="mt-1 block w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Seleccione un miembro</option>
                    @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Lista de miembros seleccionados -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Miembros Seleccionados</label>
                <ul id="miembros_seleccionados" class="list-disc pl-5 space-y-2">
                    <!-- Los miembros seleccionados aparecerán aquí -->
                </ul>
            </div>

            <!-- Campo oculto para enviar los miembros seleccionados -->
            <input type="hidden" name="usuarios" id="usuarios_input">

            <div class="text-right mt-4">
                <button type="submit" class="px-4 py-2 bg-indigo-500 text-white font-semibold rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-150">Crear Proyecto</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('crear-proyecto-form');
        const usuarioSelect = document.getElementById('usuario_select');
        const miembrosSeleccionados = document.getElementById('miembros_seleccionados');
        const usuariosInput = document.getElementById('usuarios_input');
        const miembrosArray = [];

        // Agregar miembro seleccionado a la lista de miembros
        usuarioSelect.addEventListener('change', function() {
            const selectedUserId = usuarioSelect.value;
            const selectedUserName = usuarioSelect.options[usuarioSelect.selectedIndex].text;

            if (selectedUserId && !miembrosArray.includes(selectedUserId)) {
                miembrosArray.push(selectedUserId);
                const li = document.createElement('li');
                li.textContent = selectedUserName;
                li.classList.add('text-gray-700', 'dark:text-gray-300');
                
                // Crear botón de eliminar
                const removeButton = document.createElement('button');
                removeButton.textContent = 'Eliminar';
                removeButton.classList.add('text-red-500', 'ml-2', 'text-sm');
                removeButton.addEventListener('click', function() {
                    // Eliminar miembro de la lista y del array
                    miembrosArray.splice(miembrosArray.indexOf(selectedUserId), 1);
                    li.remove();
                    updateHiddenInput();
                });

                li.appendChild(removeButton);
                li.setAttribute('data-user-id', selectedUserId);

                // Agregar el nuevo miembro a la lista de miembros seleccionados
                miembrosSeleccionados.appendChild(li);

                // Actualizar el campo oculto con el array de usuarios seleccionados
                updateHiddenInput();
            }

            // Limpiar el campo de selección
            usuarioSelect.value = '';
        });

        // Actualizar el campo oculto con los miembros seleccionados
        function updateHiddenInput() {
            usuariosInput.value = JSON.stringify(miembrosArray);
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Construir los datos del formulario
            const formData = {
                nombre: form.nombre.value,
                descripcion: form.descripcion.value,
                fecha_inicio: form.fecha_inicio.value,
                fecha_fin: form.fecha_fin.value,
                usuarios: JSON.parse(usuariosInput.value), // Convertir de JSON string a array
            };

            fetch("{{ route('proyectos.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify(formData), // Convertir los datos a JSON
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Proyecto creado exitosamente');
                        window.location.href = "{{ route('proyectos.index') }}";
                    } else {
                        alert(data.error || 'Error al crear el proyecto');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

    });
</script>
@endsection
