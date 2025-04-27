<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Reportes de Evaluación
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
      
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          Reportes de Evaluación
        </h2>

        <!-- Filtros -->
        <div class="mb-8 p-6 border rounded-lg hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-[#611232] mb-4">Filtros</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Concurso</label>
              <select v-model="filtroConcurso" class="w-full border border-gray-300 rounded-md p-2">
                <option value="">Todos</option>
                <option v-for="concurso in concursosUnicos" :key="concurso.id" :value="concurso.id">
                  {{ concurso.nombre }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Modalidad</label>
              <select v-model="filtroModalidad" class="w-full border border-gray-300 rounded-md p-2">
                <option value="">Todas</option>
                <option v-for="modalidad in modalidadesUnicas" :key="modalidad.id" :value="modalidad.id">
                  {{ modalidad.nombre }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
              <select v-model="filtroEstado" class="w-full border border-gray-300 rounded-md p-2">
                <option value="">Todos</option>
                <option value="completada">Completadas</option>
                <option value="pendiente">Pendientes</option>
              </select>
            </div>
          </div>
          <button 
            @click="aplicarFiltros"
            class="mt-4 px-4 py-2 bg-[#611232] text-white rounded-md hover:bg-[#4a0d24] transition-colors"
          >
            Aplicar Filtros
          </button>
        </div>

        <!-- Resumen -->
        <div class="mb-8 p-6 border rounded-lg hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-[#611232] mb-4">Resumen</h3>
          <div class="grid grid-cols-3 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg text-center">
              <p class="text-sm text-gray-600">Total</p>
              <p class="text-xl font-bold">{{ totalProyectos }}</p>
            </div>
            <div class="p-4 bg-green-50 rounded-lg text-center">
              <p class="text-sm text-green-600">Completados</p>
              <p class="text-xl font-bold text-green-700">{{ proyectosCompletados }}</p>
            </div>
            <div class="p-4 bg-yellow-50 rounded-lg text-center">
              <p class="text-sm text-yellow-600">Pendientes</p>
              <p class="text-xl font-bold text-yellow-700">{{ proyectosPendientes }}</p>
            </div>
          </div>
        </div>

        <!-- Listado de proyectos -->
        <div v-if="proyectosFiltrados.length > 0" class="space-y-4">
          <div v-for="proyecto in proyectosFiltrados" :key="proyecto.id" class="p-6 border rounded-lg hover:bg-gray-50">
            <h3 class="font-semibold text-lg text-[#611232]">{{ proyecto.nombre }}</h3>
            <div class="grid grid-cols-2 gap-2 mt-4 text-sm">
              <div><span class="font-medium">Modalidad:</span> {{ proyecto.modalidad }}</div>
              <div><span class="font-medium">Concurso:</span> {{ proyecto.concurso }}</div>
              <div v-if="proyecto.estado === 'completada'">
                <span class="font-medium">Puntaje:</span> 
                <span :class="{
                  'text-green-600': proyecto.puntaje >= 70,
                  'text-yellow-600': proyecto.puntaje < 70 && proyecto.puntaje >= 50,
                  'text-red-600': proyecto.puntaje < 50
                }">
                  {{ proyecto.puntaje }} pts
                </span>
              </div>
              <div v-else><span class="font-medium">Puntaje:</span> -</div>
              <div>
                <span class="font-medium">Estado:</span> 
                <span :class="{
                  'text-green-600': proyecto.estado === 'completada',
                  'text-yellow-600': proyecto.estado === 'pendiente'
                }">
                  {{ proyecto.estado === 'completada' ? 'Evaluado' : 'Pendiente' }}
                </span>
              </div>
              <div><span class="font-medium">Fecha:</span> {{ proyecto.fecha }}</div>
            </div>
          </div>
        </div>
        
        <div v-else class="p-8 text-center">
          <p class="text-gray-700">No se encontraron proyectos con los filtros seleccionados.</p>
        </div>

        <!-- Botón de exportación -->
        <div class="mt-8 text-center">
          <button 
            @click="exportarReporte"
            class="inline-flex items-center px-4 py-2 bg-[#611232] text-white rounded-md hover:bg-[#4a0d24] transition-colors disabled:opacity-50"
            disabled
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Exportar Reporte (Próximamente)
          </button>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';

const props = defineProps({
  evaluaciones: {
    type: Array,
    default: () => []
  }
});

const userRole = 'evaluador';

// Filtros
const filtroConcurso = ref('');
const filtroModalidad = ref('');
const filtroEstado = ref('');

// Datos procesados
const proyectos = computed(() => {
  return props.evaluaciones.map(e => ({
    id: e.id,
    nombre: e.nombre_proyecto,
    modalidad: e.modalidad_nombre,
    modalidad_id: e.modalidad_id,
    concurso: e.concurso_nombre,
    concurso_id: e.concurso_id,
    estado: e.estado,
    puntaje: e.puntaje_total || 0,
    fecha: e.estado === 'completada' ? e.fecha_evaluacion : e.created_at
  }));
});

// Valores únicos para filtros
const concursosUnicos = computed(() => {
  return [...new Map(props.evaluaciones.map(item => 
    [item.concurso_id, { id: item.concurso_id, nombre: item.concurso_nombre }]
  )).values()];
});

const modalidadesUnicas = computed(() => {
  return [...new Map(props.evaluaciones.map(item => 
    [item.modalidad_id, { id: item.modalidad_id, nombre: item.modalidad_nombre }]
  )).values()];
});

// Aplicar filtros
const proyectosFiltrados = computed(() => {
  let filtrados = proyectos.value;
  
  if (filtroConcurso.value) {
    filtrados = filtrados.filter(p => p.concurso_id == filtroConcurso.value);
  }
  
  if (filtroModalidad.value) {
    filtrados = filtrados.filter(p => p.modalidad_id == filtroModalidad.value);
  }
  
  if (filtroEstado.value) {
    filtrados = filtrados.filter(p => p.estado === filtroEstado.value);
  }
  
  return filtrados;
});

// Totales
const totalProyectos = computed(() => proyectosFiltrados.value.length);
const proyectosCompletados = computed(() => 
  proyectosFiltrados.value.filter(p => p.estado === 'completada').length
);
const proyectosPendientes = computed(() => 
  proyectosFiltrados.value.filter(p => p.estado === 'pendiente').length
);

const aplicarFiltros = () => {
  // La reactividad de computed properties ya maneja los filtros
};

const exportarReporte = () => {
  alert('La función de exportación estará disponible próximamente');
};

const handleMenuSelected = (menu) => {
  const routes = {
    'evaluación': 'evaluacion.index',
    'proyectos asignados': 'proyectos.asignados',
    'criterios': 'criterios.index',
    'reportes': 'reportes.index',
    'perfil': 'perfil.index'
  };
  
  if (routes[menu]) {
    router.visit(route(routes[menu]));
  }
};
</script>