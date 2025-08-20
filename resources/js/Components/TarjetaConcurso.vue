<template>
    <div @click="handleClick" class="w-64 h-80 bg-white rounded-lg shadow-md p-5 hover:shadow-lg transition-shadow duration-300 relative border border-gray-200">
        <!-- Indicador de estado -->
        <div 
            class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-semibold flex items-center space-x-1"
            :class="{
                'bg-green-100 text-green-800': concurso.status === 'abierto',
                'bg-red-100 text-red-800': concurso.status === 'cerrado',
                'bg-gray-200 text-gray-700': concurso.status === 'finalizado'
            }"
        >
            <svg 
                v-if="concurso.status === 'abierto'" 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-4 w-4 text-green-600" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg 
                v-else-if="concurso.status === 'cerrado'" 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-4 w-4 text-red-600" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <svg
                v-else-if="concurso.status === 'finalizado'"
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 text-gray-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>
                {{
                    concurso.status === 'abierto'
                        ? 'Abierto'
                        : concurso.status === 'cerrado'
                        ? 'Cerrado'
                        : concurso.status === 'finalizado'
                        ? 'Finalizado'
                        : concurso.status
                }}
            </span>
        </div>
        
        <!-- Indicador de inscripción -->
        <div v-if="inscrito" class="absolute bottom-4 left-2 px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
            Ya estás inscrito
        </div>
        
        <!-- Contenido de la tarjeta -->
        <h3 class="text-xl font-semibold text-[#611232] mb-4">{{ concurso.nombre }}</h3>

        <!-- Fechas -->
        <div class="flex items-center text-gray-600 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-sm">Inicio: {{ formatDate(concurso.fecha_inicio) }}</span>
        </div>

        <div class="flex items-center text-gray-600 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">Apertura: {{ formatDate(concurso.fecha_apertura) }}</span>
        </div>

        <div class="flex items-center text-gray-600 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">Finalización: {{ formatDate(concurso.fecha_terminacion) }}</span>
        </div>

        <!-- Fase -->
        <div class="flex items-center text-gray-600 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">Fase: {{ formatFase(concurso.fase) }}</span>
        </div>

        <!-- Información adicional según fase -->
        <div v-if="concurso.fase === 'local'" class="flex items-center text-gray-600 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-sm">Plantel: {{ concurso.plantel_nombre }}</span>
        </div>

        <div v-else-if="concurso.fase === 'estatal'" class="flex items-center text-gray-600 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
            </svg>
            <span class="text-sm">Estado: {{ concurso.estado_nombre }}</span>
        </div>

        <!-- Acciones para admin -->
        <div class="absolute bottom-4 right-4 flex space-x-3">
            <!-- Botón para acceder al Podio -->
            <div class="relative group">
            <button @click.stop="handlePodio" class="p-2 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200 border border-gray-300">
                <i class="fas fa-award text-gray-600 group-hover:text-yellow-600"></i>
            </button>
            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                Ver Podio
            </span>
            </div>

            <!-- Botones visibles solo para admin -->
            <template v-if="isAdmin">
            <div class="relative group">
                <button @click.stop="handleEditar" class="p-2 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200 border border-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 group-hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                </button>
                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                Editar
                </span>
            </div>

            <!-- Botón para configuracion -->
            <div class="relative group">
                <button @click.stop="handleConfiguracion" class="p-2 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200 border border-gray-300">
                <i class="fas fa-cog text-gray-600 group-hover:text-blue-600"></i>
                </button>
                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                Configuración
                </span>
            </div>

            <!-- Botón para cambiar estado a cerrado -->
            <div v-if="estado === 'abierto'" class="relative group">
                <button @click.stop="handleCerrar" class="p-2 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200 border border-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 group-hover:text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m-7 4h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                </button>
                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                Cerrar
                </span>
            </div>

            <div class="relative group">
                <button @click.stop="handleEliminar" class="p-2 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200 border border-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 group-hover:text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                </button>
                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                Eliminar
                </span>
            </div>
            </template>
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
  inscrito: Boolean,
  isAdmin: Boolean,
  estado: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['click', 'editar', 'eliminar', 'podio']);

const handleClick = () => {
  emit('click', props.concurso);
};

const handleEditar = (e) => {
  e.stopPropagation();
  emit('editar', props.concurso);
};

const handleEliminar = (e) => {
  e.stopPropagation();
  emit('eliminar', props.concurso);
};

const handleCerrar = (e) => {
  e.stopPropagation();
  emit('cerrar', props.concurso);
};

const handleConfiguracion = (e) => {
  e.stopPropagation();
  emit('configuracion', props.concurso);
};

const handlePodio = (e) => {
  e.stopPropagation();
  emit('podio', props.concurso);
};

const formatDate = (dateString) => {
  if (!dateString) return 'No definida';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-MX', options);
};

const formatFase = (fase) => {
  const fases = {
    'local': 'Local',
    'estatal': 'Estatal',
    'nacional': 'Nacional'
  };
  return fases[fase] || fase;
};
</script>

<style scoped>
.card {
  border: 1px solid #ccc;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 16px;
  cursor: pointer;
}
</style>