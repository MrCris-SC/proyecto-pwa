<template>
  <div>
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-8 w-full max-w-6xl mx-auto">
      <h2 class="text-xl font-bold mb-4">Equipos registrados en {{ concurso?.nombre }}</h2>
      <!-- Botón para descargar PDF -->
      <button
        class="mb-4 px-4 py-2 bg-blue-600 text-white rounded"
        @click="descargarPDF"
      >
        Descargar reporte PDF
      </button>
      <!-- Buscador -->
      <div class="mb-4 flex flex-col sm:flex-row gap-2 items-center">
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por proyecto o integrante..."
          class="border border-gray-300 rounded px-3 py-2 w-full sm:w-1/2"
        />
      </div>
      <div 
        v-if="equiposFiltrados.length"
        class="max-h-[70vh] overflow-y-auto"
      >
        <div class="divide-y divide-gray-300">
          <div
            v-for="equipo in equiposPaginados"
            :key="equipo.id"
            class="py-3 cursor-pointer hover:bg-gray-100 transition"
            @click="toggleEquipo(equipo.id, equipo.proyecto?.id)"
          >
            <div class="flex justify-between items-center">
              <div>
                <span class="font-semibold">#{{ equipo.id }}</span>
                <span class="ml-2">{{ equipo.proyecto?.nombre || 'Sin proyecto' }}</span>
                <span class="ml-2 text-gray-500 text-sm">
                  Integrantes: 
                  <!-- Imprime los nombres de los integrantes separados por coma -->
                <span v-if="Array.isArray(equipo.participantes) && equipo.participantes.length > 0">
                    {{ equipo.participantes.length }}
                </span>
                  <span v-else>
                    <!-- Depuración: muestra el array de participantes -->
                    {{ equipo.participantes && equipo.participantes.length === 0 ? '0' : 'Error de datos' }}
                  </span>
                </span>
                <span v-if="equipo.proyecto" class="ml-4 px-2 py-1 rounded text-xs"
                  :class="{
                    'bg-green-100 text-green-800': equipo.proyecto.estado === 'En orden',
                    'bg-red-100 text-red-800': equipo.proyecto.estado === 'Descalificado'
                  }"
                >
                  Estado: {{ equipo.proyecto.estado }}
                </span>
              </div>
              <svg
                :class="{'transform rotate-90': isOpen(equipo.id)}"
                class="w-4 h-4 transition-transform"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
            <transition name="fade">
              <div v-if="isOpen(equipo.id)" class="mt-2 bg-gray-50 rounded p-3">
                <div>
                  <strong>Integrantes:</strong>
                  <ul class="list-disc ml-6">
                    <li v-for="p in (equipo.participantes || [])" :key="p.nombre">
                      {{ p.nombre }} 
                    </li>
                  </ul>
                </div>
                <div class="mt-2">
                  <strong>Evaluaciones del proyecto:</strong>
                  <div v-if="evaluacionesResumen[equipo.id] && evaluacionesResumen[equipo.id].length">
                    <ul>
                      <li v-for="(ev, idx) in evaluacionesResumen[equipo.id]" :key="idx" class="mb-2">
                        <div>
                          <span class="font-semibold">Evaluador:</span> {{ ev.evaluador_nombre }} |
                          <span class="font-semibold">Estado:</span> {{ ev.estado }} |
                          
                        </div>
                        <div v-if="ev.comentarios" class="ml-4 mt-1 text-gray-700">
                          <span class="font-semibold">Comentarios:</span>
                          <span>{{ ev.comentarios }}</span>
                        </div>
                      </li>
                    </ul>
                    <!-- Mostrar promedio final si existe -->
                    <div v-if="promedioFinal[equipo.id] !== null" class="mt-2">
                      <span class="font-semibold">Promedio final:</span>
                      <span>{{ promedioFinal[equipo.id] }}</span>
                    </div>
                  </div>
                  <div v-else-if="cargandoEvaluaciones === equipo.id">
                    <span class="text-gray-400">Cargando evaluaciones...</span>
                  </div>
                  <div v-else>
                    <span class="text-gray-400">No hay evaluaciones o no se ha cargado información.</span>
                  </div>
                </div>
                <div v-if="equipo.proyecto" class="mt-4 flex gap-2">
                  <button
                    v-if="equipo.proyecto.estado !== 'Descalificado'"
                    class="px-3 py-1 bg-red-600 text-white rounded text-sm"
                    @click.stop="cambiarEstadoProyecto(equipo.proyecto.id, 'Descalificado', equipo)"
                  >
                    Descalificar
                  </button>
                  <button
                    v-if="equipo.proyecto.estado === 'Descalificado'"
                    class="px-3 py-1 bg-green-600 text-white rounded text-sm"
                    @click.stop="cambiarEstadoProyecto(equipo.proyecto.id, 'En orden', equipo)"
                  >
                    Marcar En orden
                  </button>
                </div>
              </div>
            </transition>
          </div>
        </div>
        <!-- Paginador -->
        <div class="flex justify-center mt-4 gap-2">
          <button
            class="px-3 py-1 rounded border"
            :disabled="paginaActual === 1"
            @click="paginaActual--"
          >Anterior</button>
          <span class="px-2 py-1">Página {{ paginaActual }} de {{ totalPaginas }}</span>
          <button
            class="px-3 py-1 rounded border"
            :disabled="paginaActual === totalPaginas"
            @click="paginaActual++"
          >Siguiente</button>
        </div>
      </div>
      <div v-else>
        <p>No hay equipos registrados para este concurso.</p>
      </div>
      <button class="mt-4 px-4 py-2 bg-gray-500 text-white rounded" @click="$emit('close')">Cerrar</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  concurso: Object,
  equipos: Array
});

const participantes = ref([]);

// Carga los participantes al montar el componente
onMounted(async () => {
  if (props.concurso && props.concurso.id) {
    try {
      const res = await axios.get(route('concursosFinales.participantesPorConcurso', { concurso: props.concurso.id }));
      participantes.value = res.data.participantes || [];
    } catch (e) {
      participantes.value = [];
    }
  }
});

// Asocia solo nombre y teléfono de los participantes a cada equipo
const equiposConParticipantes = computed(() => {
  if (!props.equipos || !participantes.value) return [];
  return props.equipos.map(equipo => ({
    ...equipo,
    participantes: participantes.value
      .filter(p => p.equipo_id === equipo.id)
      .map(p => ({
        nombre: p.nombre,
        telefono: p.telefono
      }))
  }));
});

// Depuración: revisa la estructura de los equipos y sus participantes en el frontend
console.log('Equipos con participantes asociados:', equiposConParticipantes.value);

const openEquipos = ref([]);
const evaluacionesResumen = ref({});
const promedioFinal = ref({}); // Nuevo: almacena el promedio final por equipo
const cargandoEvaluaciones = ref(null);

// Buscador y paginador
const busqueda = ref('');
const paginaActual = ref(1);
const elementosPorPagina = 10;

const equiposFiltrados = computed(() => {
  if (!equiposConParticipantes.value) return [];
  const texto = busqueda.value.trim().toLowerCase();
  if (!texto) return equiposConParticipantes.value;
  return equiposConParticipantes.value.filter(equipo => {
    const nombreProyecto = (equipo.proyecto?.nombre || '').toLowerCase();
    const nombresIntegrantes = (equipo.participantes || []).map(p => (p.nombre || '').toLowerCase()).join(' ');
    return nombreProyecto.includes(texto) || nombresIntegrantes.includes(texto);
  });
});

const totalPaginas = computed(() => Math.max(1, Math.ceil(equiposFiltrados.value.length / elementosPorPagina)));

const equiposPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * elementosPorPagina;
  return equiposFiltrados.value.slice(inicio, inicio + elementosPorPagina);
});

// Reinicia a la página 1 si cambia la búsqueda o la lista de equipos
watch([busqueda, () => props.equipos], () => {
  paginaActual.value = 1;
});

function isOpen(equipoId) {
  return openEquipos.value.includes(equipoId);
}

async function toggleEquipo(equipoId, proyectoId) {
  if (isOpen(equipoId)) {
    openEquipos.value = openEquipos.value.filter(id => id !== equipoId);
  } else {
    openEquipos.value.push(equipoId);
    // Si aún no se han cargado las evaluaciones para este equipo, solicítalas
    if (!evaluacionesResumen.value[equipoId] && proyectoId) {
      cargandoEvaluaciones.value = equipoId;
      try {
        const res = await axios.get(route('concursosFinales.evaluacionesResumen', { proyecto: proyectoId }));
        evaluacionesResumen.value[equipoId] = res.data.resumen || [];
        promedioFinal.value[equipoId] = res.data.promedio_final ?? null; // Guarda el promedio final
      } catch (e) {
        evaluacionesResumen.value[equipoId] = [];
        promedioFinal.value[equipoId] = null;
      } finally {
        cargandoEvaluaciones.value = null;
      }
    }
  }
}

// Cambiar estado del proyecto (Descalificado/En orden)
async function cambiarEstadoProyecto(proyectoId, nuevoEstado, equipo) {
  if (!proyectoId) return;
  try {
    const res = await axios.post(route('concursosFinales.cambiarEstadoProyecto', { proyecto: proyectoId }), {
      estado: nuevoEstado
    });
    if (res.data && res.data.success) {
      // Actualiza el estado en el objeto reactivo
      equipo.proyecto.estado = res.data.estado;
    }
  } catch (e) {
    alert('No se pudo cambiar el estado del proyecto.');
  }
}

function descargarPDF() {
  if (!props.concurso || !props.concurso.id) return;
  window.open(route('concursosFinales.descargarReporteEquipos', { concurso: props.concurso.id }), '_blank');
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: all 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  max-height: 0;
}
.fade-enter-to, .fade-leave-from {
  opacity: 1;
  max-height: 500px;
}
</style>

