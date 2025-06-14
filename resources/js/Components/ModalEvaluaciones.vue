<template>
  <div class="fixed inset-0 z-20 flex items-center justify-center bg-gray-800 bg-opacity-75">
    <div 
      class="bg-white rounded-lg shadow-lg w-11/12 max-w-4xl p-6 lg:ml-[255px]"
    >
      <h2 class="text-xl font-bold text-[#611232] mb-4">
        Evaluaciones de {{ concurso?.nombre }}
      </h2>

      <!-- Resumen del concurso -->
      <div class="mb-4">
        <p class="text-gray-700">
          Evaluaciones pendientes: {{ resumenEvaluaciones?.pendientes || 0 }}
        </p>
        <p class="text-gray-700">
          Evaluaciones completadas: {{ resumenEvaluaciones?.completadas || 0 }}
        </p>
      </div>

      <!-- Botón para crear evaluación manualmente (solo admin) -->
      <div class="mb-4 flex justify-end">
        <button
          v-if="$page.props.auth.user.rol === 'admin'"
          @click="$emit('crear-evaluacion-manual')"
          class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-700"
        >
          Crear Evaluación Manual
        </button>
      </div>

      <div v-if="evaluaciones.length" class="overflow-x-auto">
        <table class="table-auto w-full border-collapse border border-gray-300">
          <thead>
            <tr class="bg-gray-100">
              <th class="border border-gray-300 px-4 py-2 text-left">Equipo</th>
              <th class="border border-gray-300 px-4 py-2 text-left">Proyecto</th>
              <th class="border border-gray-300 px-4 py-2 text-left">Evaluador</th>
              <th class="border border-gray-300 px-4 py-2 text-left">Estado</th>
              <th class="border border-gray-300 px-4 py-2 text-left">Puntaje</th>
              <th v-if="$page.props.auth.user.rol === 'admin'" class="border border-gray-300 px-4 py-2 text-left">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="evaluacion in evaluaciones" :key="evaluacion.id">
              <td class="border border-gray-300 px-4 py-2">
                {{ evaluacion.equipo?.id || 'Sin equipo' }}
              </td>
              <td class="border border-gray-300 px-4 py-2">
                {{ evaluacion.equipo?.proyecto?.nombre || 'Sin proyecto' }}
              </td>
              <td class="border border-gray-300 px-4 py-2">
                {{ evaluacion.evaluador?.name || 'Sin evaluador' }}
              </td>
              <td class="border border-gray-300 px-4 py-2">
                {{ evaluacion.estado }}
              </td>
              <td class="border border-gray-300 px-4 py-2">
                {{ evaluacion.puntaje || 'Sin puntaje' }}
              </td>
              <td v-if="$page.props.auth.user.rol === 'admin'" class="border border-gray-300 px-4 py-2">
                <button
                  @click="$emit('eliminar-evaluacion', evaluacion)"
                  class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-700"
                >
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-else class="text-gray-500">No hay evaluaciones disponibles.</p>

      <!-- Botones -->
      <div class="flex flex-col sm:flex-row justify-end items-center mt-4 gap-2">
        <button
          @click="$emit('finalizar', concurso)"
          class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-700 sm:mr-2 w-full sm:w-auto"
          :disabled="resumenEvaluaciones.pendientes > 0"
        >
          Finalizar Concurso
        </button>
        <!-- Nuevo menú para cambiar estado -->
        <div class="w-full sm:w-auto sm:mr-2">
          <select v-model="estadoSeleccionado" class="border rounded px-2 py-1 w-full sm:w-auto">
            <option value="" disabled>Seleccionar estado</option>
            <option value="abierto">Abrir Concurso</option>
            <option value="cerrado">Cerrar Concurso</option>
          </select>
        </div>
        <button
          @click="emitirCambioEstado"
          class="bg-yellow-500 text-white px-4 py-2 rounded shadow hover:bg-yellow-700 w-full sm:w-auto"
          :disabled="!estadoSeleccionado"
        >
          Cambiar Estado
        </button>
        <p v-if="resumenEvaluaciones.pendientes > 0" class="text-red-500 text-sm w-full sm:w-auto">
          No se puede finalizar el concurso mientras haya evaluaciones pendientes.
        </p>
        <button
          @click="$emit('close')"
          class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-700 w-full sm:w-auto sm:ml-4"
        >
          Cerrar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const estadoSeleccionado = ref('');

// Obtener props y desestructurar concurso
const props = defineProps({
  evaluaciones: {
    type: Array,
    default: () => [],
  },
  concurso: {
    type: Object,
    required: true,
  },
  resumenEvaluaciones: {
    type: Object,
    default: () => ({ pendientes: 0, completadas: 0 }),
  },
});
const { concurso } = props;

// Definir emits y obtener la función emit
const emit = defineEmits(['cambiar-estado-concurso']);

const emitirCambioEstado = () => {
  if (estadoSeleccionado.value) {
    emit('cambiar-estado-concurso', { concurso, nuevoEstado: estadoSeleccionado.value });
    estadoSeleccionado.value = '';
  }
};
</script>
