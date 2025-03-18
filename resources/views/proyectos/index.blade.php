@extends('layouts.app')

@section('title', 'Proyectos')

@section('content')
    <div class="max-w-4xl mx-auto py-4 sm:px-4 lg:px-6">
        <h2 class="font-semibold text-2xl text-indigo-600 dark:text-indigo-400 leading-tight mt-3 mb-6">
            {{ __('Tus Proyectos') }}
        </h2>

        <!-- Lista de proyectos -->
        <div class="space-y-6">
            @foreach ($proyectos as $proyecto)
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 flex flex-col space-y-4">
                    <div class="flex flex-col">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                            <strong>Nombre:</strong> {{ $proyecto->nombre }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">
                            <strong>Descripción:</strong> {{ $proyecto->descripcion }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            <strong>Fecha de Inicio:</strong> {{ $proyecto->fecha_inicio }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            <strong>Fecha de Fin:</strong> {{ $proyecto->fecha_fin }}
                        </p>
                    </div>

                    <!-- Usuarios asignados -->
                    <div class="mt-4">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-gray-100">Usuarios asignados:</h4>
                        <div class="mt-2 overflow-x-auto">
                            <table
                                class="min-w-full bg-gray-100 dark:bg-gray-700 text-sm text-left text-gray-800 dark:text-gray-200 rounded-lg shadow-lg">
                                <thead>
                                    <tr class="bg-gray-200 dark:bg-gray-800">
                                        <th class="py-2 px-4 font-semibold text-gray-800 dark:text-gray-200">Nombre</th>
                                        <th class="py-2 px-4 font-semibold text-gray-800 dark:text-gray-200">Correo</th>
                                        <th class="py-2 px-4 font-semibold text-gray-800 dark:text-gray-200">Rol</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Obtener el administrador del proyecto
                                        $administrador = $proyecto->jefe;

                                        // Obtener los jefes del proyecto
                                        $jefes = $proyecto->usuarios()->wherePivot('proyecto_role', 'jefe')->get();

                                        // Obtener los miembros del proyecto
                                        $miembros = $proyecto
                                            ->usuarios()
                                            ->wherePivot('proyecto_role', 'miembro')
                                            ->get();
                                    @endphp

                                    <!-- Mostrar al administrador primero -->
                                    @if ($administrador)
                                        <tr class="border-b border-gray-300 dark:border-gray-600">
                                            <td class="py-2 px-4 text-gray-800 dark:text-gray-200">
                                                {{ $administrador->name }}
                                            </td>
                                            <td class="py-2 px-4 text-gray-600 dark:text-gray-300">
                                                {{ $administrador->email }}
                                            </td>
                                            <td class="py-2 px-4 text-sm font-semibold text-red-600 dark:text-red-400">
                                                Administrador
                                            </td>
                                        </tr>
                                    @endif

                                    <!-- Mostrar los jefes -->
                                    @foreach ($jefes as $jefe)
                                        <tr class="border-b border-gray-300 dark:border-gray-600">
                                            <td class="py-2 px-4 text-gray-800 dark:text-gray-200">
                                                {{ $jefe->name }}
                                            </td>
                                            <td class="py-2 px-4 text-gray-600 dark:text-gray-300">
                                                {{ $jefe->email }}
                                            </td>
                                            <td class="py-2 px-4 text-sm font-semibold text-green-600 dark:text-green-400">
                                                Jefe
                                            </td>
                                        </tr>
                                    @endforeach

                                    <!-- Mostrar los miembros -->
                                    @foreach ($miembros as $miembro)
                                        <tr class="border-b border-gray-300 dark:border-gray-600">
                                            <td class="py-2 px-4 text-gray-800 dark:text-gray-200">
                                                {{ $miembro->name }}
                                            </td>
                                            <td class="py-2 px-4 text-gray-600 dark:text-gray-300">
                                                {{ $miembro->email }}
                                            </td>
                                            <td
                                                class="py-2 px-4 text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                                Miembro
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex justify-end space-x-4 mt-4">
                        @can('update', $proyecto)
                            <a href="{{ route('proyectos.edit', $proyecto->id) }}"
                                class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition duration-300 transform hover:scale-105">
                                Editar
                            </a>
                        @endcan

                        @can('delete', $proyecto)
                            <form action="{{ route('proyectos.destroy', $proyecto->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition duration-300 transform hover:scale-105"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este proyecto?')">
                                    Eliminar
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Modal de Error -->
        @if (session('error'))
            <div id="errorModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <!-- Icono de error aquí -->
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.342 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Error de Permiso
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            {{ session('error') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                onclick="document.getElementById('errorModal').style.display = 'none';">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                // Oculta el modal después de 5 segundos (opcional)
                setTimeout(function() {
                    var modal = document.getElementById('errorModal');
                    if (modal) {
                        modal.style.display = 'none';
                    }
                }, 5000);
            </script>
        @endif
    </div>
@endsection
