@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-1 lg:px-1">
        <h2 class="font-semibold text-2xl text-indigo-600 dark:text-indigo-400 leading-tight mt-1">
            {{ __('Resumen de tu Dashboard') }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Resumen de Proyectos -->
            <div class="gap-4">
                <div class="bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 my-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <span class="material-icons text-indigo-600 text-4xl">business</span>
                        <h3 class="text-xl font-semibold text-white">Resumen de Proyectos</h3>
                    </div>
                    <ul class="space-y-3">
                        <li class="text-lg font-medium text-white"><strong>Total de proyectos:</strong>
                            {{ $totalProyectos }}</li>
                    </ul>
                </div>
            </div>

            <!-- Resumen de Tareas -->
            <div class="gap-4">
                <div class="bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 my-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <span class="material-icons text-green-600 text-4xl">check_circle</span>
                        <h3 class="text-xl font-semibold text-white">Resumen de Tareas</h3>
                    </div>
                    <ul class="space-y-3">
                        <li class="text-lg font-medium text-white"><strong>Total de tareas:</strong>
                            {{ $totalTareas }}</li>
                    </ul>
                </div>
            </div>

            <!-- Recordatorios de proyectos próximos a vencer -->
            <div class="gap-4">
                <div class="bg-gray-800 p-6 rounded-lg shadow-md my-6">
                    <h3 class="text-xl font-semibold text-white">Proyectos Próximos a Vencer</h3>
                    <ul class="space-y-2 mt-4">
                        @foreach ($proyectosProximos as $proyecto)
                            <li class="text-lg font-medium text-white">
                                {{ $proyecto->nombre }} - Vence el
                                {{ \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d-m-Y') }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>


            <!-- Sección de Proyectos Atrasados -->
            <div class="gap-4">
                <div class="bg-gray-800 p-6 rounded-lg shadow-md my-6">
                    <h3 class="text-xl font-semibold text-white">Proyectos Atrasados</h3>
                    @if ($proyectosAtrasados->count() > 0)
                        <ul class="text-xl font-semibold text-white">
                            @foreach ($proyectosAtrasados as $proyecto)
                                <li>
                                    {{ $proyecto->nombre }} - Vencido desde
                                    {{ \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d-m-Y') }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-xl font-regular text-white">No hay proyectos atrasados.</p>
                    @endif
                </div>
            </div>

            <!-- Gráfico: Tareas vs Proyectos -->
            <div class="gap-4">
                <div class="bg-gray-800 p-6 rounded-lg shadow-md my-6">
                    <h3 class="text-xl font-semibold text-white">Tareas vs Proyectos</h3>
                    <div class="w-full h-64">
                        <canvas id="tasksVsProjectsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Progreso de Tareas -->
            <div class="gap-4">
                <div class="bg-gray-800 p-6 rounded-lg shadow-md my-6">
                    <h3 class="text-xl font-semibold text-white">Progreso de Tareas</h3>
                    <div class="w-full h-64">
                        <canvas id="tasksProgressChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Calendario -->
            <div class="gap-4">
                <div class="bg-gray-800 p-6 rounded-lg shadow-md my-6">
                    <h3 class="text-xl font-semibold text-white">Tu Calendario</h3>
                    <div id="calendar" class="w-full h-64 bg-purple-600"></div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="flex justify-center mb-6">
                    <a href="http://34.72.118.58:3000"
                        class="inline-flex items-center px-6 py-3 bg-white text-gray-800 font-semibold border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 hover:shadow-md transition-all duration-300">
                        <svg class="w-5 h-5 mr-2 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a4 4 0 00-8 0v2M5 12h14M12 19v-7" />
                        </svg>
                        Crear sala con Daily
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- (Tu contenido actual de Dashboard se mantiene aquí) -->
                </div>
            </div>
        </div>
    </div>

    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css">

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>

    <!-- Cargar la localización en español para FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/es.js"></script>

    <!-- Script para Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Script para inicializar FullCalendar y Chart.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Datos deserializados desde Laravel (pasados como JSON)
            var totalProyectos = @json($totalProyectos); // Total de proyectos
            var totalTareas = @json($totalTareas); // Total de tareas
            var proyectos = @json($proyectos); // Los proyectos recibidos desde el controlador

            // Inicialización del gráfico de tareas vs proyectos
            var ctx = document.getElementById('tasksVsProjectsChart').getContext('2d');
            var tasksVsProjectsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Proyectos', 'Tareas'],
                    datasets: [{
                        label: 'Total',
                        data: [totalProyectos, totalTareas],
                        backgroundColor: ['#4CAF50', '#FF9800'],
                        borderColor: ['#388E3C', '#FF5722'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });



            // Inicialización del calendario
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'es', // Establece el idioma a español
                initialView: 'dayGridMonth', // Configura la vista inicial como mes
                headerToolbar: { // Personaliza la barra de herramientas
                    left: 'prev', // Flecha para el mes anterior
                    center: 'title', // Eliminar el título del mes
                    right: 'next' // Flecha para el siguiente mes
                },
                navLinks: false, // Habilitar enlaces de navegación (para cambiar de día/mes)
                dayCellClassNames: 'bg-blue-200', // Color uniforme para los días

                // Marcar los días con proyectos en rojo
                events: proyectos.map(function(proyecto) {
                    return {
                        title: proyecto.nombre, // Título del evento (nombre del proyecto)
                        start: proyecto.fecha_fin, // Fecha de fin del proyecto
                        backgroundColor: '#FF0000', // Color de fondo del evento (rojo)
                        borderColor: '#B30000' // Color del borde (rojo oscuro)
                    };
                }),



                dateClick: function(info) {
                    var selectedDate = info.dateStr; // Fecha seleccionada en formato yyyy-mm-dd

                    // Filtrar los proyectos que terminan exactamente en esa fecha
                    var proyectosDelDia = proyectos.filter(function(proyecto) {
                        return proyecto.fecha_fin === selectedDate;
                    });

                    // Crear y mostrar el modal si hay proyectos en esa fecha
                    if (proyectosDelDia.length > 0) {
                        var proyectosList = proyectosDelDia.map(function(proyecto) {
                            return `<li>${proyecto.nombre}</li>`;
                        }).join('');

                        var modalContent = `
                    <h4>Proyectos del día: ${selectedDate}</h4>
                    <ul>${proyectosList}</ul>
                `;

                        var modal = document.createElement('div');
                        modal.classList.add('modal');
                        modal.style.position = 'fixed';
                        modal.style.top = '50%';
                        modal.style.left = '50%';
                        modal.style.transform = 'translate(-50%, -50%)';
                        modal.style.backgroundColor = 'green';
                        modal.style.padding = '10px';
                        modal.style.width = '300px'; // Ancho más pequeño
                        modal.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.1)';
                        modal.style.borderRadius = '8px';
                        modal.innerHTML = modalContent;

                        document.body.appendChild(modal);

                        // Eliminar el modal después de unos segundos (3 segundos en este caso)
                        setTimeout(function() {
                            modal.remove();
                        }, 2000); // 3000 milisegundos = 3 segundos
                    }
                }
            });

            calendar.render(); // Renderiza el calendario




            // Gráfico de Progreso de Tareas (Completadas vs Pendientes)
            // Datos deserializados desde Laravel (pasados como JSON)
            var tareasCompletadas = @json($tareasCompletadas); // Tareas completadas
            var tareasPendientes = @json($tareasPendientes); // Tareas pendientes

            // Inicialización del gráfico de Progreso de Tareas (Completadas vs Pendientes)
            var ctx3 = document.getElementById('tasksProgressChart').getContext('2d');
            var tasksProgressChart = new Chart(ctx3, {
                type: 'doughnut', // Tipo de gráfico: dona (doughnut)
                data: {
                    labels: ['Completadas', 'Pendientes'],
                    datasets: [{
                        label: 'Progreso de Tareas',
                        data: [tareasCompletadas,
                            tareasPendientes
                        ], // Tareas completadas vs pendientes
                        backgroundColor: ['#4CAF50', '#FF5722'],
                        borderColor: ['#388E3C', '#D32F2F'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw;
                                }
                            }
                        },
                        datalabels: {
                            formatter: function(value, ctx) {
                                var total = ctx.dataset.data.reduce(function(acc, val) {
                                    return acc + val;
                                }, 0);
                                var percentage = (value / total * 100).toFixed(2) + '%';
                                return percentage; // Muestra el porcentaje
                            },
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 16
                            }
                        }
                    }
                }
            });

        });
    </script>
@endsection
