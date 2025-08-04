<template>
<AuthenticatedLayout>
  <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Resultados Finales del Concurso
      </h2>
    </template>

   <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <!-- Menú lateral -->
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />

      <main 
        class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg relative"
        :class="{ 'opacity-50 pointer-events-none': mostrarModalEvaluaciones || mostrarModalInscripcion || mostrarModalCerrar }"
      >

        <div class="container mx-auto px-4 py-8">
          <h1 class="text-3xl font-bold mb-8 text-center text-indigo-600">Resultados Finales</h1>

          <div
            v-for="(modalidadData, modalidad) in modalidadesAgrupadas"
            :key="modalidad"
            class="mb-12 space-y-6"
          >
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">{{ modalidad }}</h2>

            <!-- Podio Estilo Vertical -->
            <div class="flex flex-row justify-center items-start space-x-6">
              <div
                v-for="(puesto, i) in modalidadData.podio"
                :key="i"
                :class="[
                  'w-64 p-6 rounded-xl shadow text-center',
                  i === 0
                    ? 'bg-yellow-100 border-2 border-yellow-400'
                    : 'bg-gray-100 border border-gray-300',
                ]"
              >
                <h3 class="text-xl font-bold" :class="i === 0 ? 'text-yellow-700' : 'text-gray-700'">
                  {{ i === 0 ? '🥇 1er Lugar' : i === 1 ? '🥈 2do Lugar' : '🥉 3er Lugar' }}
                </h3>
                <p class="text-lg font-bold text-indigo-900 mt-2">
                  {{ puesto.equipo?.proyecto?.nombre || 'Sin nombre' }}
                </p>
                <p class="text-sm text-gray-700">Promedio: {{ puesto.promedio_final }}</p>
              </div>
            </div>


            <!-- Tabla de Resultados Vertical y 100% Responsive -->
            <div class="w-full overflow-hidden rounded-xl shadow-lg border border-gray-200">
              <table class="w-full min-w-full text-sm text-gray-800 bg-white">
                <thead>
                  <tr class="bg-indigo-100 text-gray-900">
                    <th class="py-3 px-5 text-left font-bold whitespace-nowrap">Posición</th>
                    <th class="py-3 px-5 text-left font-bold whitespace-nowrap">Proyecto</th>
                    <th class="py-3 px-5 text-left font-bold whitespace-nowrap">Promedio Final</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(res, idx) in modalidadData.resultados"
                    :key="idx"
                    :class="[
                      'transition duration-200 hover:bg-indigo-50',
                      idx === 0 ? 'bg-yellow-100 font-bold' :
                      idx === 1 ? 'bg-gray-200 font-semibold' :
                      idx === 2 ? 'bg-orange-100 font-medium' : ''
                    ]"
                  >
                    <td class="py-3 px-5 border-t text-center">{{ idx + 1 }}</td>
                    <td class="py-3 px-5 border-t truncate max-w-[250px]">{{ res.equipo?.proyecto?.nombre || 'Sin nombre' }}</td>
                    <td class="py-3 px-5 border-t text-center">{{ res.promedio_final }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="flex justify-end mb-4">
          <a
            :href="route('resultados.pdf')"
            target="_blank"
            class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg shadow inline-flex items-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h4a1 1 0 011 1v7h3.586a1 1 0 01.707 1.707l-5.586 5.586a1 1 0 01-1.414 0L1.707 11.707A1 1 0 012.414 10H6V3z" clip-rule="evenodd" />
            </svg>
            Descargar PDF de resultados
          </a>
        </div>

      </main>
    
  </div>
</AuthenticatedLayout>
   
  
</template>


<script setup>
import { ref } from 'vue'
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage } from '@inertiajs/vue3';


const handleMenuSelected = (menu) => {
  selectedMenu.value = menu.toLowerCase();
  showForm.value = selectedMenu.value !== 'concursos';

  if (selectedMenu.value === 'concursos') {
    showForm.value = false;
  } else if (selectedMenu.value === 'gestión de proyectos') {
    router.get(route('gestion.proyectos'));
  } else if (selectedMenu.value === 'registro de criterios') {
    showForm.value = true;
  } else if (selectedMenu.value === 'equipos registrados') {
    router.get(route('equipos.registrados'));
  }
};

const props = defineProps({
  podio: Array,
  resultados: Array,
  clasificaciones: Array,
  agrupados: Object,
  modalidadesAgrupadas: Object,
 
  
});
const userRole = usePage().props.value?.auth?.user?.rol || 'invitado';

const activeTab = ref(
  props.agrupados && typeof props.agrupados === 'object'
    ? Object.keys(props.agrupados)[0]
    : ''
);

console.log("Agrupación visual (modalidadesAgrupadas):", props.modalidadesAgrupadas);
</script>
