@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto py-4 sm:px-4 lg:px-6">
        <h2 class="font-semibold text-2xl text-indigo-600 dark:text-indigo-400 leading-tight mt-3 mb-6">
            {{ __('Editar Proyecto') }}
        </h2>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4">
            <form action="{{ route('proyectos.update', $proyecto->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="nombre"
                        class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="{{ $proyecto->nombre }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="descripcion"
                        class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">Descripción:</label>
                    <textarea id="descripcion" name="descripcion"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline">{{ $proyecto->descripcion }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="fecha_inicio" class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">Fecha de
                        Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ $proyecto->fecha_inicio }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="fecha_fin" class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">Fecha de
                        Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="{{ $proyecto->fecha_fin }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">Asignar Usuarios al
                        Proyecto:</label>
                    <div style="max-height: 200px; overflow-y: auto; padding-right: 10px;">
                        <!-- Contenedor con scroll -->
                        @foreach ($usuarios as $usuario)
                            @if ($usuario->id != $proyecto->usuario_id)
                                <div class="mb-2">
                                    <label for="usuario_{{ $usuario->id }}" class="inline-flex items-center">
                                        <input type="checkbox" id="usuario_{{ $usuario->id }}"
                                            name="usuarios[{{ $usuario->id }}][id]" value="{{ $usuario->id }}"
                                            class="mr-2"
                                            {{ $proyecto->usuarios()->where('users.id', $usuario->id)->exists() ? 'checked' : '' }}>
                                        <span class="text-gray-700 dark:text-gray-200">{{ $usuario->name }}</span>
                                    </label>

                                    <select name="usuarios[{{ $usuario->id }}][role]"
                                        class="ml-4 shadow appearance-none border rounded py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="miembro"
                                            {{ $proyecto->usuarios()->where('users.id', $usuario->id)->wherePivot('proyecto_role', 'miembro')->exists() ? 'selected' : '' }}>
                                            Miembro</option>
                                        <option value="jefe"
                                            {{ $proyecto->usuarios()->where('users.id', $usuario->id)->wherePivot('proyecto_role', 'jefe')->exists() ? 'selected' : '' }}>
                                            Jefe</option>
                                        <option value="administrador"
                                            {{ $proyecto->usuarios()->where('users.id', $usuario->id)->wherePivot('proyecto_role', 'administrador')->exists() ? 'selected' : '' }}>
                                            Administrador</option>
                                    </select>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <!-- Fin del contenedor con scroll -->
                </div>

                <div class="flex items-center justify-between">
                    <button
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit">
                        Guardar Cambios
                    </button>
                    <a href="{{ route('proyectos.index') }}"
                        class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
