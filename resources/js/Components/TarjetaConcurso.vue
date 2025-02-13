<template>
    <div class="w-64 h-80 bg-white rounded-lg shadow-md p-5 hover:shadow-lg transition-shadow duration-300 relative border border-gray-200">
        <!-- Indicador de estado -->
        <div 
            class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-semibold flex items-center space-x-1"
            :class="{
                'bg-green-100 text-green-800': estadoLocal === 'abierto',
                'bg-red-100 text-red-800': estadoLocal === 'cerrado'
            }"
        >
            <!-- Ícono de estado -->
            <svg 
                v-if="estadoLocal === 'abierto'" 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-4 w-4 text-green-600" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg 
                v-else 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-4 w-4 text-red-600" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <!-- Texto del estado -->
            <span>{{ estadoLocal === 'abierto' ? 'Abierto' : 'Cerrado' }}</span>
        </div>

        <!-- Contenido de la tarjeta -->
        <h3 class="text-xl font-semibold text-[#611232] mb-4">{{ titulo }}</h3>

        <!-- Fecha de inicio con ícono -->
        <div class="flex items-center text-gray-600 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-sm">Fecha de inicio: {{ fechaInicio }}</span>
        </div>

        <!-- Fecha de apertura con ícono -->
        <div class="flex items-center text-gray-600 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">Fecha de apertura: {{ fechaApertura }}</span>
        </div>

        <!-- Fecha de finalización con ícono -->
        <div class="flex items-center text-gray-600 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">Fecha de finalización: {{ fechaFinalizacion }}</span>
        </div>

        <!-- Iconos de acciones -->
        <div class="absolute bottom-4 right-4 flex space-x-3">
            <!-- Icono de editar -->
            <div class="relative group">
                <button 
                    @click="editarConcurso" 
                    class="p-2 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200 border border-gray-300"
                    aria-label="Editar concurso"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 group-hover:text-blue-600 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
                <!-- Tooltip para editar -->
                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    Editar
                </span>
            </div>

            <!-- Icono de eliminar -->
            <div class="relative group">
                <button 
                    @click="eliminarConcurso" 
                    class="p-2 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200 border border-gray-300"
                    aria-label="Eliminar concurso"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 group-hover:text-red-600 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                <!-- Tooltip para eliminar -->
                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    Eliminar
                </span>
            </div>

            <!-- Menú desplegable mejorado -->
            <div class="relative group">
                <!-- Botón de tres puntos -->
                <button 
                    @mouseenter="abrirMenu" 
                    @mouseleave="cerrarMenu" 
                    class="p-2 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200 border border-gray-300"
                    aria-label="Cambiar estado"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                    </svg>
                </button>

                <!-- Menú desplegable -->
                <div 
                    v-if="menuAbierto" 
                    @mouseenter="abrirMenu" 
                    @mouseleave="cerrarMenu" 
                    class="absolute right-0 -top-24 flex flex-col space-y-2 bg-white p-2 rounded-lg shadow-md transition-opacity duration-200 border border-gray-300"
                >
                    <!-- Opción Activar -->
                    <button 
                        @click="cambiarEstado('abierto')" 
                        class="p-2 bg-green-100 rounded-full shadow-md hover:bg-green-200 transition-colors duration-200 flex items-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Activar</span>
                    </button>

                    <!-- Opción Desactivar -->
                    <button 
                        @click="cambiarEstado('cerrado')" 
                        class="p-2 bg-red-100 rounded-full shadow-md hover:bg-red-200 transition-colors duration-200 flex items-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Desactivar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

// Props
const props = defineProps({
    id: {
        type: Number,
        required: true,
    },
    titulo: {
        type: String,
        required: true,
    },
    fechaInicio: {
        type: String,
        required: true,
    },
    fechaApertura: {
        type: String,
        required: true,
    },
    fechaFinalizacion: {
        type: String,
        required: true,
    },
    status: {
        type: String,
        required: true,
    },
});

// Estado del menú desplegable
const menuAbierto = ref(false);

// Estado local para el status
const estadoLocal = ref(props.status);

// Watch para reaccionar a cambios en la prop status
watch(() => props.status, (nuevoStatus) => {
    estadoLocal.value = nuevoStatus;
});

// Función para abrir el menú
const abrirMenu = () => {
    menuAbierto.value = true;
};

// Función para cerrar el menú
const cerrarMenu = () => {
    menuAbierto.value = false;
};

// Función para cambiar el estado
const cambiarEstado = (nuevoEstado) => {
    if (confirm(`¿Estás seguro de que deseas ${nuevoEstado === 'abierto' ? 'activar' : 'desactivar'} este concurso?`)) {
        router.post(`/concursos/${props.id}/cambiar-estado`, { status: nuevoEstado }, {
            onSuccess: () => {
                estadoLocal.value = nuevoEstado; // Actualizar el estado local
                menuAbierto.value = false; // Cerrar el menú después de la acción
            }
        });
    }
};

// Función para eliminar el concurso
const eliminarConcurso = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este concurso?')) {
        router.delete(`/concursos/${props.id}`);
    }
};

// Función para editar el concurso
const editarConcurso = () => {
    if (!props.id) {
        console.error('ID del concurso no definido.');
        return;
    }
    console.log('Redirigiendo a la página de edición...');
    router.get(`/concursos/${props.id}/editar`);
};
</script>