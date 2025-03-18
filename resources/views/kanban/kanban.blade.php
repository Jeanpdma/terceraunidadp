@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6">Tablero Kanban</h1>

        <div class="flex items-center gap-4 mb-8">
            <select id="select-proyecto" class="border rounded-md p-2 w-64">
                <option value="">Seleccionar Proyecto</option>
                @foreach ($proyectos as $proyecto)
                    <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                @endforeach
            </select>
            <button id="btn-nuevo-tablero" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                + Nuevo Tablero
            </button>
        </div>

        <div id="kanban-tableros" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Los tableros se insertarán aquí -->
        </div>

        <!-- Modales (ocultos por defecto) -->
        @include('kanban.modals.tablero')
        @include('kanban.modals.tarea')
        @include('kanban.modals.archivos')
        @include('kanban.modals.feedback')
    </div>
@endsection

<template id="template-tablero">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="tablero-nombre font-bold"></h3>
            <div class="flex gap-2">
                <button class="btn-editar-tablero text-blue-500 hover:text-blue-600">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-eliminar-tablero text-red-500 hover:text-red-600">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <button class="btn-nueva-columna bg-gray-100 hover:bg-gray-200 w-full py-2 rounded-md mb-4">
            + Nueva Columna
        </button>
        <div class="kanban-columnas grid gap-4"></div>
    </div>
</template>

<template id="template-columna">
    <div class="bg-gray-50 rounded p-4" data-columna-id="">
        <div class="flex justify-between items-center mb-4">
            <h4 class="columna-nombre font-medium"></h4>
            <button class="btn-eliminar-columna text-red-500 hover:text-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="kanban-tareas space-y-2"></div>
        <button class="btn-nueva-tarea mt-4 text-blue-500 hover:bg-blue-600 w-full text-left">
            + Nueva Tarea
        </button>
    </div>
</template>

<template id="template-tarea">
    <div class="bg-white p-3 rounded shadow" data-tarea-id="">
        <div class="flex justify-between items-center mb-2">
            <h5 class="tarea-nombre font-medium"></h5>
            <div class="flex gap-2">
                <button class="btn-editar-tarea text-blue-500 hover:text-blue-600">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button class="btn-eliminar-tarea text-red-500 hover:text-red-600">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
                <button class="btn-gestionar-archivos text-green-500 hover:text-green-600"
                    onclick="gestionarArchivos(this)">
                    <i class="fas fa-file"></i> Archivos
                </button>
                <button class="btn-gestionar-feedback text-purple-500 hover:text-purple-600"
                    onclick="gestionarFeedback(this)">
                    <i class="fas fa-comments"></i> Feedback
                </button>
            </div>
        </div>
        <p class="tarea-descripcion text-sm text-gray-600 mb-2"></p>
        <div class="flex justify-between text-xs text-gray-500">
            <span class="tarea-estado"></span>
            <span class="tarea-prioridad"></span>
        </div>
        <div class="flex justify-between text-xs text-gray-500 mt-1">
            <span class="tarea-fecha"></span>
            <span class="tarea-usuario"></span>
        </div>
    </div>
</template>

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectProyecto = document.getElementById('select-proyecto');
            const kanbanTableros = document.getElementById('kanban-tableros');
            const modalTarea = document.getElementById('modal-tarea');
            const modalArchivos = document.getElementById('modal-archivos');
            const modalFeedback = document.getElementById('modal-feedback');
            let proyectoSeleccionadoId = null;

            selectProyecto.addEventListener('change', function() {
                proyectoSeleccionadoId = this.value;
                if (proyectoSeleccionadoId) {
                    cargarTableros(proyectoSeleccionadoId);
                } else {
                    kanbanTableros.innerHTML = '';
                }
            });

            async function cargarTableros(proyectoId) {
                try {
                    const response = await fetch(`/kanban/${proyectoId}/tableros`);
                    const data = await response.json();

                    console.log("Data de cargarTableros:", data);

                    kanbanTableros.innerHTML = '';

                    if (data.success && data.tableros) {
                        data.tableros.forEach(tablero => {
                            agregarTableroDOM(tablero);
                        });
                    } else {
                        alert(data.message || 'Error al cargar los tableros');
                    }
                } catch (error) {
                    console.error('Error al cargar tableros:', error);
                    alert('Error al cargar los tableros');
                }
            }

            function agregarTableroDOM(tablero) {
                const template = document.getElementById('template-tablero').content.cloneNode(true);
                const tableroElement = template.querySelector('.bg-white');
                tableroElement.setAttribute('data-tablero-id', tablero.id);
                tableroElement.querySelector('.tablero-nombre').textContent = tablero.nombre;

                const columnasContainer = tableroElement.querySelector('.kanban-columnas');
                columnasContainer.innerHTML = '';

                if (tablero.columns && Array.isArray(tablero.columns)) {
                    tablero.columns.forEach(columna => {
                        agregarColumnaDOM(columna, columnasContainer);
                    });
                }

                kanbanTableros.appendChild(tableroElement); // Cambia esto

                const btnEditarTablero = tableroElement.querySelector('.btn-editar-tablero');
                const btnEliminarTablero = tableroElement.querySelector('.btn-eliminar-tablero');
                const btnNuevaColumna = tableroElement.querySelector('.btn-nueva-columna');

                console.log("Enlace btn Borrar Tablero: ", btnEliminarTablero);
                console.log("btnEditarTablero: ", btnEditarTablero);

                btnEditarTablero.addEventListener('click', () => editarTablero(tablero));
                btnEliminarTablero.addEventListener('click', () => eliminarTablero(tablero.id));
                btnNuevaColumna.addEventListener('click', () => crearColumna(tablero.id, columnasContainer));
            }

            function agregarColumnaDOM(columna, columnasContainer) {
                const templateColumna = document.getElementById('template-columna').content.cloneNode(true);
                const columnaElement = templateColumna.querySelector('[data-columna-id]');

                columnaElement.setAttribute('data-columna-id', columna.id);
                templateColumna.querySelector('.columna-nombre').textContent = columna.nombre;

                const tareasContainer = columnaElement.querySelector('.kanban-tareas');
                tareasContainer.innerHTML = '';
                if (columna.tasks && Array.isArray(columna.tasks)) {
                    columna.tasks.forEach(tarea => {
                        agregarTareaDOM(tarea, tareasContainer, columna.id);
                    });
                }

                columnasContainer.appendChild(columnaElement);

                const btnEliminarColumna = columnaElement.querySelector('.btn-eliminar-columna');
                const btnNuevaTarea = columnaElement.querySelector('.btn-nueva-tarea');
                //**Agrega estos console.log aqui para verificar si se están renderizando los botones */
                console.log("btnEliminarColumna:", btnEliminarColumna);

                btnEliminarColumna.addEventListener('click', () => eliminarColumna(columna.id));
                btnNuevaTarea.addEventListener('click', () => abrirModalTarea(columna.id, tareasContainer));
            }

            function agregarTareaDOM(tarea, tareasContainer, columnaId) {
                const templateTarea = document.getElementById('template-tarea').content.cloneNode(true);
                const tareaElement = templateTarea.querySelector('[data-tarea-id]');

                tareaElement.setAttribute('data-tarea-id', tarea.id);
                tareaElement.querySelector('.tarea-nombre').textContent = tarea.nombre;
                tareaElement.querySelector('.tarea-descripcion').textContent = tarea.descripcion || '';
                tareaElement.querySelector('.tarea-estado').textContent = tarea.estado;
                tareaElement.querySelector('.tarea-prioridad').textContent = tarea.prioridad;
                tareaElement.querySelector('.tarea-fecha').textContent = tarea.fecha_vencimiento;

                // Mostrar el usuario asignado
                tareaElement.querySelector('.tarea-usuario').textContent = tarea.usuarios && tarea.usuarios.length >
                    0 ? 'Asignado a: ' + tarea.usuarios[0].name : 'Sin asignar';

                const btnEditarTarea = tareaElement.querySelector('.btn-editar-tarea');
                const btnEliminarTarea = tareaElement.querySelector('.btn-eliminar-tarea');
                const btnGestionarArchivos = tareaElement.querySelector('.btn-gestionar-archivos');
                const btnGestionarFeedback = tareaElement.querySelector('.btn-gestionar-feedback');
                btnEditarTarea.addEventListener('click', () => editarTarea(tarea.id, columnaId));
                btnEliminarTarea.addEventListener('click', () => eliminarTarea(tarea.id));
                btnGestionarArchivos.addEventListener('click', () => gestionarArchivos(tarea.id));
                btnGestionarFeedback.addEventListener('click', () => gestionarFeedback(tarea.id));

                tareasContainer.appendChild(tareaElement);
            }

            function mostrarModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                }
            }

            function ocultarModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                }
            }

            function cargarUsuariosProyecto(proyectoId) {
                return new Promise((resolve, reject) => {
                    fetch(`/proyectos/${proyectoId}/usuarios`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const usuarios = data.usuarios;
                                const selectUsuario = document.getElementById('tarea-usuario');
                                selectUsuario.innerHTML = ''; // Limpiar opciones existentes

                                usuarios.forEach(usuario => {
                                    const option = document.createElement('option');
                                    option.value = usuario.id;
                                    option.textContent = usuario.name;
                                    $(selectUsuario).append(
                                        `<option value="${usuario.id}">${usuario.name}</option>`
                                    ); // Use jQuery here

                                });
                                resolve();
                            } else {
                                alert(data.message || 'Error al cargar los usuarios del proyecto');
                                reject(data.message || 'Error al cargar los usuarios del proyecto');
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar los usuarios del proyecto:', error);
                            alert('Error al cargar los usuarios del proyecto');
                            reject(error);
                        });
                });
            }

            async function editarTarea(tareaId, columnaId) {
                try {
                    const response = await fetch(`/kanban/tareas/${tareaId}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    document.getElementById('columna-id').value = columnaId;
                    if (data.success) {
                        const tarea = data.tarea;
                        document.getElementById('tarea-id').value = tarea.id;
                        document.getElementById('columna-id').value = columnaId;
                        document.getElementById('modal-tarea-title').textContent = 'Editar Tarea';
                        document.getElementById('tarea-nombre').value = tarea.nombre;
                        document.getElementById('tarea-descripcion').value = tarea.descripcion || '';
                        document.getElementById('tarea-estado').value = tarea.estado;
                        document.getElementById('tarea-prioridad').value = tarea.prioridad;
                        document.getElementById('tarea-fecha-vencimiento').value = tarea.fecha_vencimiento;
                        document.getElementById('tarea-file-size-limit').value = tarea.file_size_limit ? tarea
                            .file_size_limit / 1024 : '';

                        // Cargar usuarios y seleccionar el asignado
                        cargarUsuariosProyecto(proyectoSeleccionadoId).then(() => {
                            if (tarea.usuarios && tarea.usuarios.length > 0) {
                                $("#tarea-usuario").val(tarea.usuarios[0].id).trigger('change');
                            }
                        });

                        mostrarModal('modal-tarea');
                    } else {
                        alert(data.message || 'Error al obtener los detalles de la tarea');
                    }
                } catch (error) {
                    console.error('Error al cargar la tarea:', error);
                    alert('Error al cargar la tarea');
                }
            }

            async function cargarArchivos(tareaId) {
                const listaArchivos = document.getElementById('lista-archivos');

                try {
                    const response = await fetch(`/kanban/tareas/${tareaId}/archivos`);
                    const data = await response.json();

                    if (data.success) {
                        listaArchivos.innerHTML = '';

                        if (data.archivos.length === 0) {
                            listaArchivos.innerHTML = `
                    <div class="flex items-center justify-center p-6 text-gray-500">
                        <div class="text-center">
                            <i class="fas fa-file-alt text-4xl mb-2"></i>
                            <p>No hay archivos disponibles.</p>
                        </div>
                    </div>`;
                        } else {
                            const containerDiv = document.createElement('div');
                            containerDiv.className = 'space-y-4 p-4';

                            data.archivos.forEach(archivo => {
                                const fechaSubida = new Date(archivo.created_at).toLocaleDateString();
                                const archivoElement = document.createElement('div');
                                archivoElement.className =
                                    'bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow duration-200';

                                // Determinar el ícono basado en el tipo de archivo
                                const extension = archivo.nombre.split('.').pop().toLowerCase();
                                let fileIcon = 'fa-file';
                                if (['pdf'].includes(extension)) fileIcon = 'fa-file-pdf';
                                else if (['doc', 'docx'].includes(extension)) fileIcon = 'fa-file-word';
                                else if (['xls', 'xlsx'].includes(extension)) fileIcon =
                                    'fa-file-excel';
                                else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) fileIcon =
                                    'fa-file-image';

                                let archivoHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <i class="fas ${fileIcon} text-gray-400 text-xl"></i>
                                <div>
                                    <h4 class="font-medium text-gray-800">${archivo.nombre}</h4>
                                    <p class="text-sm text-gray-500">Subido el ${fechaSubida}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">`;

                                // Si la ruta es un enlace de Google Drive, mostrar un enlace a Google Drive
                                if (archivo.ruta.startsWith('https://')) {
                                    archivoHTML += `
                            <a href="${archivo.ruta}" target="_blank"
                                class="inline-flex items-center px-3 py-1 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                Ver en Google Drive
                            </a>`;
                                } else {
                                    // Si no es un enlace de Google Drive, mostrar el enlace de descarga
                                    archivoHTML += `
                            <a href="/kanban/archivos/${archivo.id}/descargar"
                                class="inline-flex items-center px-3 py-1 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                                <i class="fas fa-download mr-1"></i>
                                Descargar
                            </a>`;
                                }

                                archivoHTML += `
                            <button onclick="eliminarArchivo(${archivo.id})"
                                class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;

                                archivoElement.innerHTML = archivoHTML;
                                containerDiv.appendChild(archivoElement);
                            });

                            listaArchivos.appendChild(containerDiv);
                        }

                        // Agregar estilos al contenedor del modal
                        const modalContent = listaArchivos.closest('.modal-content');
                        if (modalContent) {
                            modalContent.className =
                                'modal-content bg-white rounded-lg shadow-xl max-w-2xl mx-auto mt-10 max-h-[80vh] overflow-y-auto';
                        }
                    }
                } catch (error) {
                    console.error('Error al cargar archivos:', error);
                    listaArchivos.innerHTML = `
            <div class="text-center p-6 text-red-500">
                <i class="fas fa-exclamation-circle text-4xl mb-2"></i>
                <p>Error al cargar los archivos. Por favor, intente nuevamente.</p>
            </div>`;
                }
            }

            function gestionarArchivos(tareaId) {
                document.getElementById('tarea-id-archivo').value = tareaId;
                mostrarModal('modal-archivos');
                cargarArchivos(tareaId);

                const formSubirArchivo = document.getElementById('form-subir-archivo');
                const modalArchivosCerrar = document.getElementById('modal-archivos-cerrar');
                const btnAgregarArchivo = document.getElementById('btn-agregar-archivo');
                const btnEntregarArchivo = document.getElementById('btn-entregar-archivo');
                if (modalArchivosCerrar) {
                    modalArchivosCerrar.addEventListener('click', function() {
                        ocultarModal('modal-archivos');
                        if (formSubirArchivo) {
                            formSubirArchivo.classList.add('hidden');
                        }
                    });
                }
                if (btnAgregarArchivo) {
                    btnAgregarArchivo.addEventListener('click', function() {
                        if (formSubirArchivo) {
                            formSubirArchivo.classList.remove('hidden');
                        }
                        if (formSubirArchivo) {
                            formSubirArchivo.dataset.action = 'agregar';
                        }
                    });
                }
                if (btnEntregarArchivo) {
                    btnEntregarArchivo.addEventListener('click', function() {
                        if (formSubirArchivo) {
                            formSubirArchivo.classList.remove('hidden');
                        }
                        if (formSubirArchivo) {
                            formSubirArchivo.dataset.action = 'entregar';
                        }
                    });
                }

                if (formSubirArchivo) {
                    formSubirArchivo.addEventListener('submit', async function(event) {
                        event.preventDefault();
                        const tareaId = document.getElementById('tarea-id-archivo').value;
                        const archivoInput = document.getElementById('archivo-input');
                        const archivo = archivoInput.files[0];

                        if (!archivo) {
                            alert('Por favor, selecciona un archivo.');
                            return;
                        }

                        const formData = new FormData();
                        formData.append('archivo', archivo);

                        try {
                            const response = await fetch(`/kanban/tareas/${tareaId}/archivos`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: formData
                            });
                            const data = await response.json();
                            if (data.success) {
                                alert(data.mensaje);
                                cargarArchivos(tareaId);
                                formSubirArchivo.classList.add('hidden');
                            } else {
                                alert(data.message || 'Error al subir el archivo.');
                            }
                        } catch (error) {
                            console.error('Error al subir el archivo:', error);
                            alert('Error al subir el archivo.');
                        }
                    });
                }
            }

            async function cargarFeedback(tareaId) {
                const listaFeedback = document.getElementById('lista-feedback');
                try {
                    const response = await fetch(`/kanban/tareas/${tareaId}/feedback`);
                    const data = await response.json();

                    if (data.success) {
                        listaFeedback.innerHTML = '';
                        if (data.feedback.length === 0) {
                            listaFeedback.innerHTML = '<p>No hay feedback disponible.</p>';
                        } else {
                            const baseUrl = data.base_url;

                            data.feedback.forEach(item => {
                                const feedbackElement = document.createElement('div');
                                feedbackElement.className = 'mb-4 p-4 bg-gray-100 rounded';
                                let archivoAdjuntoHTML = '';
                                if (item.archivo_adjunto) {
                                    if (item.archivo_adjunto.startsWith('https://')) {
                                        archivoAdjuntoHTML = `
                                            <a href="${item.archivo_adjunto}" target="_blank" class="text-blue-500 hover:text-blue-600">
                                                <i class="fas fa-external-link-alt mr-1"></i>
                                                Ver archivo en Google Drive
                                            </a>
                                        `;
                                    } else {
                                        archivoAdjuntoHTML = `
                                            <a href="${baseUrl}storage/${item.archivo_adjunto}" target="_blank" class="text-blue-500 hover:text-blue-600">
                                                <i class="fas fa-file mr-1"></i>
                                                Ver archivo adjunto
                                            </a>
                                        `;
                                    }
                                }
                                feedbackElement.innerHTML = `
                            <p><strong>${item.usuario.name}</strong> - ${new Date(item.created_at).toLocaleString()}</p>
                            <p>${item.comentario}</p>
                            ${archivoAdjuntoHTML}
                        `;
                                listaFeedback.appendChild(feedbackElement);
                            });
                        }
                    } else {
                        console.error('Error al cargar feedback:', data.message);
                        alert('Error al cargar feedback');
                    }
                } catch (error) {
                    console.error('Error al cargar feedback:', error);
                    alert('Error al cargar feedback');
                }
            }

            function gestionarFeedback(tareaId) {
    document.getElementById('tarea-id-feedback').value = tareaId;
    mostrarModal('modal-feedback');
    cargarFeedback(tareaId);

    const modalFeedbackCerrar = document.getElementById('modal-feedback-cerrar');
    const formAgregarFeedback = document.getElementById('form-agregar-feedback');
    const btnFiltrarFecha = document.getElementById('btn-filtrar-fecha');
    const btnBuscarTexto = document.getElementById('btn-buscar-texto');

    if (modalFeedbackCerrar) {
        modalFeedbackCerrar.addEventListener('click', function() {
            ocultarModal('modal-feedback');
            if (formAgregarFeedback) {
                formAgregarFeedback.classList.add('hidden');
            }
        });
    }

    const btnAgregarFeedback = document.getElementById('btn-agregar-feedback');
    if (btnAgregarFeedback) {
        btnAgregarFeedback.addEventListener('click', function() {
            if (formAgregarFeedback) {
                formAgregarFeedback.classList.remove('hidden');
            }
        });
    }
    if (formAgregarFeedback) {
        formAgregarFeedback.addEventListener('submit', async function(event) {
            event.preventDefault();

            const tareaId = document.getElementById('tarea-id-feedback').value;
            const comentario = document.getElementById('feedback-comentario').value;
            const archivoInput = document.getElementById('feedback-archivo');
            const archivo = archivoInput.files[0];
            const formData = new FormData();
            formData.append('comentario', comentario);
            if (archivo) {
                formData.append('archivo', archivo);
            }

            try {
                const response = await fetch(`/kanban/tareas/${tareaId}/feedback`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    alert(data.mensaje);
                    cargarFeedback(tareaId);
                    document.getElementById('form-agregar-feedback').classList.add(
                        'hidden');
                    document.getElementById('feedback-comentario').value =
                        ''; // Limpiar el textarea
                    if (archivoInput.files.length > 0) {
                        archivoInput.value = ''; // Limpiar el input de archivo
                    }
                } else {
                    alert(data.message || 'Error al agregar feedback.');
                }
            } catch (error) {
                console.error('Error al agregar feedback:', error);
                alert('Error al agregar feedback.');
            }
        });
    }
    if (btnFiltrarFecha) {
        btnFiltrarFecha.addEventListener('click', filtrarFeedbacks);
    }
    if (btnBuscarTexto) {
        btnBuscarTexto.addEventListener('click', filtrarFeedbacks);
    }

    async function filtrarFeedbacks() {
        const tareaId = document.getElementById('tarea-id-feedback').value;
        const fechaInicio = document.getElementById('feedback-fecha-inicio').value;
        const fechaFin = document.getElementById('feedback-fecha-fin').value;
        const buscarTexto = document.getElementById('feedback-buscar').value;
        let url = `/kanban/tareas/${tareaId}/feedback?`;
        if (fechaInicio) url += `fecha_inicio=${fechaInicio}&`;
        if (fechaFin) url += `fecha_fin=${fechaFin}&`;
        if (buscarTexto) url += `buscar=${buscarTexto}&`;
        try {
            const response = await fetch(url);
            const data = await response.json();
            if (data.success) {
                cargarFeedback(tareaId, data
                    .feedback); // Pasa el feedback filtrado a la función cargarFeedback
            } else {
                console.error('Error al filtrar feedback:', data.message);
                alert('Error al filtrar feedback');
            }
        } catch (error) {
            console.error('Error al filtrar feedback:', error);
            alert('Error al filtrar feedback');
        }
    }
}

            // FORM SUBMIT TAREA
            if (modalTarea) {
                document.getElementById('form-tarea').addEventListener('submit', async function(event) {
                    event.preventDefault();

                    const tareaId = document.getElementById('tarea-id').value;
                    const columnaId = document.getElementById('columna-id').value;
                    const nombre = document.getElementById('tarea-nombre').value;
                    const descripcion = document.getElementById('tarea-descripcion').value;
                    const estado = document.getElementById('tarea-estado').value;
                    const prioridad = document.getElementById('tarea-prioridad').value;
                    const fecha_vencimiento = document.getElementById('tarea-fecha-vencimiento').value;
                    const usuario_id = $("#tarea-usuario").val();
                    const file_size_limit = document.getElementById('tarea-file-size-limit').value;

                    const formData = {
                        nombre: nombre,
                        descripcion: descripcion,
                        estado: estado,
                        prioridad: prioridad,
                        fecha_vencimiento: fecha_vencimiento,
                        usuario_id: usuario_id,
                        file_size_limit: file_size_limit
                    };
                    let method = 'PUT';
                    let url = `/kanban/tareas/${tareaId}`;
                    if (!tareaId) {
                        method = 'POST';
                        url = `/kanban/${columnaId}/tareas`;
                    }

                    try {
                        const response = await fetch(url, { //`${url}`,
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(formData)
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert(data.mensaje);
                            ocultarModal('modal-tarea');
                            cargarTableros(proyectoSeleccionadoId); // Recarga los tableros
                        } else {
                            alert(data.message || 'Error al actualizar la tarea.');
                        }
                    } catch (error) {
                        console.error('Error al actualizar la tarea:', error);
                        alert('Error al actualizar la tarea.');
                    }
                });
            }
            const modalTareaCancelar = document.getElementById('modal-tarea-cancelar');
            if (modalTareaCancelar) {
                modalTareaCancelar.addEventListener('click', function() {
                    ocultarModal('modal-tarea');
                });
            }

            // Crear Tablero
            document.getElementById('btn-nuevo-tablero').addEventListener('click', function() {
                document.getElementById('tablero-id').value = '';
                document.getElementById('tablero-nombre').value = '';
                document.getElementById('modal-tablero-title').textContent = 'Crear Tablero';
                mostrarModal('modal-tablero');
            });

            document.getElementById('form-tablero').addEventListener('submit', async function(event) {
                event.preventDefault();
                const tableroId = document.getElementById('tablero-id').value;
                const nombre = document.getElementById('tablero-nombre').value;
                const proyectoId = document.getElementById('select-proyecto').value;
                const url = tableroId ? `/kanban/${proyectoId}/tableros/${tableroId}` :
                    `/kanban/${proyectoId}/tableros`;
                const method = tableroId ? 'PUT' : 'POST';

                fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            nombre
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            cargarTableros(proyectoId);
                            ocultarModal('modal-tablero');
                        } else {
                            alert(data.message || 'Error al guardar el tablero');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            // Cerrar Modal Tablero
            const modalCancelarTablero = document.getElementById('modal-tablero-cancelar');
            if (modalCancelarTablero) {
                modalCancelarTablero.addEventListener('click', function() {
                    ocultarModal('modal-tablero');
                });
            }

            // Editar Tablero
            function editarTablero(tablero) {
                document.getElementById('tablero-id').value = tablero.id;
                document.getElementById('tablero-nombre').value = tablero.nombre;
                document.getElementById('modal-tablero-title').textContent = 'Editar Tablero';
                mostrarModal('modal-tablero');
            }

            // Eliminar Tablero
            function eliminarTablero(tableroId) {
                const proyectoId = document.getElementById('select-proyecto').value;
                if (!confirm('¿Seguro que deseas eliminar este tablero?')) return;

                fetch(`/kanban/${proyectoId}/tableros/${tableroId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            cargarTableros(proyectoId);
                        } else {
                            alert(data.message || 'Error al eliminar el tablero');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Abrir Modal Tarea
            function abrirModalTarea(columnaId, tareasContainer) {
                // Limpiar los campos del modal
                document.getElementById('tarea-id').value = '';
                document.getElementById('tarea-nombre').value = '';
                document.getElementById('tarea-descripcion').value = '';
                document.getElementById('tarea-estado').value = 'pendiente'; // Valor por defecto
                document.getElementById('tarea-prioridad').value = 'media'; // Valor por defecto
                document.getElementById('tarea-fecha-vencimiento').value = '';
                document.getElementById('tarea-file-size-limit').value = '';


                document.getElementById('columna-id').value = columnaId;

                // Cambiar el título del modal
                document.getElementById('modal-tarea-title').textContent = 'Crear Nueva Tarea';

                // Cargar usuarios del proyecto
                cargarUsuariosProyecto(proyectoSeleccionadoId).then(() => {
                    // Mostrar el modal
                    mostrarModal('modal-tarea');
                    // Inicializar Select2 después de cargar los usuarios y mostrar el modal
                    $('#tarea-usuario').select2({
                        placeholder: 'Seleccione un usuario',
                        allowClear: true // Opcional: Permite deseleccionar el usuario
                    });
                });
            }
            // Crear Columna
            function crearColumna(tableroId, columnasContainer) {
                const nombre = prompt('Nombre de la nueva columna:');
                if (!nombre) return;

                fetch(`/kanban/${tableroId}/columnas`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            nombre
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Respuesta al crear columna:', data); // Aquí puedes inspeccionar los datos

                        if (data.success) {
                            agregarColumnaDOM(data.columna, columnasContainer);
                        } else {
                            alert(data.message || 'Error al crear columna');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Eliminar Columna
            function eliminarColumna(columnaId) {
                if (!confirm('¿Seguro que deseas eliminar esta columna?')) return;

                fetch(`/kanban/columnas/${columnaId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const columnaElement = document.querySelector(`[data-columna-id="${columnaId}"]`);
                            if (columnaElement) {
                                columnaElement.remove();
                            }
                        } else {
                            alert(data.message || 'Error al eliminar la columna');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Eliminar Tarea
            function eliminarTarea(tareaId) {
                if (!confirm('¿Seguro que deseas eliminar esta tarea?')) return;

                fetch(`/kanban/tareas/${tareaId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const tareaElement = document.querySelector(`[data-tarea-id="${tareaId}"]`);
                            if (tareaElement) {
                                tareaElement.remove();
                            }
                        } else {
                            alert(data.message || 'Error al eliminar la tarea');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    </script>
@endsection
