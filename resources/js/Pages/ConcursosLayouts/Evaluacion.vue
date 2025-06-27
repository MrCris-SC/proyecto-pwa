<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Módulo de Evaluación
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
      
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <!-- Encabezado -->
          <div class="bg-white border-b border-gray-200 p-6">
            <h1 class="text-2xl font-bold text-[#611232]">
              Evaluación de Proyectos
            </h1>
            <p class="text-gray-600 mt-1">Complete la evaluación para cada criterio según corresponda</p>
            
            <!-- Resumen de estado -->
            <div class="flex items-center mt-4 space-x-4 text-sm">
              <div class="flex items-center">
                <span class="w-3 h-3 rounded-full bg-[#611232] mr-2"></span>
                <span>{{ evaluacionesPendientesCount }} pendientes</span>
              </div>
              <div class="flex items-center">
                <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                <span>{{ evaluacionesCompletadasCount }} completadas</span>
              </div>
            </div>
          </div>

          <!-- Selector de proyecto con loader -->
          <div class="bg-gray-50 p-6 border-b border-gray-200 relative">
            <div class="max-w-3xl mx-auto">
              <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">
                Seleccione proyecto a evaluar
              </label>
              <div class="relative">
                <select 
                  v-model="proyectoSeleccionadoId" 
                  @change="cambiarProyecto"
                  class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#611232] focus:border-[#611232] appearance-none bg-white"
                  :disabled="cargandoProyecto"
                >
                  <option value="" disabled>-- Seleccione un proyecto --</option>
                  <optgroup label="Pendientes de evaluación" v-if="proyectosPendientes.length > 0">
                    <option 
                      v-for="proyecto in proyectosPendientes" 
                      :key="proyecto.id" 
                      :value="proyecto.id"
                      class="py-2 font-medium"
                    >
                      {{ proyecto.nombre_proyecto }} ({{ proyecto.modalidad_nombre }})
                    </option>
                  </optgroup>
                  <optgroup label="Evaluaciones completadas" v-if="proyectosCompletados.length > 0">
                    <option 
                      v-for="proyecto in proyectosCompletados" 
                      :key="proyecto.id" 
                      :value="proyecto.id"
                      class="py-2 text-gray-500"
                    >
                      {{ proyecto.nombre_proyecto }} ({{ proyecto.modalidad_nombre }}) - ✔️ Completado
                    </option>
                  </optgroup>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>
            <div v-if="cargandoProyecto" class="absolute inset-0 bg-white/70 flex items-center justify-center">
              <svg class="animate-spin h-6 w-6 text-[#611232]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
          </div>

          <!-- Contenido principal -->
          <div v-if="proyectoSeleccionado" class="p-6">
            <div class="max-w-6xl mx-auto">
              <!-- Tarjetas de información del proyecto -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Tarjeta 1: Detalles del proyecto -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                  <div class="flex items-center mb-3">
                    <div class="p-2 rounded-full bg-[#611232]/10 text-[#611232] mr-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                      </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Detalles del Proyecto</h3>
                  </div>
                  <div class="space-y-2 pl-11">
                    <p class="flex items-start">
                      <span class="font-medium text-gray-700 w-24 flex-shrink-0">Nombre:</span>
                      <span class="text-gray-900">{{ proyectoSeleccionado.nombre_proyecto }}</span>
                    </p>
                    <p class="flex items-start">
                      <span class="font-medium text-gray-700 w-24 flex-shrink-0">Modalidad:</span>
                      <span class="text-gray-900">{{ proyectoSeleccionado.modalidad_nombre }}</span>
                    </p>
                    <p class="flex items-start" v-if="proyectoSeleccionado.ya_evaluado">
                      <span class="font-medium text-gray-700 w-24 flex-shrink-0">Estado:</span>
                      <span class="text-green-600 font-medium">Evaluación completada</span>
                    </p>
                  </div>
                </div>

                <!-- Tarjeta 2: Línea de investigación -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                  <div class="flex items-center mb-3">
                    <div class="p-2 rounded-full bg-[#611232]/10 text-[#611232] mr-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                      </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Línea de Investigación</h3>
                  </div>
                  <div class="pl-11">
                    <div class="flex items-center">
                      <span class="text-gray-900">{{ proyectoSeleccionado.linea_investigacion }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sección de pestañas y progreso -->
              <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6">
                <!-- Encabezado -->
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                  <h4 class="font-semibold text-gray-800">Progreso de Evaluación</h4>
                  <div class="flex items-center text-sm">
                    <span class="mr-4">Total: {{ porcentajeTotal.toFixed(0) }}%</span>
                    <div class="w-24 bg-gray-200 rounded-full h-2">
                      <div class="h-2 rounded-full bg-[#611232]" 
                          :style="`width: ${porcentajeTotal}%`"></div>
                    </div>
                  </div>
                </div>

                <!-- Pestañas -->
                <div class="px-4 pt-2">
                  <div class="flex border-b border-gray-200 space-x-1">
                    <button
                      v-for="tipo in tiposCriterio"
                      :key="tipo.value"
                      @click="tipoActivo = tipo.value"
                      class="px-4 py-2 text-sm font-medium relative group"
                      :class="{
                        'text-[#611232]': tipoActivo === tipo.value,
                        'text-gray-500 hover:text-gray-700': tipoActivo !== tipo.value
                      }"
                    >
                      {{ tipo.label }}
                      <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-transparent group-hover:bg-gray-200 transition-colors"
                          :class="{'bg-[#611232]': tipoActivo === tipo.value}"></div>
                      
                      <!-- Indicador de progreso por tipo -->
                      <div class="absolute -bottom-1 left-0 right-0 h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-300"
                            :class="{
                              'bg-green-500': porcentajePorTipo[tipo.value] >= 70,
                              'bg-yellow-500': porcentajePorTipo[tipo.value] < 70 && porcentajePorTipo[tipo.value] >= 50,
                              'bg-red-500': porcentajePorTipo[tipo.value] < 50
                            }"
                            :style="`width: ${porcentajePorTipo[tipo.value]}%`"></div>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Resumen por tipo -->
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <h5 class="text-sm font-medium text-gray-700 mb-1">Puntuación Obtenida</h5>
                      <p class="text-lg font-bold">
                        {{ (criteriosPorTipo[tipoActivo]?.total || 0).toFixed(1) }} / 
                        {{ (criteriosPorTipo[tipoActivo]?.maximo || 0).toFixed(1) }} pts
                      </p>
                    </div>
                    <div>
                      <h5 class="text-sm font-medium text-gray-700 mb-1">Porcentaje</h5>
                      <div class="flex items-center">
                        <span class="text-lg font-bold mr-2">{{ porcentajePorTipo[tipoActivo].toFixed(0) }}%</span>
                        <span class="text-xs px-2 py-1 rounded-full"
                              :class="{
                                'bg-green-100 text-green-800': porcentajePorTipo[tipoActivo] >= 70,
                                'bg-yellow-100 text-yellow-800': porcentajePorTipo[tipoActivo] < 70 && porcentajePorTipo[tipoActivo] >= 50,
                                'bg-red-100 text-red-800': porcentajePorTipo[tipoActivo] < 50
                              }">
                          {{ porcentajePorTipo[tipoActivo] >= 70 ? 'Excelente' : 
                            porcentajePorTipo[tipoActivo] >= 50 ? 'Aceptable' : 'Necesita mejora' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Botón de guardar sección -->
                  <div class="mt-4 flex justify-end">
                    <button
                      v-if="!proyectoSeleccionado.ya_evaluado"
                      @click="guardarTipo(tipoActivo)"
                      class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-[#611232] hover:bg-[#4a0d24] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#611232] disabled:opacity-50"
                      :disabled="loading"
                    >
                      <svg v-if="loading" class="animate-spin -ml-1 mr-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ loading ? 'Guardando...' : 'Guardar ' + tiposCriterio.find(t => t.value === tipoActivo)?.label }}
                    </button>
                  </div>
                </div>
              </div>

              <!-- Lista de criterios -->
              <div v-if="criteriosPorTipo[tipoActivo]?.criterios?.length > 0" class="mb-6">
                <div class="overflow-hidden border border-gray-200 rounded-xl shadow-sm">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                          Criterio
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider w-32">
                          Puntaje Máx.
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider w-40">
                          Puntaje Asignado
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="(criterio, index) in criteriosPorTipo[tipoActivo].criterios" :key="criterio.id" 
                          :class="{
                            'bg-gray-50/50': index % 2 === 0,
                            'opacity-75': proyectoSeleccionado.ya_evaluado
                          }">
                        <td class="px-6 py-4">
                          <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-[#611232]/10 flex items-center justify-center mr-4 mt-0.5">
                              <span class="text-[#611232] font-medium">{{ index + 1 }}</span>
                            </div>
                            <div>
                              <div class="flex items-center">
                                <span class="font-medium text-gray-900">{{ criterio.nombre }}</span>
                              </div>
                              <p class="text-sm text-gray-500 mt-1">{{ criterio.descripcion }}</p>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 text-center font-medium text-gray-900">
                          {{ criterio.puntaje_maximo.toFixed(2) }} pts
                        </td>
                        <td class="px-6 py-4 text-center">
                          <div v-if="!proyectoSeleccionado.ya_evaluado" class="flex justify-center">
                            <div class="relative w-32">
                              <input 
                                type="number" 
                                v-model.number="puntajes[criterio.id]"
                                :max="criterio.puntaje_maximo"
                                min="0"
                                step="0.01"
                                class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#611232] focus:border-[#611232] text-center"
                                @change="validarPuntaje(criterio)"
                                placeholder="0.00"
                              >
                              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 text-sm">pts</span>
                              </div>
                            </div>
                          </div>
                          <div v-else class="text-center font-medium">
                            {{ puntajes[criterio.id] || 0 }} pts
                          </div>
                          <div v-if="erroresPuntajes[criterio.id]" class="text-red-500 text-xs mt-1">
                            {{ erroresPuntajes[criterio.id] }}
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div v-else class="text-center py-8 text-gray-500">
                No hay criterios definidos para este tipo
              </div>

              <!-- Comentarios -->
              <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-8">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#611232] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                  </svg>
                  Observaciones y Comentarios
                </h4>
                <div class="h-full">
                  <textarea 
                    v-model="comentarios" 
                    :readonly="proyectoSeleccionado.ya_evaluado"
                    :class="{'bg-gray-100': proyectoSeleccionado.ya_evaluado}"
                    class="w-full h-40 border border-gray-300 rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-[#611232] focus:border-[#611232]" 
                    placeholder="Escriba aquí sus observaciones sobre el proyecto..."
                  ></textarea>
                  <p class="text-xs text-gray-500 mt-2">
                    Incluya comentarios detallados que justifiquen su evaluación.
                  </p>
                </div>
              </div>

              <!-- Botón de enviar evaluación final -->
              <div class="flex justify-end">
                <button 
                  v-if="!proyectoSeleccionado.ya_evaluado"
                  @click="confirmarEnvio"
                  class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-[#611232] hover:bg-[#4a0d24] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#611232] disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="loading || !formularioValido"  
                >
                  <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  {{ loading ? 'Enviando...' : 'Enviar Evaluación Final' }}
                </button>
                <div v-else class="bg-green-100 text-green-800 px-4 py-2 rounded-lg flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Evaluación final enviada
                </div>
              </div>
            </div>
          </div>

          <!-- Sin proyectos asignados -->
          <div v-else-if="evaluacionesPendientes.length === 0" class="text-center py-16 bg-gray-50 rounded-b-lg">
            <div class="mx-auto max-w-md px-4">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <h3 class="mt-2 text-lg font-medium text-gray-900">No hay proyectos asignados</h3>
              <p class="mt-1 text-sm text-gray-500">
                Actualmente no tienes proyectos asignados para evaluar. Por favor, revisa más tarde.
              </p>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Modal de confirmación para enviar evaluación final -->
    <div v-if="mostrarModalConfirmacion" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
        <h3 class="text-lg font-semibold text-[#611232] mb-2">Confirmar envío</h3>
        <p class="text-gray-700 mb-4">
          ¿Estás seguro de enviar la evaluación? <br>
          <span class="font-semibold text-red-600">Una vez enviada la Evaluación Final ya no se podrá editar.</span>
        </p>
        <div class="flex justify-end space-x-2 mt-6">
          <button
            @click="cerrarModal"
            class="px-4 py-2 rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition"
          >Cancelar</button>
          <button
            @click="confirmarEnvioFinal"
            class="px-4 py-2 rounded bg-[#611232] text-white font-semibold hover:bg-[#4a0d24] transition"
          >Sí, enviar evaluación</button>
        </div>
        <button @click="cerrarModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  evaluacionesPendientes: Array,
  tiposCriterio: Array
});

const userRole = 'evaluador';

// Estado del componente
const proyectoSeleccionadoId = ref(null);
const proyectoSeleccionado = ref(null);
const puntajes = ref({});
const comentarios = ref('');
const erroresPuntajes = ref({});
const loading = ref(false);
const cargandoProyecto = ref(false);
const tipoActivo = ref('informe');
const mostrarModalConfirmacion = ref(false);

// Computed properties
const proyectosPendientes = computed(() => {
  return props.evaluacionesPendientes.filter(p => !p.evaluacion_completa);
});

const proyectosCompletados = computed(() => {
  return props.evaluacionesPendientes.filter(p => p.evaluacion_completa);
});

const evaluacionesPendientesCount = computed(() => proyectosPendientes.value.length);
const evaluacionesCompletadasCount = computed(() => proyectosCompletados.value.length);

const totalPuntos = computed(() => {
  return Object.values(puntajes.value).reduce((sum, puntaje) => sum + (parseFloat(puntaje) || 0), 0);
});

const criteriosPorTipo = computed(() => {
  if (!proyectoSeleccionado.value) return {};
  
  const agrupados = {};
  props.tiposCriterio.forEach(tipo => {
    agrupados[tipo.value] = {
      label: tipo.label,
      criterios: proyectoSeleccionado.value.criterios
        .filter(c => c.tipo_criterio === tipo.value),
      total: 0,
      maximo: 0
    };
    
    // Calcular totales
    agrupados[tipo.value].total = agrupados[tipo.value].criterios
      .reduce((sum, c) => sum + (parseFloat(puntajes.value[c.id]) || 0), 0);
      
    agrupados[tipo.value].maximo = agrupados[tipo.value].criterios
      .reduce((sum, c) => sum + parseFloat(c.puntaje_maximo), 0);
  });
  
  return agrupados;
});

const porcentajePorTipo = computed(() => {
  const porcentajes = {};
  props.tiposCriterio.forEach(tipo => {
    const total = criteriosPorTipo.value[tipo.value]?.total || 0;
    const maximo = criteriosPorTipo.value[tipo.value]?.maximo || 1;
    porcentajes[tipo.value] = (total / maximo) * 100;
  });
  return porcentajes;
});

const porcentajeTotal = computed(() => {
  let total = 0;
  let maximo = 0;
  
  props.tiposCriterio.forEach(tipo => {
    total += criteriosPorTipo.value[tipo.value]?.total || 0;
    maximo += criteriosPorTipo.value[tipo.value]?.maximo || 0;
  });
  
  return maximo > 0 ? (total / maximo) * 100 : 0;
});

const formularioValido = computed(() => {
  if (!proyectoSeleccionado.value) return false;
  // Verificar que todos los criterios tengan puntaje válido
  for (const criterio of proyectoSeleccionado.value.criterios) {
    const puntaje = parseFloat(puntajes.value[criterio.id]);
    if (isNaN(puntaje)) return false;
    if (puntaje < 0 || puntaje > criterio.puntaje_maximo) return false;
  }
  // El comentario es obligatorio para enviar evaluación final
  if (!comentarios.value || comentarios.value.trim().length === 0) return false;
  return true;
});

// Métodos
const cambiarProyecto = async () => {
  if (!proyectoSeleccionadoId.value) return;

  cargandoProyecto.value = true;
  
  try {
    // Encontrar el proyecto seleccionado
    proyectoSeleccionado.value = props.evaluacionesPendientes.find(
      p => p.id === proyectoSeleccionadoId.value
    );
    
    resetearFormulario();
    
    // Cargar todos los puntajes existentes (completos o parciales)
    if (proyectoSeleccionado.value) {
      proyectoSeleccionado.value.criterios.forEach(criterio => {
        puntajes.value[criterio.id] = criterio.puntaje_asignado !== null 
          ? criterio.puntaje_asignado 
          : 0;
      });
      
      comentarios.value = proyectoSeleccionado.value.comentarios || '';
    }
  } finally {
    cargandoProyecto.value = false;
  }
};

const resetearFormulario = () => {
  erroresPuntajes.value = {};
};

const validarPuntaje = (criterio) => {
  const valor = parseFloat(puntajes.value[criterio.id]) || 0;
  
  if (valor < 0) {
    erroresPuntajes.value[criterio.id] = 'El puntaje no puede ser negativo';
    puntajes.value[criterio.id] = 0;
  } else if (valor > criterio.puntaje_maximo) {
    erroresPuntajes.value[criterio.id] = `Máximo permitido: ${criterio.puntaje_maximo.toFixed(2)}`;
    puntajes.value[criterio.id] = criterio.puntaje_maximo;
  } else {
    erroresPuntajes.value[criterio.id] = '';
  }
};

const guardarTipo = async (tipo) => {
  if (!proyectoSeleccionado.value) return;

  loading.value = true;
  
  try {
    const criteriosPuntajes = criteriosPorTipo.value[tipo].criterios.map(criterio => {
      return {
        criterio_id: criterio.id,
        puntaje: parseFloat(puntajes.value[criterio.id]) || 0
      };
    });

    const response = await axios.post(route('evaluacion.guardar', proyectoSeleccionadoId.value), {
      puntajes: criteriosPuntajes,
      comentarios: comentarios.value,
      tipo_criterio: tipo
    });

    // Actualizar el proyecto con los datos devueltos por el servidor
    if (response.data.success) {
      const evaluacionActualizada = response.data.evaluacion;
      const proyectoIndex = props.evaluacionesPendientes.findIndex(
        p => p.id === proyectoSeleccionadoId.value
      );
      
      if (proyectoIndex !== -1) {
        // Actualizar los criterios en el listado principal
        props.evaluacionesPendientes[proyectoIndex].criterios = evaluacionActualizada.criterios;
        props.evaluacionesPendientes[proyectoIndex].comentarios = evaluacionActualizada.comentarios;
        
        // Si cambió el estado de completado
        if (evaluacionActualizada.evaluacion_completa) {
          props.evaluacionesPendientes[proyectoIndex].evaluacion_completa = true;
          props.evaluacionesPendientes[proyectoIndex].ya_evaluado = true;
        }
      }
      
      // Actualizar el proyecto seleccionado
      proyectoSeleccionado.value.criterios = evaluacionActualizada.criterios;
      proyectoSeleccionado.value.comentarios = evaluacionActualizada.comentarios;
      
      // Cuando recibas la evaluación actualizada del backend:
      if (evaluacionActualizada.evaluacion_completa) {
        proyectoSeleccionado.value.evaluacion_completa = true;
        proyectoSeleccionado.value.ya_evaluado = true;
      }
    }
  } catch (error) {
    console.error('Error al guardar:', error);
  } finally {
    loading.value = false;
  }
};

const confirmarEnvio = () => {
  if (!formularioValido.value) return;

  if (totalPuntos.value < 80) {
    // Si el puntaje es bajo, puedes mostrar otro modal o mensaje, pero aquí seguimos con el mismo modal
    mostrarModalConfirmacion.value = true;
    return;
  }
  mostrarModalConfirmacion.value = true;
};

const cerrarModal = () => {
  mostrarModalConfirmacion.value = false;
};

const confirmarEnvioFinal = () => {
  mostrarModalConfirmacion.value = false;
  enviarEvaluacionFinal();
};

const enviarEvaluacionFinal = async () => {
  if (!formularioValido.value) return;

  loading.value = true;

  try {
    const response = await axios.post(route('evaluacion.enviar-final', proyectoSeleccionadoId.value), {
      comentarios: comentarios.value
    });

    if (response.data.success) {
      router.reload();
    } else {
      alert(response.data.message || 'No se pudo enviar la evaluación final.');
    }
  } catch (error) {
    // Mostrar mensaje de error detallado del backend
    let msg = 'Error al enviar la evaluación final.';
    if (error.response) {
      if (error.response.data && error.response.data.message) {
        msg = error.response.data.message;
      } else if (typeof error.response.data === 'string') {
        msg = error.response.data;
      }
    } else if (error.message) {
      msg = error.message;
    }
    alert(msg);
  } finally {
    loading.value = false;
  }
};

const guardarEvaluacion = async () => {
  if (!formularioValido.value) return;

  loading.value = true;
  
  try {
    const criteriosPuntajes = [];
    
    for (const [criterioId, puntaje] of Object.entries(puntajes.value)) {
      criteriosPuntajes.push({
        criterio_id: parseInt(criterioId),
        puntaje: parseFloat(puntaje) || 0
      });
    }
    
    const response = await axios.post(route('evaluacion.guardar', proyectoSeleccionadoId.value), {
      puntajes: criteriosPuntajes,
      comentarios: comentarios.value
    });

    if (response.data.success) {
      // Recargar la página para actualizar completamente los datos
      router.reload();
    }
  } finally {
    loading.value = false;
  }
};

const handleMenuSelected = (menu) => {
  if (menu === 'criterios') {
    router.visit(route('criterios.index'));
  } else if (menu === 'proyectos asignados') {
    router.visit(route('proyectos-asignados.index'));
  }
};

// Watchers
watch(proyectoSeleccionadoId, (newVal) => {
  if (newVal) {
    cambiarProyecto();
  }
});

watch(() => props.evaluacionesPendientes, (newEvaluaciones) => {
  if (proyectoSeleccionadoId.value) {
    // Actualizar el proyecto seleccionado si existe en los nuevos datos
    const proyectoActualizado = newEvaluaciones.find(
      p => p.id === proyectoSeleccionadoId.value
    );
    
    if (proyectoActualizado) {
      proyectoSeleccionado.value = proyectoActualizado;
      
      // Mantener los puntajes que ya estaban cargados
      proyectoActualizado.criterios.forEach(criterio => {
        if (puntajes.value[criterio.id] === undefined) {
          puntajes.value[criterio.id] = criterio.puntaje_asignado !== null 
            ? criterio.puntaje_asignado 
            : 0;
        }
      });
    }
  }
}, { deep: true });

// Inicialización
onMounted(() => {
  if (props.evaluacionesPendientes.length > 0) {
    // Seleccionar el primer proyecto pendiente por defecto, si existe
    const primerPendiente = proyectosPendientes.value[0] || props.evaluacionesPendientes[0];
    if (primerPendiente) {
      proyectoSeleccionadoId.value = primerPendiente.id;
      nextTick(() => {
        cambiarProyecto();
      });
    }
  }
});
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

button:disabled {
  cursor: not-allowed;
}

/* Transiciones para suavizar cambios */
.tab-indicator {
  transition: all 0.3s ease;
}

.progress-bar {
  transition: width 0.5s ease;
}
</style>