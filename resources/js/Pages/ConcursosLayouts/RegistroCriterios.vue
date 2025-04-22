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
            @change="cargarDatosIniciales"
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

        <!-- Selector de modalidad -->
        <div v-if="concursoId" class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Modalidad</label>
          <div class="flex space-x-2 overflow-x-auto pb-2">
            <button
              v-for="modalidad in modalidades"
              :key="modalidad.id"
              @click="cambiarModalidad(modalidad.id)"
              class="px-4 py-2 rounded-lg whitespace-nowrap transition"
              :class="{
                'bg-[#611232] text-white': modalidadActiva === modalidad.id,
                'bg-gray-200 text-gray-700 hover:bg-gray-300': modalidadActiva !== modalidad.id,
                'border-2 border-green-500': modalidadesCompletas.includes(modalidad.id)
              }"
            >
              {{ modalidad.nombre }}
              <span class="ml-1 text-xs font-normal">
                ({{ sumaPorModalidad[modalidad.id] || 0 }}/100)
                <i v-if="modalidadesCompletas.includes(modalidad.id)" class="fas fa-check ml-1 text-green-500"></i>
              </span>
            </button>
          </div>
        </div>

        <!-- Criterios por modalidad -->
        <div v-if="concursoId && modalidadActiva" class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-[#611232]">
              Criterios para {{ modalidades.find(m => m.id === modalidadActiva)?.nombre }}
            </h3>
            <div class="flex items-center space-x-2">
              <span class="text-sm font-medium" :class="{
                'text-green-600': modalidadCompleta,
                'text-red-600': !modalidadCompleta
              }">
                {{ modalidadCompleta ? 'Completa (100 pts)' : `Incompleta (${sumaDistribucion}/100 pts)` }}
              </span>
            </div>
          </div>

          <!-- Distribución de puntos entre tipos -->
          <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h4 class="font-medium mb-3 text-[#611232]">Distribución de Puntos</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div v-for="tipo in tiposCriterio" :key="tipo.value" 
                   class="p-3 rounded-lg border transition-colors"
                   :class="{
                     'border-[#611232] bg-[#F8E8EE]': tipoCriterioActivo === tipo.value,
                     'border-gray-300 hover:border-[#611232]/50': tipoCriterioActivo !== tipo.value,
                     'bg-green-50 border-green-300': sumaPorTipo[tipo.value] === puntosAsignados[tipo.value] && puntosAsignados[tipo.value] > 0
                   }"
                   @click="tipoCriterioActivo = tipo.value">
                <div class="flex items-center justify-between mb-2">
                  <span class="font-medium">{{ tipo.label }}</span>
                  <span class="text-sm px-2 py-1 rounded" 
                        :class="{
                          'bg-green-100 text-green-800': sumaPorTipo[tipo.value] === puntosAsignados[tipo.value],
                          'bg-red-100 text-red-800': sumaPorTipo[tipo.value] !== puntosAsignados[tipo.value]
                        }">
                    {{ sumaPorTipo[tipo.value] || 0 }}/{{ puntosAsignados[tipo.value] || 0 }}
                  </span>
                </div>
                <input
                  v-model.number="puntosAsignados[tipo.value]"
                  type="number"
                  min="0"
                  max="100"
                  class="w-full p-2 border border-gray-300 rounded focus:ring-[#611232] focus:border-[#611232] distribution-input"
                  @change="validarDistribucion"
                  :disabled="modalidadesCompletas.includes(modalidadActiva)"
                >
              </div>
            </div>
            <div class="mt-3 flex justify-between items-center">
              <span class="text-sm font-medium" :class="{
                'text-green-600': distribucionValida,
                'text-red-600': !distribucionValida
              }">
                Total: {{ sumaDistribucion }}/100 puntos
              </span>
              <button
                v-if="!distribucionValida && !modalidadesCompletas.includes(modalidadActiva)"
                @click="autoDistribuir"
                class="text-sm text-[#611232] hover:underline flex items-center"
              >
                <i class="fas fa-magic mr-1"></i> Distribuir equitativamente
              </button>
            </div>
          </div>

          <!-- Selector de tipo de criterio -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Criterio Activo</label>
            <div class="flex space-x-2">
              <button
                v-for="tipo in tiposCriterio"
                :key="tipo.value"
                @click="tipoCriterioActivo = tipo.value"
                class="px-4 py-2 rounded-lg transition flex-1 text-center"
                :class="{
                  'bg-[#611232] text-white': tipoCriterioActivo === tipo.value,
                  'bg-gray-200 text-gray-700 hover:bg-gray-300': tipoCriterioActivo !== tipo.value,
                  'border-2 border-green-500': sumaPorTipo[tipo.value] === puntosAsignados[tipo.value] && puntosAsignados[tipo.value] > 0
                }"
              >
                {{ tipo.label }}
                <span class="ml-1 text-xs font-normal">
                  ({{ sumaPorTipo[tipo.value] || 0 }}/{{ puntosAsignados[tipo.value] || 0 }})
                </span>
              </button>
            </div>
          </div>

          <!-- Lista de criterios -->
          <div v-if="criteriosFiltrados.length === 0" class="text-center py-4 text-gray-500">
            No hay criterios registrados para esta combinación
          </div>

          <div v-else>
            <div 
              v-for="(criterio, index) in criteriosFiltrados" 
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
                    :disabled="modalidadesCompletas.includes(modalidadActiva)"
                  >
                </div>
                <div class="ml-4 w-32">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Puntaje Máximo</label>
                  <input
                    v-model.number="criterio.puntaje_maximo"
                    type="number"
                    min="1"
                    :max="puntosAsignados[tipoCriterioActivo] || 100"
                    class="w-full p-2 border border-gray-300 rounded focus:ring-[#611232] focus:border-[#611232]"
                    @change="validarPuntajes"
                    :disabled="modalidadesCompletas.includes(modalidadActiva)"
                  >
                </div>
                <button
                  @click="eliminarCriterio(criterio.id || index)"
                  class="ml-4 p-2 text-red-500 hover:text-red-700"
                  title="Eliminar criterio"
                  :disabled="modalidadesCompletas.includes(modalidadActiva)"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Agregar nuevo criterio -->
          <button 
            @click="agregarCriterio"
            class="mt-4 px-4 py-2 bg-[#8A1C4A] text-white rounded-lg hover:bg-[#9C2755] transition flex items-center justify-center"
            :disabled="puntosAsignados[tipoCriterioActivo] <= 0 || modalidadesCompletas.includes(modalidadActiva)"
          >
            <i class="fas fa-plus mr-2"></i> Agregar Criterio
          </button>

          <!-- Resumen y guardar -->
          <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium">
                Total para {{ tiposCriterio.find(t => t.value === tipoCriterioActivo)?.label }}: 
                <span :class="{
                  'text-green-600': sumaPorTipo[tipoCriterioActivo] === puntosAsignados[tipoCriterioActivo],
                  'text-red-600': sumaPorTipo[tipoCriterioActivo] !== puntosAsignados[tipoCriterioActivo]
                }">
                  {{ sumaPorTipo[tipoCriterioActivo] || 0 }}/{{ puntosAsignados[tipoCriterioActivo] || 0 }} puntos
                </span>
                <span v-if="sumaPorTipo[tipoCriterioActivo] !== (puntosAsignados[tipoCriterioActivo] || 0)" class="ml-2 text-red-500 text-xs">
                  (La suma debe ser exactamente {{ puntosAsignados[tipoCriterioActivo] || 0 }})
                </span>
              </span>
              
              <button
                @click="guardarTipoCriterio"
                :disabled="guardando || !puedeGuardarTipo || modalidadesCompletas.includes(modalidadActiva)"
                class="px-4 py-2 bg-[#4CAF50] text-white rounded-lg hover:bg-[#45a049] disabled:opacity-50 transition flex items-center"
              >
                <span v-if="guardando">
                  <i class="fas fa-spinner fa-spin mr-2"></i> Guardando...
                </span>
                <span v-else>
                  <i class="fas fa-save mr-2"></i> Guardar {{ tiposCriterio.find(t => t.value === tipoCriterioActivo)?.label }}
                </span>
              </button>
            </div>

            <div class="mt-4">
              <h4 class="text-sm font-medium mb-2">Resumen por tipos:</h4>
              <div class="grid grid-cols-3 gap-2">
                <div v-for="tipo in tiposCriterio" :key="tipo.value" 
                     class="p-2 rounded border transition-colors"
                     :class="{
                       'border-green-500 bg-green-50': sumaPorTipo[tipo.value] === puntosAsignados[tipo.value],
                       'border-red-300 bg-red-50': sumaPorTipo[tipo.value] !== puntosAsignados[tipo.value],
                       'cursor-pointer hover:border-[#611232]': tipoCriterioActivo !== tipo.value
                     }"
                     @click="tipoCriterioActivo = tipo.value">
                  <span class="block text-xs font-medium text-gray-700">{{ tipo.label }}</span>
                  <span class="block text-sm font-semibold" :class="{
                    'text-green-600': sumaPorTipo[tipo.value] === puntosAsignados[tipo.value],
                    'text-red-600': sumaPorTipo[tipo.value] !== puntosAsignados[tipo.value]
                  }">
                    {{ sumaPorTipo[tipo.value] || 0 }}/{{ puntosAsignados[tipo.value] || 0 }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Botón de guardar general -->
        <div v-if="concursoId && modalidadActiva" class="flex justify-end mt-6">
          <button
            @click="guardarTodosCriterios"
            :disabled="guardando || !modalidadCompleta || modalidadesCompletas.includes(modalidadActiva)"
            class="px-6 py-2 bg-[#611232] text-white rounded-lg hover:bg-[#8A1C4A] disabled:opacity-50 transition flex items-center"
          >
            <span v-if="guardando">
              <i class="fas fa-spinner fa-spin mr-2"></i> Guardando...
            </span>
            <span v-else>
              <i class="fas fa-save mr-2"></i> Guardar Todos los Criterios
            </span>
          </button>
        </div>

        <!-- Mensajes de éxito/error -->
        <div v-if="mensaje" class="p-4 mb-4 rounded-lg" :class="{
          'bg-green-100 text-green-800': mensaje.tipo === 'exito',
          'bg-red-100 text-red-800': mensaje.tipo === 'error'
        }">
          <div class="flex items-center">
            <i :class="{
              'fas fa-check-circle mr-2': mensaje.tipo === 'exito',
              'fas fa-exclamation-circle mr-2': mensaje.tipo === 'error'
            }"></i>
            {{ mensaje.texto }}
          </div>
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
  modalidades: {
    type: Array,
    default: () => []
  },
  userRole: {
    type: String,
    required: true
  }
});

// Tipos de criterio según la guía
const tiposCriterio = ref([
  { value: 'informe', label: 'I. Informe' },
  { value: 'modalidad', label: 'II. Modalidad' },
  { value: 'exposicion', label: 'III. Exposición' }
]);

const concursoId = ref('');
const modalidadActiva = ref(null);
const tipoCriterioActivo = ref('informe');
const criterios = ref([]);
const guardando = ref(false);
const mensaje = ref(null);
const modalidadesGuardadas = ref([]);
const modalidadesCompletas = ref([]);
const puntosAsignados = ref({
  informe: 0,
  modalidad: 0,
  exposicion: 0
});

// Almacenará los puntos por modalidad
const puntosPorModalidad = ref({});

// Computed properties
const sumaDistribucion = computed(() => {
  return Object.values(puntosAsignados.value).reduce((a, b) => a + b, 0);
});

const distribucionValida = computed(() => sumaDistribucion.value === 100);

const criteriosFiltrados = computed(() => {
  if (!modalidadActiva.value || !tipoCriterioActivo.value) return [];
  return criterios.value.filter(c => 
    c.modalidad_id == modalidadActiva.value && 
    c.tipo_criterio == tipoCriterioActivo.value
  );
});

const sumaPorTipo = computed(() => {
  const sumas = {};
  if (!modalidadActiva.value) return sumas;

  tiposCriterio.value.forEach(tipo => {
    sumas[tipo.value] = criterios.value
      .filter(c => c.modalidad_id == modalidadActiva.value && c.tipo_criterio == tipo.value)
      .reduce((sum, c) => sum + (parseFloat(c.puntaje_maximo) || 0), 0);
  });
  return sumas;
});

const sumaPorModalidad = computed(() => {
  const sumas = {};
  props.modalidades.forEach(modalidad => {
    sumas[modalidad.id] = criterios.value
      .filter(c => c.modalidad_id == modalidad.id)
      .reduce((sum, c) => sum + (parseFloat(c.puntaje_maximo) || 0), 0);
  });
  return sumas;
});

const modalidadCompleta = computed(() => {
  return distribucionValida.value && 
         tiposCriterio.value.every(tipo => {
           return sumaPorTipo.value[tipo.value] === puntosAsignados.value[tipo.value] && 
                  puntosAsignados.value[tipo.value] > 0;
         });
});

const puedeGuardarTipo = computed(() => {
  return sumaPorTipo.value[tipoCriterioActivo.value] === puntosAsignados.value[tipoCriterioActivo.value];
});

// Métodos
const cargarDatosIniciales = () => {
  if (!concursoId.value) return;
  
  // Cargar criterios existentes para este concurso
  criterios.value = props.criteriosExistentes
    .filter(c => c.concurso_id == concursoId.value)
    .map(c => ({ ...c }));

  // Identificar modalidades guardadas y completas
  modalidadesGuardadas.value = [...new Set(
    props.criteriosExistentes
      .filter(c => c.concurso_id == concursoId.value)
      .map(c => c.modalidad_id)
  )];

  // Calcular modalidades completas
  props.modalidades.forEach(modalidad => {
    const puntosModalidad = { informe: 0, modalidad: 0, exposicion: 0 };
    let totalPuntos = 0;
    
    tiposCriterio.value.forEach(tipo => {
      const suma = criterios.value
        .filter(c => c.modalidad_id == modalidad.id && c.tipo_criterio == tipo.value)
        .reduce((sum, c) => sum + (parseFloat(c.puntaje_maximo) || 0), 0);
      
      puntosModalidad[tipo.value] = suma;
      totalPuntos += suma;
    });

    puntosPorModalidad.value[modalidad.id] = puntosModalidad;
    
    if (totalPuntos === 100) {
      modalidadesCompletas.value = [...new Set([...modalidadesCompletas.value, modalidad.id])];
    }
  });

  // Si solo hay una modalidad, seleccionarla automáticamente
  if (props.modalidades.length === 1) {
    cambiarModalidad(props.modalidades[0].id);
  }
};

const cambiarModalidad = (id) => {
  // Guardar los puntos actuales antes de cambiar de modalidad
  if (modalidadActiva.value) {
    puntosPorModalidad.value[modalidadActiva.value] = { ...puntosAsignados.value };
    
    // Actualizar estado de completitud
    if (modalidadCompleta.value) {
      modalidadesCompletas.value = [...new Set([...modalidadesCompletas.value, modalidadActiva.value])];
    } else {
      modalidadesCompletas.value = modalidadesCompletas.value.filter(m => m !== modalidadActiva.value);
    }
  }
  
  modalidadActiva.value = id;
  
  // Restaurar puntos para esta modalidad
  if (puntosPorModalidad.value[id]) {
    puntosAsignados.value = { ...puntosPorModalidad.value[id] };
  } else {
    puntosAsignados.value = { informe: 0, modalidad: 0, exposicion: 0 };
  }
  
  // Filtrar criterios para esta modalidad
  criterios.value = props.criteriosExistentes
    .filter(c => c.concurso_id == concursoId.value && c.modalidad_id == id)
    .map(c => ({ ...c }));
};

const validarDistribucion = () => {
  const total = sumaDistribucion.value;
  
  if (total > 100) {
    mostrarMensaje(`La suma (${total}) excede 100 puntos`, 'error');
    
    // Ajuste automático proporcional
    const totalActual = total;
    Object.keys(puntosAsignados.value).forEach(key => {
      puntosAsignados.value[key] = Math.round((puntosAsignados.value[key] / totalActual) * 100);
    });
    
    // Ajustar diferencia por redondeo
    const diferencia = 100 - sumaDistribucion.value;
    if (diferencia !== 0) {
      puntosAsignados.value.informe += diferencia;
    }
  }
};

const autoDistribuir = () => {
  const baseValue = Math.floor(100 / tiposCriterio.value.length);
  tiposCriterio.value.forEach((tipo, index) => {
    puntosAsignados.value[tipo.value] = index === tiposCriterio.value.length - 1 
      ? 100 - (baseValue * (tiposCriterio.value.length - 1))
      : baseValue;
  });
};

const agregarCriterio = () => {
  if (!modalidadActiva.value || !tipoCriterioActivo.value) return;
  
  const puntosDisponibles = puntosAsignados.value[tipoCriterioActivo.value] - 
                          (sumaPorTipo.value[tipoCriterioActivo.value] || 0);
  
  if (puntosDisponibles <= 0) {
    mostrarMensaje('No hay puntos disponibles para agregar más criterios en este tipo', 'error');
    return;
  }
  
  criterios.value.push({
    nombre: '',
    puntaje_maximo: Math.min(10, puntosDisponibles),
    modalidad_id: modalidadActiva.value,
    tipo_criterio: tipoCriterioActivo.value,
    concurso_id: concursoId.value
  });
};

const eliminarCriterio = (idOrIndex) => {
  if (typeof idOrIndex === 'number') {
    criterios.value.splice(idOrIndex, 1);
  } else {
    criterios.value = criterios.value.filter(c => c.id !== idOrIndex);
  }
};

const validarPuntajes = () => {
  const sumaActual = sumaPorTipo.value[tipoCriterioActivo.value];
  const maxPermitido = puntosAsignados.value[tipoCriterioActivo.value];
  
  if (sumaActual > maxPermitido) {
    mostrarMensaje(
      `La suma no puede exceder ${maxPermitido} puntos para este tipo (actual: ${sumaActual})`,
      'error'
    );
  }
};

const guardarTipoCriterio = async () => {
  const puntosEsperados = puntosAsignados.value[tipoCriterioActivo.value];
  const sumaActual = sumaPorTipo.value[tipoCriterioActivo.value];
  
  if (sumaActual !== puntosEsperados) {
    mostrarMensaje(
      `Los criterios deben sumar exactamente ${puntosEsperados} puntos para este tipo`,
      'error'
    );
    return;
  }

  guardando.value = true;

  try {
    await router.post(route('criterios.storeTipo'), {
      concurso_id: concursoId.value,
      modalidad_id: modalidadActiva.value,
      tipo_criterio: tipoCriterioActivo.value,
      criterios: criteriosFiltrados.value,
      puntos_asignados: puntosAsignados.value
    }, {
      onSuccess: () => {
        puntosPorModalidad.value[modalidadActiva.value] = { ...puntosAsignados.value };
        modalidadesGuardadas.value = [...new Set([...modalidadesGuardadas.value, modalidadActiva.value])];
        
        mostrarMensaje(
          `Criterios de ${tiposCriterio.value.find(t => t.value === tipoCriterioActivo.value)?.label} guardados exitosamente`,
          'exito'
        );
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

const guardarTodosCriterios = async () => {
  if (!modalidadCompleta.value) {
    mostrarMensaje('¡Debes completar correctamente todos los tipos de criterio primero!', 'error');
    return;
  }

  guardando.value = true;
  
  try {
    const response = await axios.post(route('criterios.storeModalidad'), {
      concurso_id: concursoId.value,
      modalidad_id: modalidadActiva.value,
      criterios: criterios.value.filter(c => c.modalidad_id == modalidadActiva.value),
      puntos_asignados: puntosAsignados.value
    });

    if (response.data.success) {
      modalidadesCompletas.value = [...new Set([...modalidadesCompletas.value, modalidadActiva.value])];
      mostrarMensaje('¡Todos los criterios guardados exitosamente!', 'exito');
    } else {
      throw new Error(response.data.message || 'Error desconocido al guardar');
    }
  } catch (error) {
    console.error('Error detallado:', error);
    mostrarMensaje(
      `Error al guardar: ${error.response?.data?.message || error.message}`,
      'error'
    );
  } finally {
    guardando.value = false;
  }
}

const mostrarMensaje = (texto, tipo) => {
  mensaje.value = { texto, tipo };
  setTimeout(() => { mensaje.value = null; }, 5000);
};

// Watchers
watch(concursoId, cargarDatosIniciales);

// Inicialización
onMounted(() => {
  if (props.concursos.length === 1) {
    concursoId.value = props.concursos[0].id;
  }
});

const handleMenuSelected = (menu) => {
  if (menu === 'Concursos') {
    router.get(route('concursos.index'));
  }
};
</script>


<style scoped>
input[type="number"] {
  appearance: textfield;
  -moz-appearance: textfield;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.distribution-input {
  transition: all 0.2s ease;
}

.distribution-input:focus {
  border-color: #611232;
  box-shadow: 0 0 0 1px #611232;
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

button:disabled {
  cursor: not-allowed;
}
</style>