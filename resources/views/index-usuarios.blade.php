@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <h2 class="font-semibold text-2xl text-indigo-600 dark:text-indigo-400 leading-tight mt-3 mb-6">
        {{ __('Gestión de Usuarios') }}
    </h2>

    <!-- Lista de usuarios como tarjetas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($usuarios as $usuario)
        <div id="usuario-{{ $usuario->id }}" class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 flex flex-col justify-between items-start transition-all hover:shadow-2xl">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">{{ $usuario->name }}</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2"><strong>Email: </strong>{{ $usuario->email }}</p>
            <p class="text-gray-600 dark:text-gray-300 mt-2"><strong>Rol: </strong><span class="usuario-role">{{ ucfirst($usuario->role) }}</span></p>

            <!-- Botón para cambiar el rol -->
            <button data-id="{{ $usuario->id }}" onclick="openEditModal(this)" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg mt-4 transition duration-300 transform hover:scale-105">
                Cambiar Rol
            </button>

            <button data-id="{{ $usuario->id }}" onclick="deleteUser(this)" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition duration-300 transform hover:scale-105">
                Eliminar
            </button>

        </div>
        @endforeach
    </div>

    <!-- Modal de Edición de Rol -->
    <div id="editModal" class="fixed inset-0 flex justify-center items-center bg-gray-500 bg-opacity-50 hidden z-50">
        <div class="bg-gray-700 p-8 rounded-lg w-11/12 sm:w-1/2 md:w-1/3 lg:w-1/4 border-2 border-gray-300 shadow-lg">
            <h3 class="text-2xl mb-6 text-white">Editar Rol de Usuario</h3>

            <form id="editForm" method="POST" onsubmit="updateRole(event)" action="">
                @csrf
                @method('PUT')

                <!-- Nombre del Usuario -->
                <p id="userName" class="text-white font-medium mb-4"></p>

                <!-- Selección de Rol -->
                <label for="role" class="block text-white font-medium mb-2">Seleccionar Rol</label>
                <select id="role" name="role" class="bg-gray-600 text-white border p-2 w-full mb-4 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
                    <option value="administrador">Administrador</option>
                    <option value="jefe">Jefe</option>
                    <option value="miembro">Miembro</option>
                </select>

                <!-- Campo oculto para el ID del usuario -->
                <input type="hidden" id="userId" name="id">

                <!-- Botones de guardar y cancelar -->
                <div class="flex justify-between mt-4">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition duration-300">
                        Guardar Cambios
                    </button>
                    <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition duration-300" onclick="closeEditModal()">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Función para abrir el modal de edición de rol
    function openEditModal(button) {
        var usuarioId = button.getAttribute('data-id'); // Obtener el ID del usuario
        // Obtener datos del usuario usando AJAX
        fetch(`/users/${usuarioId}/edit`) // Usar la ruta correcta con el ID del usuario
            .then(response => response.json())
            .then(data => {
                // Llenar el formulario con los datos del usuario
                document.getElementById('userName').textContent = `Cambiar rol para: ${data.usuario.name}`;
                document.getElementById('role').value = data.usuario.role;

                // Colocar el ID del usuario en el campo oculto
                document.getElementById('userId').value = data.usuario.id;

                // Asegurarse de que la URL del formulario incluya el ID del usuario correctamente
                let formAction = document.getElementById('editForm').action;
                formAction = `/users/${usuarioId}/update`; // La URL se actualiza dinámicamente
                document.getElementById('editForm').action = formAction; // Actualizar la URL de acción del formulario

                // Mostrar el modal
                document.getElementById('editModal').classList.remove('hidden');
            })
            .catch(error => console.error('Error al obtener los datos del usuario:', error));
    }

    // Función para actualizar el rol y modificar la vista sin recargar la página
    function updateRole(event) {
        event.preventDefault(); // Previene el comportamiento por defecto del formulario

        // Obtener el formulario
        var form = document.getElementById('editForm');

        // Crear un nuevo objeto FormData para obtener los datos del formulario
        var formData = new FormData(form);

        // Realizar la solicitud para actualizar el rol
        fetch(`/users/${formData.get('id')}/update`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json' // Si estás enviando JSON
                },
                body: JSON.stringify({
                    role: formData.get('role') // Usamos formData para obtener el valor del rol
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);

                // Actualizar el rol en la interfaz de usuario
                var usuarioRow = document.querySelector(`#usuario-${formData.get('id')}`);
                var rolElement = usuarioRow.querySelector('.usuario-role');
                rolElement.textContent = `${capitalizeFirstLetter(data.usuario.role)}`;

                // Cerrar el modal
                closeEditModal();
            })
            .catch(error => console.error('Error al actualizar el rol:', error));
    }

    // Función para eliminar un usuario
    // Función para eliminar un usuario
    function deleteUser(button) {
        var usuarioId = button.getAttribute('data-id');
        if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
            fetch(`/users/${usuarioId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    document.getElementById(`usuario-${usuarioId}`).remove(); // Eliminar la tarjeta del usuario
                })
                .catch(error => console.error('Error al eliminar el usuario:', error));
        }
    }

    // Función para capitalizar la primera letra (para mostrar "Administrador", etc.)
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Función para cerrar el modal de edición
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endsection
