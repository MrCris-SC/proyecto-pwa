<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Registro de Criterios
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          Registro de Criterios de Evaluación
        </h2>

        <!-- Selector de concurso -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Concurso</label>
          <select 
            v-model="concursoId"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-[#611232] focus:border-[#611232]"
            @change="inicializarCriterios"
          >
            <option value="" disabled selected>Seleccione un concurso</option>
            <option 
              v-for="concurso in concursos" 
              :key="concurso.id" 
              :value="concurso.id"
            >
              {{ concurso.nombre }} ({{ concurso.fase }})
            </option>
          </select>
        </div>

        <!-- Selector de línea de investigación -->
        <div v-if="concursoId" class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Línea de Investigación</label>
          <div class="flex space-x-2 overflow-x-auto pb-2">
            <button
              v-for="linea in lineas"
              :key="linea.id"
              @click="lineaActiva = linea.id"
              class="px-4 py-2 rounded-lg whitespace-nowrap transition"
              :class="{
                'bg-[#611232] text-white': lineaActiva === linea.id,
                'bg-gray-200 text-gray-700 hover:bg-gray-300': lineaActiva !== linea.id,
                'border-2 border-green-500': lineasGuardadas.includes(linea.id)
              }"
              :title="lineasGuardadas.includes(linea.id) ? 'Línea guardada' : 'Línea no guardada'"
            >
              {{ linea.nombre }}
              <span class="ml-1 text-xs font-normal">
                ({{ sumaPorLinea[linea.id] || 0 }}/100)
                <i v-if="lineasGuardadas.includes(linea.id)" class="fas fa-check ml-1 text-green-500"></i>
              </span>
            </button>
          </div>
        </div>

        <!-- Criterios por línea -->
        <div v-if="concursoId && lineaActiva" class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-[#611232]">
              Criterios para {{ lineas.find(l => l.id === lineaActiva)?.nombre }}
            </h3>
            <div class="flex items-center space-x-2">
              <span class="text-sm" :class="{
                'text-green-600': lineasGuardadas.includes(lineaActiva),
                'text-gray-500': !lineasGuardadas.includes(lineaActiva)
              }">
                {{ lineasGuardadas.includes(lineaActiva) ? 'Guardado' : 'No guardado' }}
              </span>
              <button 
                @click="agregarCriterio"
                class="px-3 py-1 bg-[#8A1C4A] text-white rounded-lg hover:bg-[#9C2755] transition"
              >
                <i class="fas fa-plus mr-1"></i> Agregar
              </button>
            </div>
          </div>

          <div v-if="criteriosPorLinea[lineaActiva]?.length === 0" class="text-center py-4 text-gray-500">
            No hay criterios registrados para esta línea
          </div>

          <div v-else>
            <div 
              v-for="(criterio, index) in criteriosPorLinea[lineaActiva]" 
              :key="index"
              class="border-b border-gray-200 py-4"
            >
              <div class="flex items-start mb-3">
                <div class="flex-grow">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Criterio</label>
                  <input
                    v-model="criterio.nombre"
                    type="text"
                    class="w-full p-2 border border-gray-300 rounded focus:ring-[#611232] focus:border-[#611232]"
                    placeholder="Ej. Originalidad, Presentación, etc."
                  >
                </div>
                <div class="ml-4 w-32">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Puntaje Máximo</label>
                  <input
                    v-model.number="criterio.puntaje_maximo"
                    type="number"
                    min="1"
                    max="100"
                    class="w-full p-2 border border-gray-300 rounded focus:ring-[#611232] focus:border-[#611232]"
                    @change="validarPuntajes"
                  >
                </div>
                <button
                  @click="eliminarCriterio(index)"
                  class="ml-4 p-2 text-red-500 hover:text-red-700"
                  title="Eliminar criterio"
                  :disabled="criteriosPorLinea[lineaActiva].length <= 1"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>

            <div class="mt-4 flex justify-between items-center">
              <span class="text-sm font-medium">
                Total: {{ sumaPorLinea[lineaActiva] || 0 }}/100 puntos
                <span v-if="sumaPorLinea[lineaActiva] !== 100" class="ml-2 text-red-500 text-xs">
                  (La suma debe ser exactamente 100)
                </span>
              </span>
              
              <button
                @click="guardarLineaActual"
                :disabled="guardandoLinea === lineaActiva || sumaPorLinea[lineaActiva] !== 100"
                class="px-4 py-2 bg-[#4CAF50] text-white rounded-lg hover:bg-[#45a049] disabled:opacity-50 transition"
              >
                <span v-if="guardandoLinea === lineaActiva">
                  <i class="fas fa-spinner fa-spin mr-2"></i> Guardando...
                </span>
                <span v-else>
                  <i class="fas fa-save mr-2"></i> Guardar esta línea
                </span>
              </button>
            </div>
          </div>
        </div>

        <!-- Botón de guardar general -->
        <div v-if="concursoId && lineasGuardadas.length < lineas.length" class="flex justify-end">
          <button
            @click="guardarCriterios"
            :disabled="guardando || !validarSumas"
            class="px-6 py-2 bg-[#611232] text-white rounded-lg hover:bg-[#8A1C4A] disabled:opacity-50 transition"
          >
            <span v-if="guardando">
              <i class="fas fa-spinner fa-spin mr-2"></i> Guardando...
            </span>
            <span v-else>
              <i class="fas fa-save mr-2"></i> Guardar Todos los Criterios
            </span>
          </button>
        </div>

        <!-- Resumen de progreso -->
        <div v-if="concursoId" class="mt-6 p-4 bg-gray-50 rounded-lg">
          <h3 class="text-lg font-semibold mb-2 text-[#611232]">Progreso</h3>
          <div class="flex items-center">
            <div class="w-full bg-gray-200 rounded-full h-2.5 mr-4">
              <div 
                class="bg-green-600 h-2.5 rounded-full" 
                :style="{ width: `${(lineasGuardadas.length / lineas.length) * 100}%` }"
              ></div>
            </div>
            <span class="text-sm font-medium">
              {{ lineasGuardadas.length }} de {{ lineas.length }} líneas guardadas
            </span>
          </div>
        </div>

        <!-- Mensajes de éxito/error -->
        <div v-if="mensaje" class="p-4 mb-4 rounded-lg" :class="{
          'bg-green-100 text-green-800': mensaje.tipo === 'exito',
          'bg-red-100 text-red-800': mensaje.tipo === 'error'
        }">
          {{ mensaje.texto }}
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  concursos: {
    type: Array,
    required: true
  },
  criteriosExistentes: {
    type: Array,
    default: () => []
  },
  lineas: {
    type: Array,
    default: () => []
  },
  userRole: {
    type: String,
    required: true
  }
});

const concursoId = ref('');
const lineaActiva = ref(null);
const criteriosPorLinea = ref({});
const guardando = ref(false);
const guardandoLinea = ref(null);
const mensaje = ref(null);
const lineasGuardadas = ref([]);

// Inicializar estructura de criterios
const inicializarCriterios = () => {
  if (!concursoId.value) return;

  // Cargar datos guardados temporalmente si existen
  cargarDesdeLocalStorage();

  // Inicializar estructura para todas las líneas
  const estructuraInicial = {};
  props.lineas.forEach(linea => {
    estructuraInicial[linea.id] = props.criteriosExistentes
      .filter(c => c.concurso_id == concursoId.value && c.linea_investigacion_id == linea.id)
      .map(c => ({ ...c }));
    
    if (estructuraInicial[linea.id].length === 0 && !criteriosPorLinea.value[linea.id]) {
      estructuraInicial[linea.id] = [{ 
        nombre: '', 
        puntaje_maximo: 0,
        linea_investigacion_id: linea.id,
        concurso_id: concursoId.value
      }];
    }

    // Si hay criterios existentes, marcamos la línea como guardada
    if (estructuraInicial[linea.id].some(c => c.id)) {
      if (!lineasGuardadas.value.includes(linea.id)) {
        lineasGuardadas.value.push(linea.id);
      }
    }
  });

  // Conservar los datos editados no guardados
  Object.keys(criteriosPorLinea.value).forEach(lineaId => {
    if (estructuraInicial[lineaId] && criteriosPorLinea.value[lineaId]) {
      estructuraInicial[lineaId] = criteriosPorLinea.value[lineaId];
    }
  });

  criteriosPorLinea.value = estructuraInicial;
  lineaActiva.value = props.lineas[0]?.id || null;
};

// Calcular suma por línea
const sumaPorLinea = computed(() => {
  const sumas = {};
  Object.keys(criteriosPorLinea.value).forEach(lineaId => {
    sumas[lineaId] = criteriosPorLinea.value[lineaId].reduce(
      (sum, c) => sum + (parseFloat(c.puntaje_maximo) || 0), 0
    );
  });
  return sumas;
});

// Validar que todas las líneas sumen 100
const validarSumas = computed(() => {
  return Object.values(sumaPorLinea.value).every(suma => suma === 100);
});

// Agregar nuevo criterio
const agregarCriterio = () => {
  if (!lineaActiva.value) return;
  
  criteriosPorLinea.value[lineaActiva.value].push({
    nombre: '',
    puntaje_maximo: 0,
    linea_investigacion_id: lineaActiva.value,
    concurso_id: concursoId.value
  });
  
  guardarEnLocalStorage();
};

// Eliminar criterio
const eliminarCriterio = (index) => {
  if (criteriosPorLinea.value[lineaActiva.value].length > 1) {
    criteriosPorLinea.value[lineaActiva.value].splice(index, 1);
    guardarEnLocalStorage();
  } else {
    mostrarMensaje('Debe haber al menos un criterio por línea', 'error');
  }
};

// Validar puntajes al cambiar
const validarPuntajes = () => {
  const sumaActual = sumaPorLinea.value[lineaActiva.value];
  if (sumaActual > 100) {
    mostrarMensaje(`La suma no puede exceder 100 puntos (actual: ${sumaActual})`, 'error');
  }
  guardarEnLocalStorage();
};

// Guardar en localStorage
const guardarEnLocalStorage = () => {
  if (concursoId.value) {
    localStorage.setItem(`criterios_temp_${concursoId.value}`, JSON.stringify(criteriosPorLinea.value));
  }
};

// Cargar desde localStorage
const cargarDesdeLocalStorage = () => {
  if (concursoId.value) {
    const datos = localStorage.getItem(`criterios_temp_${concursoId.value}`);
    if (datos) {
      criteriosPorLinea.value = JSON.parse(datos);
    }
  }
};

// Guardar solo la línea actual
const guardarLineaActual = async () => {
  const lineaId = lineaActiva.value;
  if (!lineaId || sumaPorLinea.value[lineaId] !== 100) {
    mostrarMensaje('La suma debe ser exactamente 100 puntos para guardar', 'error');
    return;
  }

  guardandoLinea.value = lineaId;

  try {
    await router.post(route('criterios.storeLinea'), {
      concurso_id: concursoId.value,
      linea_id: lineaId,
      criterios: criteriosPorLinea.value[lineaId]
    }, {
      onSuccess: () => {
        mostrarMensaje(`Criterios para ${props.lineas.find(l => l.id === lineaId)?.nombre} guardados exitosamente`, 'exito');
        // Marcar la línea como guardada
        if (!lineasGuardadas.value.includes(lineaId)) {
          lineasGuardadas.value.push(lineaId);
        }
        // Limpiar el almacenamiento local para esta línea
        guardarEnLocalStorage();
      },
      onError: (errors) => {
        mostrarMensaje(Object.values(errors).join(' '), 'error');
      }
    });
  } catch (error) {
    mostrarMensaje('Error al guardar los criterios', 'error');
  } finally {
    guardandoLinea.value = null;
  }
};

// Guardar todos los criterios
const guardarCriterios = async () => {
  if (!validarSumas.value) {
    mostrarMensaje('Todas las líneas deben sumar exactamente 100 puntos', 'error');
    return;
  }

  guardando.value = true;

  try {
    await router.post(route('criterios.store'), {
      concurso_id: concursoId.value,
      criterios: Object.values(criteriosPorLinea.value).flat()
    }, {
      onSuccess: () => {
        mostrarMensaje('Todos los criterios guardados exitosamente', 'exito');
        // Marcar todas las líneas como guardadas
        lineasGuardadas.value = props.lineas.map(linea => linea.id);
        // Limpiar el almacenamiento local
        localStorage.removeItem(`criterios_temp_${concursoId.value}`);
      },
      onError: (errors) => {
        mostrarMensaje(Object.values(errors).join(' '), 'error');
      }
    });
  } catch (error) {
    mostrarMensaje('Error al guardar los criterios', 'error');
  } finally {
    guardando.value = false;
  }
};

// Mostrar mensajes temporales
const mostrarMensaje = (texto, tipo) => {
  mensaje.value = { texto, tipo };
  setTimeout(() => { mensaje.value = null; }, 5000);
};

// Watch para cambios en los criterios
watch(criteriosPorLinea, (newVal) => {
  guardarEnLocalStorage();
}, { deep: true });

// Inicializar si solo hay un concurso
onMounted(() => {
  if (props.concursos.length === 1) {
    concursoId.value = props.concursos[0].id;
    inicializarCriterios();
  }
});

const handleMenuSelected = (menu) => {
  if (menu === 'Concursos') {
    router.get(route('concursos.index'));
  }
};
</script>

<style scoped>
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.lineas-container {
  scrollbar-width: thin;
  scrollbar-color: #611232 #f1f1f1;
}
.lineas-container::-webkit-scrollbar {
  height: 6px;
}
.lineas-container::-webkit-scrollbar-track {
  background: #f1f1f1;
}
.lineas-container::-webkit-scrollbar-thumb {
  background-color: #611232;
  border-radius: 3px;
}

/* Estilo para botones deshabilitados */
button:disabled {
  cursor: not-allowed;
}
</style>