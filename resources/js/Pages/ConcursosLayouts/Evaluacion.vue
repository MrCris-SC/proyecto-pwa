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
                <span class="inline-block bg-[#611232]/10 px-3 py-1 rounded-lg mr-2"></span>
                Evaluación de Proyectos
              </h1>
              <p class="text-gray-600 mt-1">Complete la evaluación para cada criterio según corresponda</p>
            </div>

            <!-- Selector de proyecto -->
            <div class="bg-gray-50 p-6 border-b border-gray-200">
              <div class="max-w-3xl mx-auto">
                <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">
                  Seleccione proyecto a evaluar
                </label>
                <div class="relative">
                  <select 
                    v-model="proyectoSeleccionadoId" 
                    @change="cambiarProyecto"
                    class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#611232] focus:border-[#611232] appearance-none bg-white"
                  >
                    <option value="" disabled>-- Seleccione un proyecto --</option>
                    <option 
                      v-for="proyecto in evaluacionesPendientes" 
                      :key="proyecto.id" 
                      :value="proyecto.id"
                      class="py-2"
                    >
                      {{ proyecto.nombre_proyecto }} ({{ parseModalidad(proyecto.modalidad) }})
                    </option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Información del proyecto -->
            <div v-if="proyectoSeleccionado" class="p-6">
              <div class="max-w-6xl mx-auto">
                <!-- Tarjetas de información -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
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
                        <span class="text-gray-900">{{ parseModalidad(proyectoSeleccionado.modalidad) }}</span>
                      </p>
                    </div>
                  </div>

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
                        <span 
                          v-if="lineasValidas.includes(proyectoSeleccionado.linea_investigacion)"
                          class="ml-3 px-2.5 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded-full flex items-center"
                          title="Coincide con las líneas PROIDET"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                          </svg>
                          Válida
                        </span>
                        <span 
                          v-else 
                          class="ml-3 px-2.5 py-0.5 bg-red-100 text-red-800 text-xs font-medium rounded-full flex items-center"
                          title="No coincide con las líneas PROIDET"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                          </svg>
                          Inválida
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Tabla de criterios -->
                <div class="mb-10">
                  <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                      <span class="bg-[#611232]/10 p-2 rounded-lg mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#611232]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                      </span>
                      Criterios de Evaluación
                    </h3>
                    <div class="text-sm text-gray-500">
                      Total: {{ proyectoSeleccionado.criterios.length }} criterios
                    </div>
                  </div>

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
                        <tr v-for="(criterio, index) in proyectoSeleccionado.criterios" :key="criterio.id" 
                            :class="{'bg-gray-50/50': index % 2 === 0}">
                          <td class="px-6 py-4">
                            <div class="flex items-start">
                              <div class="flex-shrink-0 h-10 w-10 rounded-full bg-[#611232]/10 flex items-center justify-center mr-4 mt-0.5">
                                <span class="text-[#611232] font-medium">{{ index + 1 }}</span>
                              </div>
                              <div>
                                <div class="flex items-center">
                                  <span class="font-medium text-gray-900">{{ criterio.nombre }}</span>
                                  <button 
                                    v-if="criterioTooltips[criterio.nombre]"
                                    class="ml-2 text-gray-400 hover:text-gray-500 focus:outline-none"
                                    :title="criterioTooltips[criterio.nombre]"
                                    @click="showTooltip(criterio.nombre)"
                                  >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                  </button>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ criterio.descripcion }}</p>
                              </div>
                            </div>
                          </td>
                          <td class="px-6 py-4 text-center font-medium text-gray-900">
                            {{ criterio.puntaje_maximo.toFixed(2) }} pts
                          </td>
                          <td class="px-6 py-4 text-center">
                            <div class="flex justify-center">
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
                            <div v-if="erroresPuntajes[criterio.id]" class="text-red-500 text-xs mt-1">
                              {{ erroresPuntajes[criterio.id] }}
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Resumen y observaciones -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                  <!-- Resumen de puntaje -->
                  <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#611232] mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                      Resumen de Puntuación
                    </h4>
                    <div class="space-y-4">
                      <div>
                        <div class="flex justify-between items-baseline mb-1">
                          <span class="text-sm font-medium text-gray-700">Puntaje obtenido</span>
                          <span class="text-lg font-bold" :class="{
                            'text-orange-500': totalPuntos < 80,
                            'text-green-600': totalPuntos >= 80
                          }">
                            {{ totalPuntos.toFixed(2) }}
                          </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                          <div 
                            class="h-2.5 rounded-full" 
                            :class="{
                              'bg-orange-500': totalPuntos < 80,
                              'bg-green-600': totalPuntos >= 80
                            }"
                            :style="`width: ${Math.min(100, (totalPuntos / puntajeMaximoTotal) * 100)}%`"
                          ></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                          <span>0</span>
                          <span>{{ puntajeMaximoTotal.toFixed(2) }} (máximo)</span>
                        </div>
                      </div>
                      
                      <div v-if="totalPuntos < 80" class="bg-orange-50 border-l-4 border-orange-500 p-4">
                        <div class="flex">
                          <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-500" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                          </div>
                          <div class="ml-3">
                            <p class="text-sm text-orange-700">
                              El proyecto no alcanza el mínimo recomendado de 80 puntos.
                            </p>
                          </div>
                        </div>
                      </div>
                      <div v-else class="bg-green-50 border-l-4 border-green-500 p-4">
                        <div class="flex">
                          <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                          </div>
                          <div class="ml-3">
                            <p class="text-sm text-green-700">
                              El proyecto cumple con el puntaje recomendado.
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Comentarios -->
                  <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#611232] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                      </svg>
                      Observaciones y Comentarios
                    </h4>
                    <div class="h-full">
                      <textarea 
                        v-model="comentarios" 
                        class="w-full h-40 border border-gray-300 rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-[#611232] focus:border-[#611232]" 
                        placeholder="Escriba aquí sus observaciones sobre el proyecto..."
                      ></textarea>
                      <p class="text-xs text-gray-500 mt-2">
                        Incluya comentarios detallados que justifiquen su evaluación.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                  <button 
                    @click="guardarEvaluacion(true)"
                    :disabled="loading"
                    class="inline-flex items-center px-5 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#611232] disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    {{ loading ? 'Guardando...' : 'Guardar Borrador' }}
                  </button>
                  <button 
                    @click="confirmarEnvio"
                    class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-[#611232] hover:bg-[#4a0d24] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#611232] disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="loading"  
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
                </div>
              </div>
            </div>

            <!-- Sin proyectos asignados -->
            <div v-else class="text-center py-16 bg-gray-50 rounded-b-lg">
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
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  evaluacionesPendientes: Array,
  lineasInvestigacion: Array
});

const userRole = 'evaluador';

// Datos de evaluación
const proyectoSeleccionadoId = ref(null);
const proyectoSeleccionado = ref(null);
const puntajes = ref({});
const comentarios = ref('');
const erroresPuntajes = ref({});
const loading = ref(false);

// Tooltips para criterios
const criterioTooltips = ref({
  'Originalidad': 'El proyecto debe ser original y no contener plagio (Base IX)',
  'Innovación': 'Debe demostrar aporte científico o tecnológico (Base X)',
  'Impacto social': 'Debe resolver un problema detectado en el plantel/comunidad (Base X)',
  'Factibilidad técnica': 'Debe ser técnicamente viable (Base X)',
  'Calidad académica': 'Debe mostrar rigor metodológico (Base X)'
});

// Líneas válidas de investigación
const lineasValidas = computed(() => props.lineasInvestigacion || []);

// Parsear modalidad (puede venir como string JSON o como objeto)
const parseModalidad = (modalidad) => {
  if (typeof modalidad === 'string' && modalidad.startsWith('{')) {
    try {
      const parsed = JSON.parse(modalidad);
      return parsed.nombre || modalidad;
    } catch {
      return modalidad;
    }
  }
  return modalidad?.nombre || modalidad;
};

// Calcular puntaje total
const totalPuntos = computed(() => {
  return Object.values(puntajes.value).reduce((sum, puntaje) => sum + (parseFloat(puntaje) || 0), 0);
});

// Calcular puntaje máximo total
const puntajeMaximoTotal = computed(() => {
  if (!proyectoSeleccionado.value) return 0;
  return proyectoSeleccionado.value.criterios.reduce((sum, criterio) => sum + parseFloat(criterio.puntaje_maximo), 0);
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

const validarFormulario = () => {
  if (!proyectoSeleccionado.value) return false;
  
  // Verificar que todos los criterios tengan puntaje
  for (const criterio of proyectoSeleccionado.value.criterios) {
    if (puntajes.value[criterio.id] === null || puntajes.value[criterio.id] === undefined || puntajes.value[criterio.id] === '') {
      return false;
    }
  }
  
  return true;
};

const confirmarEnvio = () => {
  if (!validarFormulario()) {
    alert('Por favor complete todos los puntajes antes de enviar');
    return;
  }

  // Mostrar advertencia pero permitir enviar de todos modos
  if (totalPuntos.value < 80) {
    if (!confirm('ADVERTENCIA: El proyecto no alcanza el mínimo recomendado de 80 puntos. ¿Desea enviar igualmente?')) {
      return;
    }
  }
  
  guardarEvaluacion(false);
};

const guardarEvaluacion = async (esBorrador) => {
  if (!validarFormulario()) {
    alert('Por favor complete todos los puntajes antes de guardar');
    return;
  }

  loading.value = true;
  
  try {
    const criteriosPuntajes = [];
    
    for (const [criterioId, puntaje] of Object.entries(puntajes.value)) {
      criteriosPuntajes.push({
        criterio_id: parseInt(criterioId),
        puntaje: parseFloat(puntaje) || 0
      });
    }
    
    await router.post(route('evaluacion.guardar', proyectoSeleccionadoId.value), {
      puntajes: criteriosPuntajes,
      comentarios: comentarios.value,
      es_borrador: esBorrador
    });
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

// Inicialización
onMounted(() => {
  if (props.evaluacionesPendientes.length > 0) {
    proyectoSeleccionadoId.value = props.evaluacionesPendientes[0].id;
    cambiarProyecto();
  }
});
</script>
