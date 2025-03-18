<!-- esto saldra cuando den click en Feedback en la vista de kanban.blade.php-->
<!-- Modal para Historial de Feedback -->
<div id="modal-feedback" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-2/3 max-w-2xl">
        <h2 class="text-xl font-bold mb-4">Historial de Feedback</h2>
        <!-- Filtros -->
        <div class="flex items-center justify-between mb-4">
            <div>
                <label for="feedback-fecha-inicio" class="block text-gray-700 text-sm font-bold mb-2">Filtrar por
                    fecha:</label>
                <input type="date" id="feedback-fecha-inicio"
                    class="shadow appearance-none border rounded w-auto py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <label for="feedback-fecha-fin" class="block text-gray-700 text-sm font-bold mb-2">a</label>
                <input type="date" id="feedback-fecha-fin"
                    class="shadow appearance-none border rounded w-auto py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button id="btn-filtrar-fecha"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Filtrar</button>
            </div>
            <div>
                <label for="feedback-buscar" class="block text-gray-700 text-sm font-bold mb-2">Buscar por
                    texto:</label>
                <input type="text" id="feedback-buscar"
                    class="shadow appearance-none border rounded w-auto py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button id="btn-buscar-texto"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Buscar</button>
            </div>
        </div>
        <!-- Botones -->
        <div class="flex justify-between">
            <button id="modal-feedback-cerrar"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Regresar</button>
            <button id="btn-agregar-feedback"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Agregar
                Feedback</button>
        </div>
        <!-- Lista de todos los Feedback de la tarea cada fedback tendra un boton que permita editar el feedback-->
        <div class="overflow-y-auto" style="max-height: 300px;">
            <div id="lista-feedback" class="space-y-2">
                <!-- Aquí se listan los feedback -->
            </div>
        </div>
        <!-- Formulario para agregar feedback se mostrara cuando den click en el boton de agregar fitback o al dar clic en editar feffdab-->
        <form id="form-agregar-feedback" class="mb-4 hidden" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="feedback-comentario" class="block text-gray-700 text-sm font-bold mb-2">Agregar
                    Feedback:</label>
                <textarea name="comentario" id="feedback-comentario" placeholder="Escribe tu feedback aquí"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required></textarea>
            </div>
            <div class="mb-4">
                <label for="feedback-archivo" class="block text-gray-700 text-sm font-bold mb-2">Adjuntar archivo
                    (opcional):</label>
                <input type="file" name="archivo" id="feedback-archivo"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <input type="hidden" id="tarea-id-feedback">
            </div>
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Agregar</button>
        </form>
    </div>
</div>
