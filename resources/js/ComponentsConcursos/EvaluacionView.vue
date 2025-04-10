<template>
    <div>
      <!-- Selector de proyecto con estilo mejorado -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar proyecto:</label>
          <select 
            v-model="proyectoId" 
            @change="$emit('cambiar-proyecto', proyectoId)"
            class="block w-full md:w-96 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#611232] focus:border-[#611232]"
          >
            <option v-for="proyecto in evaluacionesPendientes" :value="proyecto.id">
              {{ proyecto.nombre_proyecto }} ({{ proyecto.tipo_proyecto }})
            </option>
          </select>
        </div>
        
        <div v-if="proyecto" class="bg-blue-50 p-3 rounded-lg border border-blue-200">
          <p class="text-sm font-medium text-blue-800">
            <span class="font-bold">Línea:</span> {{ proyecto.linea_investigacion }} | 
            <span class="font-bold">Modalidad:</span> {{ proyecto.modalidad }}
          </p>
        </div>
      </div>
  
      <!-- Panel de evaluación -->
      <div v-if="proyecto" class="space-y-6">
        <!-- Información del proyecto -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
          <h4 class="text-lg font-bold text-[#611232] mb-2">{{ proyecto.nombre_proyecto }}</h4>
          <p class="text-gray-600 mb-3">{{ proyecto.descripcion }}</p>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <span class="font-medium">Autor(es):</span> {{ proyecto.autores }}
            </div>
            <div>
              <span class="font-medium">Asesor:</span> {{ proyecto.asesor }}
            </div>
            <div>
              <span class="font-medium">Institución:</span> {{ proyecto.institucion }}
            </div>
          </div>
        </div>
  
        <!-- Criterios de evaluación -->
        <div class="space-y-4">
          <h5 class="text-lg font-semibold text-gray-800 border-b pb-2">Criterios de Evaluación</h5>
          
          <div v-for="criterio in proyecto.criterios" :key="criterio.id" class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div class="flex-1">
                <h6 class="font-semibold text-gray-800">
                  {{ criterio.nombre }}
                  <span class="text-sm font-normal text-gray-500 ml-2">(Máx. {{ criterio.puntaje_maximo }} pts)</span>
                </h6>
                <p class="text-sm text-gray-600 mt-1">{{ criterio.descripcion }}</p>
              </div>
              
              <div class="flex items-center space-x-2">
                <input
                  type="number"
                  v-model.number="puntajes[criterio.id]"
                  :max="criterio.puntaje_maximo"
                  min="0"
                  class="w-20 px-3 py-2 border border-gray-300 rounded-md text-center focus:ring-[#611232] focus:border-[#611232]"
                  @change="validarPuntaje(criterio)"
                >
                <span class="text-sm text-gray-500">/ {{ criterio.puntaje_maximo }}</span>
              </div>
            </div>
            <div v-if="erroresPuntajes[criterio.id]" class="text-red-500 text-xs mt-1">
              {{ erroresPuntajes[criterio.id] }}
            </div>
          </div>
        </div>
  
        <!-- Resumen y acciones -->
        <div class="mt-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Comentarios -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones del evaluador:</label>
              <textarea
                v-model="comentarios"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#611232] focus:border-[#611232]"
                placeholder="Escriba aquí sus comentarios sobre el proyecto..."
              ></textarea>
            </div>
            
            <!-- Resumen de puntuación -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
              <h6 class="font-semibold text-gray-800 mb-3">Resumen de Evaluación</h6>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-600">Puntaje obtenido:</span>
                  <span class="font-medium">{{ totalPuntos }} pts</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Puntaje máximo:</span>
                  <span class="font-medium">{{ puntajeMaximoTotal }} pts</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Porcentaje:</span>
                  <span class="font-medium">{{ porcentaje }}%</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full mt-2 overflow-hidden">
                  <div 
                    class="h-full rounded-full"
                    :class="{
                      'bg-red-500': porcentaje < 60,
                      'bg-yellow-500': porcentaje >= 60 && porcentaje < 80,
                      'bg-green-600': porcentaje >= 80
                    }"
                    :style="{ width: `${porcentaje}%` }"
                  ></div>
                </div>
                <div class="pt-2">
                  <p v-if="porcentaje < 80" class="text-red-600 text-sm">
                    <i class="fas fa-exclamation-circle mr-1"></i> No cumple con el mínimo requerido (80%)
                  </p>
                  <p v-else class="text-green-600 text-sm">
                    <i class="fas fa-check-circle mr-1"></i> Cumple con los requisitos mínimos
                  </p>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Botones de acción -->
          <div class="flex justify-end space-x-3 mt-6">
            <button
              @click="$emit('guardar-evaluacion', { puntajes, comentarios, esBorrador: true })"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#611232]"
            >
              <i class="far fa-save mr-2"></i> Guardar borrador
            </button>
            <button
              @click="confirmarEnvio"
              class="px-4 py-2 bg-[#611232] text-white rounded-md hover:bg-[#4a0d24] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#611232]"
            >
              <i class="fas fa-paper-plane mr-2"></i> Enviar evaluación final
            </button>
          </div>
        </div>
      </div>
  
      <!-- Estado vacío -->
      <div v-else class="text-center py-12">
        <div class="mx-auto w-24 h-24 text-gray-400 mb-4">
          <i class="fas fa-folder-open text-5xl"></i>
        </div>
        <h4 class="text-lg font-medium text-gray-700 mb-2">No hay proyectos asignados</h4>
        <p class="text-gray-500 mb-4">Actualmente no tienes proyectos pendientes por evaluar.</p>
        <button
          @click="currentView = 'proyectos'"
          class="px-4 py-2 text-sm text-[#611232] font-medium border border-[#611232] rounded-md hover:bg-[#611232]/10"
        >
          Ver todos mis proyectos
        </button>
      </div>
    </div>
  </template>