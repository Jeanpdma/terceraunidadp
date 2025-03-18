<!-- archivos.blade.php -->
<div id="modal-archivos" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-2/3 max-w-2xl">
        <!-- Botones -->
        <div class="flex justify-between mb-4">
            <!-- Botón "Agregar archivo" (solo para jefe/administrador) -->
            @if(auth()->check() && (auth()->user()->role === 'administrador' || auth()->user()->role === 'jefe'))
                <button id="btn-agregar-archivo" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">Agregar archivo</button> <!-- Añadí mr-2 para un margen derecho -->
            @endif

            <!-- Botón "Entregar archivo" (solo para miembro asignado) -->
            @if(auth()->check() && auth()->user()->role === 'miembro')
                <button id="btn-entregar-archivo" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">Entregar archivo</button> <!-- Añadí mr-2 para un margen derecho -->
            @endif

            <!-- Botón "Regresar" cerrara el  modal-->
            <button id="modal-archivos-cerrar" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Regresar</button>
        </div>
        <h2 class="text-xl font-bold mb-4">Archivos</h2>
           <!-- Contenedor con altura fija y scroll -->
        <div class="overflow-y-auto" style="max-height: 300px;">
            <div id="lista-archivos" class="space-y-2">
                <!-- Aquí se listan los archivos -->
            </div>
         </div>

        <!-- Formulario para subir archivos solo se mostrara cuando den clic en Agregar archivo o Entregar archivo-->
        <form id="form-subir-archivo" class="mb-4 hidden" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="archivo-input" class="block text-gray-700 text-sm font-bold mb-2">Seleccionar archivo</label>
                <input type="file" id="archivo-input" name="archivo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <input type="hidden" id="tarea-id-archivo">
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Subir Archivo</button>
        </form>
    </div>
</div>
