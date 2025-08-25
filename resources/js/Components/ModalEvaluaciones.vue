<template>
  <div class="fixed inset-0 z-20 flex items-center justify-center bg-gray-800 bg-opacity-75">
    <div 
      class="bg-white rounded-lg shadow-lg w-11/12 max-w-4xl p-6 lg:ml-[255px] max-h-screen overflow-y-auto"
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

      <!-- Buscador -->
      <div class="mb-4 flex justify-between items-center">
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por equipo, proyecto o evaluador..."
          class="border rounded px-3 py-2 w-full max-w-xs"
        />
        <!-- Botón para crear evaluación manualmente (solo admin) -->
        <button
          v-if="$page.props.auth.user.rol === 'admin'"
          @click="$emit('crear-evaluacion-manual')"
          class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-700 ml-2"
        >
          Crear Evaluación Manual
        </button>
      </div>

      <div v-if="evaluacionesFiltradas.length" class="overflow-x-auto">
        <div class="max-h-[60vh] overflow-y-auto">
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
              <tr v-for="evaluacion in evaluacionesPaginadas" :key="evaluacion.id">
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
        <!-- Paginador -->
        <div class="flex justify-end items-center mt-2 gap-2">
          <button
            @click="paginaActual = Math.max(1, paginaActual - 1)"
            :disabled="paginaActual === 1"
            class="px-2 py-1 border rounded disabled:opacity-50"
          >
            Anterior
          </button>
          <span>Página {{ paginaActual }} de {{ totalPaginas }}</span>
          <button
            @click="paginaActual = Math.min(totalPaginas, paginaActual + 1)"
            :disabled="paginaActual === totalPaginas"
            class="px-2 py-1 border rounded disabled:opacity-50"
          >
            Siguiente
          </button>
        </div>
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
        <!-- Botón reactivo para cambiar estado -->
        <button
          v-if="concurso && concurso.status === 'abierto'"
          @click="abrirModalCambioEstado"
          class="px-4 py-2 rounded shadow w-full sm:w-auto bg-yellow-600 text-white hover:bg-yellow-800"
        >
          Cerrar Concurso
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

      <!-- Modal de confirmación para cambiar estado -->
      <div v-if="mostrarModalConfirmacion" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
          <h3 class="text-lg font-bold mb-4 text-[#611232]">Confirmar acción</h3>
          <p class="mb-6">
            ¿Está seguro que desea 
            cerrar
            el concurso <strong>{{ concurso.nombre }}</strong>?
          </p>
          <div class="flex justify-end gap-2">
            <button class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400" @click="mostrarModalConfirmacion = false">Cancelar</button>
            <button
              class="px-4 py-2 bg-[#611232] text-white rounded hover:bg-[#4a0d24]"
              @click="confirmarCambioEstado"
            >
              Sí, cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const estadoSeleccionado = ref('');
const busqueda = ref('');
const paginaActual = ref(1);
const registrosPorPagina = 10;
const mostrarModalConfirmacion = ref(false);

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

// Filtrado de evaluaciones
const evaluacionesFiltradas = computed(() => {
  if (!busqueda.value) return props.evaluaciones;
  const texto = busqueda.value.toLowerCase();
  return props.evaluaciones.filter(e => {
    const equipo = e.equipo?.id?.toString() || '';
    const proyecto = e.equipo?.proyecto?.nombre?.toLowerCase() || '';
    const evaluador = e.evaluador?.name?.toLowerCase() || '';
    return (
      equipo.includes(texto) ||
      proyecto.includes(texto) ||
      evaluador.includes(texto)
    );
  });
});

const totalPaginas = computed(() => {
  return Math.max(1, Math.ceil(evaluacionesFiltradas.value.length / registrosPorPagina));
});

const evaluacionesPaginadas = computed(() => {
  const inicio = (paginaActual.value - 1) * registrosPorPagina;
  return evaluacionesFiltradas.value.slice(inicio, inicio + registrosPorPagina);
});

// Resetear página al cambiar búsqueda
watch(busqueda, () => {
  paginaActual.value = 1;
});

// Definir emits y obtener la función emit
const emit = defineEmits(['cambiar-estado-concurso']);

function abrirModalCambioEstado() {
  mostrarModalConfirmacion.value = true;
}

function confirmarCambioEstado() {
  mostrarModalConfirmacion.value = false;
  // Solo permite cerrar el concurso si está abierto
  if (concurso.status === 'abierto') {
    emit('cambiar-estado-concurso', { concurso, nuevoEstado: 'cerrado' });
  }
}
</script>
 
