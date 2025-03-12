<template>
    <div @click="handleClick" class="w-64 h-80 bg-white rounded-lg shadow-md p-5 hover:shadow-lg transition-shadow duration-300 relative border border-gray-200">
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
         <!-- Indicador de inscripción -->
        <div v-if="inscrito" class="absolute bottom-4 left-2 px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
            Ya estás inscrito
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
        <div v-if="isAdmin" class="absolute bottom-4 right-4 flex space-x-3">
            <!-- Icono de editar -->
            <div class="relative group">
                <button 
                    @click.stop="handleEditar" 
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
                    @click.stop="handleEliminar" 
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
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  concurso: {
    type: Object,
    required: true
  },
  titulo: String,
  fechaInicio: String,
  fechaApertura: String,
  fechaFinalizacion: String,
  inscrito: Boolean,
  isAdmin: Boolean
});

const emit = defineEmits(['click', 'editar', 'eliminar']);

const handleClick = () => {
  emit('click', props.concurso);
};

const handleEditar = () => {
  emit('editar', props.concurso);
};

const handleEliminar = () => {
  emit('eliminar', props.concurso);
};
</script>

<style scoped>
.card {
  border: 1px solid #ccc;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 16px;
}
</style>
