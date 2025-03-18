{{--
@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6">Crear KanbanBoard</h1>

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

        <div id="kanban-tableros" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

        <!-- Modal para Crear/Editar Tablero -->
        <div id="modal-tablero" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-96">
                <h2 id="modal-tablero-title" class="text-xl font-bold mb-4">Crear Tablero</h2>
                <form id="form-tablero" class="space-y-4">
                    <input type="hidden" id="tablero-id">
                    <div>
                        <label for="tablero-nombre" class="block text-sm font-medium text-gray-700">Nombre del
                            Tablero</label>
                        <input type="text" id="tablero-nombre"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="modal-cancelar" class="px-4 py-2 border rounded-md">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Template para Tablero -->
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
                    <button class="btn-nueva-columna bg-gray-100 hover:bg-gray-200 w-full py-2 rounded-md mb-4">
                        + Nueva Columna
                    </button>
                    <div class="kanban-columnas grid gap-4"></div>
                </div>
            </div>
        </template>

        <!-- Template para Columna -->
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

        <!-- Template para Tarea -->
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

        <!-- Modal para Crear/Editar Tarea -->
        <div id="modal-tarea" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-2/3 max-w-2xl">
                <h2 id="modal-tarea-title" class="text-xl font-bold mb-4">Crear Tarea</h2>
                <form id="form-tarea" class="space-y-4" enctype="multipart/form-data">
                    <input type="hidden" id="tarea-id">
                    <input type="hidden" id="columna-id">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tarea-nombre" class="block text-sm font-medium text-gray-700">Nombre de la
                                Tarea</label>
                            <input type="text" id="tarea-nombre" name="nombre"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        <div>
                            <label for="tarea-estado" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select id="tarea-estado" name="estado"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="en progreso">En Progreso</option>
                                <option value="completada">Completada</option>
                            </select>
                        </div>
                        <div>
                            <label for="tarea-prioridad" class="block text-sm font-medium text-gray-700">Prioridad</label>
                            <select id="tarea-prioridad" name="prioridad"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                            </select>
                        </div>
                        <div>
                            <label for="tarea-fecha-vencimiento" class="block text-sm font-medium text-gray-700">Fecha de
                                Vencimiento</label>
                            <input type="date" id="tarea-fecha-vencimiento" name="fecha_vencimiento"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        <div>
                            <label for="tarea-usuario" class="block text-sm font-medium text-gray-700">Asignar a</label>
                            <select id="tarea-usuario" name="usuario_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <!-- Opciones se llenarán dinámicamente -->
                            </select>
                        </div>
                        <div>
                            <label for="tarea-file-size-limit" class="block text-sm font-medium text-gray-700">Límite de
                                tamaño de archivo (KB)</label>
                            <input type="number" id="tarea-file-size-limit" name="file_size_limit"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="1"
                                max="100000">
                        </div>
                    </div>
                    <div>
                        <label for="tarea-descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea id="tarea-descripcion" name="descripcion" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm resize-none"></textarea>
                    </div>
                    <div id="tarea-archivos-container">
                        <label for="tarea-archivos" class="block text-sm font-medium text-gray-700">Archivos</label>
                        <input type="file" id="tarea-archivos" name="archivos[]" multiple class="mt-1 block w-full">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="modal-tarea-cancelar"
                            class="px-4 py-2 border rounded-md">Cancelar</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para Gestión de Archivos -->
        <div id="modal-archivos" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-2/3 max-w-2xl">
                <h2 id="modal-archivos-title" class="text-xl font-bold mb-4">Gestión de Archivos</h2>
                <div id="lista-archivos" class="mb-4">
                    <!-- Los archivos se cargarán aquí dinámicamente -->
                </div>
                <form id="form-subir-archivo" class="mb-4" enctype="multipart/form-data">
                    <input type="file" id="archivo-input" name="archivo" class="mb-2">
                    <input type="hidden" id="tarea-id-archivo">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Subir Archivo</button>
                </form>
                <button id="modal-archivos-cerrar" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md">Cerrar</button>
            </div>
        </div>

        <!-- Modal para Feedback -->
        <div id="modal-feedback" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-2/3 max-w-2xl">
                <h2 class="text-xl font-bold mb-4">Feedback</h2>
                <div id="lista-feedback" class="mb-4 max-h-64 overflow-y-auto">
                    <!-- El feedback se cargará aquí dinámicamente -->
                </div>
                <form id="form-agregar-feedback" class="mb-4" enctype="multipart/form-data">
                    <textarea name="comentario" id="feedback-comentario" placeholder="Escribe tu feedback aquí" class="w-full mb-2"
                        required></textarea>
                    <input type="file" name="archivo" class="mb-2">
                    <input type="hidden" id="tarea-id-feedback">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Agregar Feedback</button>
                </form>
                <button id="modal-feedback-cerrar" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md">Cerrar</button>
            </div>
        </div>

        <!-- Modal para Notificaciones -->
        <div id="modal-notificaciones"
            class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-2/3 max-w-2xl">
                <h2 class="text-xl font-bold mb-4">Notificaciones</h2>
                <div id="lista-notificaciones" class="mb-4">
                    <!-- Las notificaciones se cargarán aquí dinámicamente -->
                </div>
                <button id="modal-notificaciones-cerrar"
                    class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md">Cerrar</button>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const selectProyecto = document.getElementById('select-proyecto');
            const kanbanTableros = document.getElementById('kanban-tableros');
            const btnNuevoTablero = document.getElementById('btn-nuevo-tablero');
            const modalTablero = document.getElementById('modal-tablero');
            const formTablero = document.getElementById('form-tablero');
            const modalCancelar = document.getElementById('modal-cancelar');
			const modalTarea = document.getElementById('modal-tarea');
			const formTarea = document.getElementById('form-tarea');
			const modalTareaCancelar = document.getElementById('modal-tarea-cancelar');
			const modalArchivos = document.getElementById('modal-archivos');
			const listaArchivos = document.getElementById('lista-archivos');
			const formSubirArchivo = document.getElementById('form-subir-archivo');
			const modalArchivosCerrar = document.getElementById('modal-archivos-cerrar');
			const modalFeedback = document.getElementById('modal-feedback');
			const listaFeedback = document.getElementById('lista-feedback');
			const formAgregarFeedback = document.getElementById('form-agregar-feedback');
			const modalFeedbackCerrar = document.getElementById('modal-feedback-cerrar');

			let proyectoSeleccionadoId = null;


            function mostrarModal() {
                modalTablero.classList.remove('hidden');
            }

            function ocultarModal() {
                modalTablero.classList.add('hidden');
                formTablero.reset();
                document.getElementById('tablero-id').value = '';
                document.getElementById('modal-tablero-title').textContent = 'Crear Tablero';
            }

            btnNuevoTablero.addEventListener('click', mostrarModal);
            modalCancelar.addEventListener('click', ocultarModal);

            formTablero.addEventListener('submit', async function(e) {
                e.preventDefault();
                const tableroId = document.getElementById('tablero-id').value;
                const nombre = document.getElementById('tablero-nombre').value;
                const proyectoId = proyectoSeleccionadoId; //selectProyecto.value;

                try {
                    const url = tableroId ?
                        `/kanban/${proyectoId}/editar-tablero/${tableroId}` :
                        `/kanban/${proyectoId}/crear-tablero`;

                    const response = await fetch(url, {
                        method: tableroId ? 'PUT' : 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            nombre
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        if (!tableroId) {
                            agregarTableroDOM(data.tablero);
                        } else {
                            actualizarTableroDOM(data.tablero);
                        }
                        ocultarModal();
                    } else {
                        alert(data.message || 'Error al guardar el tablero');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud');
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
                template.querySelector('.tablero-nombre').textContent = tablero.nombre;

                const btnEditar = template.querySelector('.btn-editar-tablero');
                const btnEliminar = template.querySelector('.btn-eliminar-tablero');
                const btnNuevaColumna = template.querySelector('.btn-nueva-columna');
                const columnasContainer = template.querySelector('.kanban-columnas');

                btnEditar.addEventListener('click', () => editarTablero(tablero));
                btnEliminar.addEventListener('click', () => eliminarTablero(tablero.id));
                btnNuevaColumna.addEventListener('click', () => crearColumna(tablero.id, columnasContainer));

                if (tablero.columns && Array.isArray(tablero.columns)) {
                    tablero.columns.forEach(columna => {
                        agregarColumnaDOM(columna, columnasContainer);
                    });
                }

                kanbanTableros.appendChild(tableroElement);
            }

            function actualizarTableroDOM(tablero) {
                const tableroElement = document.querySelector(`[data-tablero-id="${tablero.id}"]`);
                if (tableroElement) {
                    tableroElement.querySelector('.tablero-nombre').textContent = tablero.nombre;
                }
            }

            async function editarTablero(tablero) {
                document.getElementById('tablero-id').value = tablero.id;
                document.getElementById('tablero-nombre').value = tablero.nombre;
                document.getElementById('modal-tablero-title').textContent = 'Editar Tablero';
                mostrarModal();
            }

            async function eliminarTablero(tableroId) {
                if (confirm('¿Estás seguro de eliminar este tablero?')) {
                    try {
                        const response = await fetch(
                            `/kanban/${proyectoSeleccionadoId}/eliminar-tablero/${tableroId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                        const data = await response.json();
                        if (data.success) {
                            const tableroElement = document.querySelector(`[data-tablero-id="${tableroId}"]`);
                            if (tableroElement) {
                                tableroElement.remove();
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al eliminar el tablero');
                    }
                }
            }

            async function crearColumna(tableroId, columnasContainer) {
                const nombre = prompt('Nombre de la nueva columna:');
                if (!nombre) return;

                try {
                    const response = await fetch(`/kanban/${tableroId}/crear-columna`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            nombre
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        agregarColumnaDOM(data.columna, columnasContainer);
                    }
                } catch (error) {
                    console.error('Error al crear columna:', error);
                }
            }

            function agregarColumnaDOM(columna, columnasContainer) {
                const template = document.getElementById('template-columna').content.cloneNode(true);
                const columnaElement = template.querySelector('[data-columna-id]');

                columnaElement.setAttribute('data-columna-id', columna.id);
                template.querySelector('.columna-nombre').textContent = columna.nombre;

                const btnEliminarColumna = template.querySelector('.btn-eliminar-columna');
                const btnNuevaTarea = template.querySelector('.btn-nueva-tarea');
                const tareasContainer = template.querySelector('.kanban-tareas');

                btnEliminarColumna.addEventListener('click', () => eliminarColumna(columna.id, columnaElement));
                btnNuevaTarea.addEventListener('click', () => crearTarea(columna.id, tareasContainer));

                if (columna.tasks && Array.isArray(columna.tasks)) {
                    columna.tasks.forEach(tarea => {
                        agregarTareaDOM(tarea, tareasContainer);
                    });
                }

                columnasContainer.appendChild(columnaElement);
            }

            async function eliminarColumna(columnaId, columnaElement) {
                if (confirm('¿Estás seguro de eliminar esta columna?')) {
                    try {
                        const response = await fetch(`/kanban/eliminar-columna/${columnaId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            columnaElement.remove();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al eliminar la columna');
                    }
                }
            }

            async function crearTarea(columnaId, tareasContainer) {
				document.getElementById('columna-id').value = columnaId;
				document.getElementById('tarea-id').value = '';
				document.getElementById('modal-tarea-title').textContent = 'Crear Tarea';

				// Resetear campos del formulario
				document.getElementById('tarea-nombre').value = '';
				document.getElementById('tarea-descripcion').value = '';
				document.getElementById('tarea-estado').value = 'pendiente';
				document.getElementById('tarea-prioridad').value = 'media';
				document.getElementById('tarea-fecha-vencimiento').value = '';
				document.getElementById('tarea-usuario').value = '';
				document.getElementById('tarea-file-size-limit').value = '';

				// Mostrar input de archivos para nuevas tareas
				document.getElementById('tarea-archivos-container').style.display = 'block';
				cargarUsuariosProyecto(proyectoSeleccionadoId);
				modalTarea.classList.remove('hidden');
            }

			async function cargarUsuariosProyecto(proyectoId) {
				try {
					const response = await fetch(`/proyectos/${proyectoId}/usuarios`);
					const data = await response.json();
					if (data.success) {
						const selectUsuario = document.getElementById('tarea-usuario');
						selectUsuario.innerHTML = ''; // Limpiar opciones anteriores
						data.usuarios.forEach(usuario => {
							const option = document.createElement('option');
							option.value = usuario.id;
							option.textContent = usuario.name;
							selectUsuario.appendChild(option);
						});
					} else {
						alert(data.message || 'Error al cargar los usuarios del proyecto');
					}
				} catch (error) {
					console.error('Error al cargar usuarios:', error);
					alert('Error al cargar los usuarios');
				}
			}

            function agregarTareaDOM(tarea, tareasContainer) {
                const template = document.getElementById('template-tarea').content.cloneNode(true);
                const tareaElement = template.querySelector('[data-tarea-id]');

                tareaElement.setAttribute('data-tarea-id', tarea.id);
                template.querySelector('.tarea-nombre').textContent = tarea.tarea.nombre;
                template.querySelector('.tarea-descripcion').textContent = tarea.tarea.descripcion || '';
                template.querySelector('.tarea-estado').textContent = tarea.tarea.estado;
                template.querySelector('.tarea-prioridad').textContent = tarea.tarea.prioridad;
                template.querySelector('.tarea-fecha').textContent = tarea.tarea.fecha_vencimiento;

                const usuario = tarea.tarea.usuarios && tarea.tarea.usuarios[0];
                template.querySelector('.tarea-usuario').textContent = usuario ? usuario.name : 'Sin asignar';

				const btnEditarTarea = template.querySelector('.btn-editar-tarea');
                const btnEliminarTarea = template.querySelector('.btn-eliminar-tarea');
                const btnGestionarArchivos = template.querySelector('.btn-gestionar-archivos');
                const btnGestionarFeedback = template.querySelector('.btn-gestionar-feedback');

				btnEditarTarea.addEventListener('click', () => editarTarea(tarea.id));
                btnGestionarArchivos.addEventListener('click', () => gestionarArchivos(tarea.tarea.id));
                btnGestionarFeedback.addEventListener('click', () => gestionarFeedback(tarea.tarea.id));
                btnEliminarTarea.addEventListener('click', () => eliminarTarea(tarea.id, tareaElement));


                tareasContainer.appendChild(tareaElement);
            }

			async function editarTarea(tareaId) {
				try {
					const response = await fetch(`/tareas/${tareaId}`);
					const data = await response.json();

					if (data.success) {
						const tarea = data.tarea;

						document.getElementById('tarea-id').value = tarea.id;
						document.getElementById('columna-id').value = tarea.kanban_column_id;
						document.getElementById('modal-tarea-title').textContent = 'Editar Tarea';

						document.getElementById('tarea-nombre').value = tarea.nombre;
						document.getElementById('tarea-descripcion').value = tarea.descripcion || '';
						document.getElementById('tarea-estado').value = tarea.estado;
						document.getElementById('tarea-prioridad').value = tarea.prioridad;
						document.getElementById('tarea-fecha-vencimiento').value = tarea.fecha_vencimiento;
						document.getElementById('tarea-file-size-limit').value = tarea.file_size_limit ? tarea.file_size_limit / 1024 : '';

						// Ocultar input de archivos al editar tareas
						document.getElementById('tarea-archivos-container').style.display = 'none';

						// Cargar usuarios y seleccionar el asignado
						cargarUsuariosProyecto(proyectoSeleccionadoId).then(() => {
							document.getElementById('tarea-usuario').value = tarea.usuarios[0].id;
						});

						modalTarea.classList.remove('hidden');
					} else {
						alert(data.message || 'Error al obtener los detalles de la tarea');
					}
				} catch (error) {
					console.error('Error al cargar la tarea:', error);
					alert('Error al cargar la tarea');
				}
			}

            async function eliminarTarea(tareaId, tareaElement) {
                if (confirm('¿Estás seguro de eliminar esta tarea?')) {
                    try {
                        const response = await fetch(`/kanban/eliminar-tarea/${tareaId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            tareaElement.remove();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al eliminar la tarea');
                    }
                }
            }

            selectProyecto.addEventListener('change', function() {
				proyectoSeleccionadoId = this.value;
                if (proyectoSeleccionadoId) {
                    cargarTableros(proyectoSeleccionadoId);
                } else {
                    kanbanTableros.innerHTML = '';
                }
            });

			formTarea.addEventListener('submit', async function(e) {
				e.preventDefault();
				const tareaId = document.getElementById('tarea-id').value;
				const columnaId = document.getElementById('columna-id').value;
				const formData = new FormData(this);

				try {
					const url = tareaId ?
						`/kanban/tarea/${tareaId}/editar` :
						`/kanban/${columnaId}/crear-tarea`;

					const response = await fetch(url, {
						method: tareaId ? 'PUT' : 'POST',
						body: formData,
						headers: {
							'X-CSRF-TOKEN': csrfToken
						}
					});

					const data = await response.json();
					if (data.success) {
						if (!tareaId) {
							const tareasContainer = document.querySelector(
								`[data-columna-id="${columnaId}"] .kanban-tareas`);
							agregarTareaDOM(data.tarea, tareasContainer);
						} else {
							//TODO: actualizar tarea en el dom
							const tareaElement = document.querySelector(`[data-tarea-id="${tareaId}"]`);
							tareaElement.remove()
							const tareasContainer = document.querySelector(
								`[data-columna-id="${columnaId}"] .kanban-tareas`);
							agregarTareaDOM(data.tarea, tareasContainer);
						}
						modalTarea.classList.add('hidden');
						this.reset();
					} else {
						alert(data.message || 'Error al guardar la tarea');
					}
				} catch (error) {
					console.error('Error:', error);
					alert('Error al procesar la solicitud');
				}
			});

			modalTareaCancelar.addEventListener('click', function() {
				modalTarea.classList.add('hidden');
				formTarea.reset();
			});


            async function gestionarArchivos(tareaId) {
				document.getElementById('tarea-id-archivo').value = tareaId;
                const modalArchivos = document.getElementById('modal-archivos');
                const listaArchivos = document.getElementById('lista-archivos');
                const formSubirArchivo = document.getElementById('form-subir-archivo');
				modalArchivos.classList.remove('hidden');
                await cargarArchivos(tareaId);


            }

            async function cargarArchivos(tareaId) {
                const listaArchivos = document.getElementById('lista-archivos');
                try {
                    const response = await fetch(`/kanban/tarea/${tareaId}/archivos`);
                    const data = await response.json();
                    if (data.success) {
                        listaArchivos.innerHTML = '';
						if (data.archivos.length === 0) {
							listaArchivos.innerHTML = '<p>No hay archivos disponibles.</p>';
						} else {
							data.archivos.forEach(archivo => {
								const fechaSubida = new Date(archivo.created_at).toLocaleDateString();
								const archivoElement = document.createElement('div');
								archivoElement.className = 'flex justify-between items-center mb-2';
								archivoElement.innerHTML = `
									<span>${archivo.nombre}</span>
									<div>
										<a href="/kanban/archivo/${archivo.id}/descargar" class="text-blue-500 hover:text-blue-600 mr-2">Descargar</a>
										<span>Fecha: ${fechaSubida}</span>
									</div>
								`;
								listaArchivos.appendChild(archivoElement);
							});
						}

                    }
                } catch (error) {
                    console.error('Error al cargar archivos:', error);
                }
            }

			formSubirArchivo.addEventListener('submit', async (e) => {
				e.preventDefault();
				const tareaId = document.getElementById('tarea-id-archivo').value;
				const formData = new FormData(formSubirArchivo);
				try {
					const response = await fetch(`/kanban/tarea/${tareaId}/subir-archivo`, {
						method: 'POST',
						body: formData,
						headers: {
							'X-CSRF-TOKEN': csrfToken
						}
					});
					const data = await response.json();
					if (data.success) {
						await cargarArchivos(tareaId);
						formSubirArchivo.reset();
					} else {
						alert(data.mensaje || 'Error al subir el archivo');
					}
				} catch (error) {
					console.error('Error al subir archivo:', error);
					alert('Error al subir el archivo');
				}
			});

			modalArchivosCerrar.addEventListener('click', function() {
				modalArchivos.classList.add('hidden');
			});

            async function cargarVersionesArchivo(tareaId) {
                const versionesArchivo = document.getElementById('versiones-archivo');
                try {
                    const response = await fetch(`/kanban/tarea/${tareaId}/versiones-archivo`);
                    const data = await response.json();
                    if (data.success) {
                        versionesArchivo.innerHTML = '<h3 class="font-bold mb-2">Versiones del archivo:</h3>';
                        data.versiones.forEach(version => {
                            const versionElement = document.createElement('div');
                            versionElement.className = 'mb-2';
                            versionElement.innerHTML = `
                        <p>Versión: ${new Date(version.created_at).toLocaleString()}</p>
                        <p>Subido por: ${version.usuario.name}</p>
                        <p>Comentario: ${version.comentario || 'Sin comentario'}</p>
                        <a href="/storage/${version.ruta}" target="_blank" class="text-blue-500 hover:text-blue-600">Descargar</a>
                        ${!version.es_final ? `<button class="btn-marcar-final ml-2 text-green-500 hover:text-green-600" data-version-id="${version.id}">Marcar como final</button>` : '<span class="ml-2 text-green-500">Versión final</span>'}
                    `;
                            versionesArchivo.appendChild(versionElement);
                        });
                    }
                } catch (error) {
                    console.error('Error al cargar versiones de archivo:', error);
                }
            }

            async function gestionarFeedback(tareaId) {
				document.getElementById('tarea-id-feedback').value = tareaId;
                const modalFeedback = document.getElementById('modal-feedback');
                const listaFeedback = document.getElementById('lista-feedback');
                const formAgregarFeedback = document.getElementById('form-agregar-feedback');
				modalFeedback.classList.remove('hidden');
                await cargarFeedback(tareaId);
            }

            async function cargarFeedback(tareaId) {
                const listaFeedback = document.getElementById('lista-feedback');
                try {
                    const response = await fetch(`/kanban/tarea/${tareaId}/feedback`);
                    const data = await response.json();
                    if (data.success) {
                        listaFeedback.innerHTML = '';
						if (data.feedback.length === 0) {
							listaFeedback.innerHTML = '<p>No hay feedback disponible.</p>';
						} else {
							data.feedback.forEach(item => {
								const feedbackElement = document.createElement('div');
								feedbackElement.className = 'mb-4 p-4 bg-gray-100 rounded';
								feedbackElement.innerHTML = `
									<p><strong>${item.usuario.name}</strong> - ${new Date(item.created_at).toLocaleString()}</p>
									<p>${item.comentario}</p>
									${item.archivo_adjunto ? `<a href="/storage/${item.archivo_adjunto}" target="_blank" class="text-blue-500 hover:text-blue-600">Ver archivo adjunto</a>` : ''}
								`;
								listaFeedback.appendChild(feedbackElement);
							});
						}

                    }
                } catch (error) {
                    console.error('Error al cargar feedback:', error);
                }
            }

			formAgregarFeedback.addEventListener('submit', async (e) => {
				e.preventDefault();
				const tareaId = document.getElementById('tarea-id-feedback').value;
				const formData = new FormData(formAgregarFeedback);
				try {
					const response = await fetch(`/kanban/tarea/${tareaId}/agregar-feedback`, {
						method: 'POST',
						body: formData,
						headers: {
							'X-CSRF-TOKEN': csrfToken
						}
					});
					const data = await response.json();
					if (data.success) {
						await cargarFeedback(tareaId);
						document.getElementById('feedback-comentario').value = '';
					} else {
						alert(data.mensaje || 'Error al agregar feedback');
					}
				} catch (error) {
					console.error('Error al agregar feedback:', error);
					alert('Error al agregar feedback');
				}
			});

			modalFeedbackCerrar.addEventListener('click', function() {
				modalFeedback.classList.add('hidden');
			});

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-gestionar-archivos')) {
                    const tareaId = e.target.closest('[data-tarea-id]').dataset.tareaId;
                    gestionarArchivos(tareaId);
                } else if (e.target.classList.contains('btn-gestionar-feedback')) {
                    const tareaId = e.target.closest('[data-tarea-id]').dataset.tareaId;
                    gestionarFeedback(tareaId);
                }
            });


            // Función para cargar y mostrar notificaciones
            async function cargarNotificaciones() {
                try {
                    const response = await fetch('/kanban/notificaciones');
                    const data = await response.json();
                    if (data.success) {
                        const listaNotificaciones = document.getElementById('lista-notificaciones');
                        listaNotificaciones.innerHTML = '';
                        data.notificaciones.forEach(notificacion => {
                            const notificacionElement = document.createElement('div');
                            notificacionElement.className = 'mb-4 p-4 bg-yellow-100 rounded';
                            notificacionElement.innerHTML = `
                        <p><strong>Tarea:</strong> ${notificacion.tarea_nombre}</p>
                        <p><strong>Mensaje:</strong> ${notificacion.mensaje}</p>
                        <p><strong>Fecha de eliminación:</strong> ${new Date(notificacion.fecha_eliminacion).toLocaleString()}</p>
                    `;
                            listaNotificaciones.appendChild(notificacionElement);
                        });
                    }
                } catch (error) {
                    console.error('Error al cargar notificaciones:', error);
                }
            }

            // Botón para mostrar notificaciones (puedes agregarlo en tu barra de navegación)
            const btnMostrarNotificaciones = document.createElement('button');
            btnMostrarNotificaciones.textContent = 'Notificaciones';
            btnMostrarNotificaciones.className = 'bg-blue-500 text-white px-4 py-2 rounded-md';
            btnMostrarNotificaciones.addEventListener('click', () => {
                cargarNotificaciones();
                document.getElementById('modal-notificaciones').classList.remove('hidden');
            });
            document.querySelector('.container').prepend(btnMostrarNotificaciones);

            document.getElementById('modal-notificaciones-cerrar').addEventListener('click', function() {
                document.getElementById('modal-notificaciones').classList.add('hidden');
            });

            // Cargar notificaciones periódicamente (cada 5 minutos)
            setInterval(cargarNotificaciones, 300000);
   selectProyecto.addEventListener('change', function() {
        proyectoSeleccionadoId = this.value;
        if (proyectoSeleccionadoId) {
            cargarTableros(proyectoSeleccionadoId);
        } else {
            kanbanTableros.innerHTML = '';
        }
    });
        });
    </script>
@endsection
--}}
