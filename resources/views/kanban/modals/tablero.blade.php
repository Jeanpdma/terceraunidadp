<!-- Modal para Crear/Editar Tableros -->
<div class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden" id="modal-tablero">
    <div class="bg-white rounded-lg shadow-lg w-1/3">
        <form id="form-tablero">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-gray-700" id="modal-tablero-title">Crear Tablero</h2>
            </div>
            <div class="p-4">
                <input type="hidden" id="tablero-id">
                <div class="mb-4">
                    <label for="tablero-nombre" class="block text-gray-700 font-medium">Nombre del Tablero</label>
                    <input type="text" id="tablero-nombre" class="w-full border border-gray-300 rounded-lg p-2 mt-1" placeholder="Nombre del tablero">
                </div>
            </div>
            <div class="p-4 border-t flex justify-end gap-2">
                <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md" id="modal-tablero-cancelar">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
