<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Módulo de Evaluación
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <MenuLateral @menu-selected="handleMenuSelected" />
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          Evaluación
        </h2>

        <div class="p-8 bg-white rounded-lg shadow-lg">
          <template v-if="currentView === 'evaluacion'">
            <div v-if="proyectoSeleccionado">
              <div class="flex justify-between items-start mb-6">
                <div>
                  <h3 class="text-lg font-semibold">Evaluando: {{ proyectoSeleccionado.nombre_proyecto }}</h3>
                  <p class="text-sm text-gray-600">
                    <span class="font-medium">Tipo:</span> {{ proyectoSeleccionado.tipo_proyecto }} | 
                    <span class="font-medium">Modalidad:</span> {{ proyectoSeleccionado.modalidad }} | 
                    <span class="font-medium">Línea investigación:</span> {{ proyectoSeleccionado.linea_investigacion }}
                  </p>
                </div>
                <select 
                  v-model="proyectoSeleccionadoId" 
                  @change="cambiarProyecto"
                  class="border rounded p-2 text-sm"
                >
                  <option v-for="proyecto in evaluacionesPendientes" :value="proyecto.id">
                    {{ proyecto.nombre_proyecto }}
                  </option>
                </select>
              </div>
              
              <!-- Sección de criterios de evaluación -->
              <div class="mb-8 space-y-6">
                <div v-for="criterio in proyectoSeleccionado.criterios" :key="criterio.id" class="border-b pb-4">
                  <div class="flex justify-between items-start">
                    <div>
                      <h5 class="font-semibold mb-1">
                        {{ criterio.nombre }} ({{ criterio.puntaje_maximo }} puntos)
                      </h5>
                      <p class="text-sm text-gray-600">{{ criterio.descripcion }}</p>
                    </div>
                    <input 
                      type="number" 
                      v-model="puntajes[criterio.id]"
                      :max="criterio.puntaje_maximo"
                      min="0"
                      class="mt-2 border rounded p-2 w-20"
                      @change="validarPuntaje(criterio)"
                    >
                  </div>
                  <div v-if="erroresPuntajes[criterio.id]" class="text-red-500 text-sm mt-1">
                    {{ erroresPuntajes[criterio.id] }}
                  </div>
                </div>
              </div>
              
              <!-- Comentarios adicionales -->
              <div class="mb-6">
                <label class="block font-semibold mb-2">Observaciones del Evaluador:</label>
                <textarea 
                  v-model="comentarios" 
                  class="w-full border rounded p-2 h-24" 
                  placeholder="Ingrese sus observaciones sobre el proyecto..."
                ></textarea>
              </div>
              
              <!-- Resumen de puntaje -->
              <div class="mt-6 p-4 bg-gray-100 rounded mb-6">
                <p class="font-semibold">Puntaje Total: {{ totalPuntos }}/{{ puntajeMaximoTotal }}</p>
                <p v-if="totalPuntos < 80" class="text-red-600 text-sm">
                  Nota: El proyecto necesita mínimo 80 puntos para pasar a la siguiente fase.
                </p>
                <p v-else class="text-green-600 text-sm">
                  El proyecto cumple con el puntaje mínimo requerido.
                </p>
              </div>
              
              <!-- Botones de acción -->
              <div class="flex justify-end space-x-4">
                <button 
                  @click="guardarEvaluacion(true)"
                  class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                >
                  Guardar Borrador
                </button>
                <button 
                  @click="confirmarEnvio"
                  class="px-4 py-2 bg-[#611232] text-white rounded hover:bg-[#4a0d24]"
                >
                  Enviar Evaluación Final
                </button>
              </div>
            </div>
            
            <div v-else class="text-center py-8">
              <p class="text-gray-600">No tienes proyectos asignados para evaluar en este momento.</p>
              <router-link to="/proyectos-asignados" class="text-[#611232] hover:underline">
                Ver mis proyectos asignados
              </router-link>
            </div>
          </template>
          
          <template v-else-if="currentView === 'proyectos'">
            <h3 class="text-lg font-semibold mb-6">Mis Proyectos Asignados</h3>
            
            <div v-if="evaluaciones.length > 0" class="space-y-4">
              <div v-for="evaluacion in evaluaciones" :key="evaluacion.id" class="border rounded-lg p-4">
                <div class="flex justify-between items-start">
                  <div>
                    <h4 class="font-bold">{{ evaluacion.proyecto.nombre }}</h4>
                    <p class="text-sm text-gray-600">
                      {{ evaluacion.proyecto.tipo }} | {{ evaluacion.proyecto.modalidad }} | 
                      {{ evaluacion.proyecto.linea_investigacion }}
                    </p>
                    <p class="text-sm mt-1">
                      <span class="font-medium">Concurso:</span> {{ evaluacion.concurso.nombre }} ({{ evaluacion.concurso.fase }})
                    </p>
                  </div>
                  <span 
                    :class="{
                      'bg-green-100 text-green-800': evaluacion.estado === 'completada',
                      'bg-yellow-100 text-yellow-800': evaluacion.estado === 'pendiente'
                    }" 
                    class="px-3 py-1 rounded-full text-xs font-medium"
                  >
                    {{ evaluacion.estado === 'completada' ? 'Evaluado' : 'Pendiente' }}
                  </span>
                </div>
                
                <div v-if="evaluacion.puntajes.length > 0" class="mt-4">
                  <h5 class="font-semibold mb-2">Puntajes:</h5>
                  <ul class="grid grid-cols-2 gap-2 text-sm">
                    <li v-for="puntaje in evaluacion.puntajes" class="flex justify-between">
                      <span>{{ puntaje.criterio.nombre }}:</span>
                      <span>{{ puntaje.puntaje }}/{{ puntaje.criterio.puntaje_maximo }}</span>
                    </li>
                  </ul>
                  <p class="mt-2 font-medium">
                    Total: {{ calcularTotal(evaluacion.puntajes) }}/{{ calcularMaximo(evaluacion.puntajes) }}
                  </p>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-8">
              <p class="text-gray-600">No tienes proyectos asignados para evaluar.</p>
            </div>
          </template>
          
          <template v-else-if="currentView === 'criterios'">
            <div class="space-y-6">
              <h3 class="text-lg font-semibold mb-4">Criterios Generales de Evaluación</h3>
              
              <div v-if="concurso" class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-bold text-[#611232] mb-2">Criterios para {{ concurso.nombre }}</h4>
                <ul class="space-y-3">
                  <li v-for="criterio in concurso.criterios" class="border-b pb-3 last:border-0">
                    <h5 class="font-semibold">{{ criterio.nombre }} ({{ criterio.puntaje_maximo }} puntos)</h5>
                    <p class="text-sm text-gray-600">{{ criterio.descripcion }}</p>
                  </li>
                </ul>
              </div>
              
              <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <h4 class="font-bold text-[#611232]">Consideraciones Importantes</h4>
                <ul class="list-disc pl-5 mt-2 space-y-2 text-sm">
                  <li>El proyecto debe ser <strong>original</strong> y no debe contener plagio.</li>
                  <li>Debe estar alineado con las <strong>líneas de investigación PROIDET</strong>.</li>
                  <li>Mínimo <strong>80 puntos</strong> para avanzar a fase nacional.</li>
                </ul>
              </div>

              <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h4 class="font-bold text-[#611232]">Líneas de Investigación PROIDET</h4>
                <ol class="list-decimal pl-5 mt-2 space-y-2 text-sm">
                  <li v-for="linea in lineasInvestigacion" :key="linea">
                    <strong>{{ linea }}</strong>
                  </li>
                </ol>
              </div>
            </div>
          </template>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  evaluacionesPendientes: Array,
  evaluaciones: Array,
  concurso: Object,
  lineasInvestigacion: Array
});

// Estado para controlar la vista actual
const currentView = ref('evaluacion');

// Datos de evaluación
const proyectoSeleccionadoId = ref(null);
const proyectoSeleccionado = ref(null);
const puntajes = ref({});
const comentarios = ref('');
const erroresPuntajes = ref({});

// Calcular puntaje total
const totalPuntos = computed(() => {
  return Object.values(puntajes.value).reduce((sum, puntaje) => sum + (parseInt(puntaje) || 0), 0);
});

// Calcular puntaje máximo total
const puntajeMaximoTotal = computed(() => {
  if (!proyectoSeleccionado.value) return 0;
  return proyectoSeleccionado.value.criterios.reduce((sum, criterio) => sum + criterio.puntaje_maximo, 0);
});

// Métodos
const cambiarProyecto = () => {
  proyectoSeleccionado.value = props.evaluacionesPendientes.find(
    p => p.id === proyectoSeleccionadoId.value
  );
  resetearFormulario();
};

const resetearFormulario = () => {
  puntajes.value = {};
  comentarios.value = '';
  erroresPuntajes.value = {};
  
  if (proyectoSeleccionado.value) {
    proyectoSeleccionado.value.criterios.forEach(criterio => {
      puntajes.value[criterio.id] = 0;
    });
  }
};

const validarPuntaje = (criterio) => {
  const valor = parseInt(puntajes.value[criterio.id]) || 0;
  
  if (valor < 0) {
    erroresPuntajes.value[criterio.id] = 'El puntaje no puede ser negativo';
    puntajes.value[criterio.id] = 0;
  } else if (valor > criterio.puntaje_maximo) {
    erroresPuntajes.value[criterio.id] = `El puntaje máximo para este criterio es ${criterio.puntaje_maximo}`;
    puntajes.value[criterio.id] = criterio.puntaje_maximo;
  } else {
    erroresPuntajes.value[criterio.id] = '';
  }
};

const confirmarEnvio = () => {
  if (totalPuntos.value < 80) {
    if (!confirm('El proyecto no alcanza el mínimo de 80 puntos. ¿Desea enviar igualmente?')) {
      return;
    }
  }
  
  guardarEvaluacion(false);
};

const guardarEvaluacion = (esBorrador) => {
  const criteriosPuntajes = [];
  
  for (const [criterioId, puntaje] of Object.entries(puntajes.value)) {
    criteriosPuntajes.push({
      criterio_id: parseInt(criterioId),
      puntaje: parseInt(puntaje) || 0
    });
  }
  
  router.post(route('evaluacion.guardar', proyectoSeleccionadoId.value), {
    puntajes: criteriosPuntajes,
    comentarios: comentarios.value,
    es_borrador: esBorrador
  });
};

const calcularTotal = (puntajes) => {
  return puntajes.reduce((sum, puntaje) => sum + puntaje.puntaje, 0);
};

const calcularMaximo = (puntajes) => {
  return puntajes.reduce((sum, puntaje) => sum + puntaje.criterio.puntaje_maximo, 0);
};

const handleMenuSelected = (menu) => {
  switch(menu) {
    case 'evaluación':
      currentView.value = 'evaluacion';
      break;
    case 'proyectos asignados':
      currentView.value = 'proyectos';
      break;
    case 'criterios':
      currentView.value = 'criterios';
      break;
    default:
      currentView.value = 'evaluacion';
  }
};

// Inicialización

onMounted(() => {
  console.log('Evaluaciones pendientes:', props.evaluacionesPendientes);
  console.log('Todas las evaluaciones:', props.evaluaciones);
  
  if (props.evaluacionesPendientes.length > 0) {
    proyectoSeleccionadoId.value = props.evaluacionesPendientes[0].id;
    cambiarProyecto();
  }
});
</script>