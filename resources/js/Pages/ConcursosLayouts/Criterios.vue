<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Criterios de Evaluación
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
      
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <div class="mb-6 flex justify-between items-center">
          <h2 class="text-2xl font-bold text-[#611232]">
            Criterios de Evaluación de Prototipos y Proyectos de Emprendimiento.
          </h2>
        </div>

        <div v-for="modalidad in modalidadesUnicas" :key="modalidad" class="mb-8">
          <div class="bg-[#611232] px-4 py-2 rounded-t-lg">
            <h3 class="text-lg font-bold text-white">{{ modalidad }}</h3>
          </div>
          
          <div class="overflow-hidden shadow rounded-b-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-4 py-2 text-left text-sm font-medium text-gray-500">Tipo</th>
                  <th scope="col" class="px-4 py-2 text-left text-sm font-medium text-gray-500">Criterio</th>
                  <th scope="col" class="px-4 py-2 text-right text-sm font-medium text-gray-500">Puntaje</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <template v-for="tipo in tiposCriterio" :key="tipo.value">
                  <template v-if="criteriosPorModalidadYTipo(modalidad, tipo.value).length > 0">
                    <tr class="bg-gray-50">
                      <td colspan="3" class="px-4 py-2 text-sm font-medium text-gray-900">
                        {{ tipo.label }}
                      </td>
                    </tr>
                    <tr v-for="criterio in criteriosPorModalidadYTipo(modalidad, tipo.value)" :key="criterio.id" class="hover:bg-gray-50">
                      <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                        {{ tipo.label }}
                      </td>
                      <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ criterio.nombre }}
                      </td>
                      <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                          {{ criterio.puntaje_maximo }} pts
                        </span>
                      </td>
                    </tr>
                  </template>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';

const props = defineProps({
  criterios: {
    type: Array,
    default: () => []
  }
});

const userRole = 'evaluador';

const tiposCriterio = [
  { value: 'informe', label: 'I. Informe' },
  { value: 'modalidad', label: 'II. Modalidad' },
  { value: 'exposicion', label: 'III. Exposición' }
];

const modalidadesUnicas = computed(() => {
  const modalidades = props.criterios.map(c => c.modalidad);
  return [...new Set(modalidades)].sort();
});

const criteriosPorModalidadYTipo = (modalidad, tipo) => {
  return props.criterios.filter(
    c => c.modalidad === modalidad && c.tipo_criterio === tipo
  );
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

<style scoped>
tbody tr:nth-child(odd) {
  background-color: #f9f9f9;
}

tbody tr:hover {
  background-color: #f1f1f1;
}
</style>