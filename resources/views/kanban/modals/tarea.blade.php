<!-- Modal para Crear/Editar Tareas -->
<div class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden" id="modal-tarea">
    <div class="bg-white rounded-lg shadow-lg w-1/3">
        <form id="form-tarea">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-gray-700" id="modal-tarea-title">Crear Tarea</h2>
            </div>
            <div class="p-4">
                <input type="hidden" id="tarea-id">
                <input type="hidden" id="columna-id">

                <!-- Nombre de la tarea -->
                <div class="mb-4">
                    <label for="tarea-nombre" class="block text-gray-700 font-medium">Nombre de la Tarea</label>
                    <input type="text" id="tarea-nombre" class="w-full border border-gray-300 rounded-lg p-2 mt-1"
                        placeholder="Nombre de la tarea">
                </div>

                <!-- Descripcion de la tarea -->
                <div class="mb-4">
                    <label for="tarea-descripcion" class="block text-gray-700 font-medium">Descripcion de la
                        Tarea</label>
                    <textarea id="tarea-descripcion" class="w-full border border-gray-300 rounded-lg p-2 mt-1"
                        placeholder="Descripcion de la tarea"></textarea>
                </div>

                <!-- Estado de la tarea -->
                <div class="mb-4">
                    <label for="tarea-estado" class="block text-gray-700 font-medium">Estado de la Tarea</label>
                    <select id="tarea-estado" class="w-full border border-gray-300 rounded-lg p-2 mt-1">
                        <option value="pendiente">pendiente</option>
                        <option value="en progreso">en progreso</option>
                        <option value="completada">completada</option>
                    </select>
                </div>

                <!-- Prioridad de la tarea -->
                <div class="mb-4">
                    <label for="tarea-prioridad" class="block text-gray-700 font-medium">Prioridad de la Tarea</label>
                    <select id="tarea-prioridad" class="w-full border border-gray-300 rounded-lg p-2 mt-1">
                        <option value="baja">baja</option>
                        <option value="media">media</option>
                        <option value="alta">alta</option>
                    </select>
                </div>

                <!-- Fecha de vencimiento -->
                <div class="mb-4">
                    <label for="tarea-fecha-vencimiento" class="block text-gray-700 font-medium">Fecha de
                        Vencimiento</label>
                    <input type="date" id="tarea-fecha-vencimiento"
                        class="w-full border border-gray-300 rounded-lg p-2 mt-1">
                </div>

                <!-- Selección de usuarios -->
                <div class="mb-4">
                    <label for="tarea-usuario" class="block text-gray-700 font-medium">Asignar Usuarios</label>
                    <select id="tarea-usuario" class="w-full border border-gray-300 rounded-lg p-2 mt-1">
                        <option value="">Seleccione miembros</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Límite de tamaño de archivo -->
                <div class="mb-4">
                    <label for="tarea-file-size-limit" class="block text-gray-700 font-medium">Límite de Tamaño de
                        Archivo (KB)</label>
                    <input type="number" id="tarea-file-size-limit"
                        class="w-full border border-gray-300 rounded-lg p-2 mt-1"
                        placeholder="Límite de tamaño de archivo">
                </div>


            </div>
            <div class="p-4 border-t flex justify-end gap-2">
                <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md"
                    id="modal-tarea-cancelar">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
