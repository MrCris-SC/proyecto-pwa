<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Criterios de Evaluación
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
      
      <main class="w-full max-w-6xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <div class="mb-6 flex justify-between items-center">
          <h2 class="text-2xl font-bold text-[#611232]">
            Criterios de Evaluación
          </h2>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Línea de Investigación</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criterio</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                  <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Puntaje Máximo</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <template v-for="linea in lineasInvestigacion" :key="linea.id">
                  <tr v-for="criterio in criteriosPorLinea(linea.id)" :key="criterio.id" class="hover:bg-gray-50">
                    <td v-if="criterio === criteriosPorLinea(linea.id)[0]" :rowspan="criteriosPorLinea(linea.id).length" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ linea.nombre }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ criterio.nombre }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      {{ criterio.descripcion }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ criterio.puntaje_maximo }} pts
                      </span>
                    </td>
                  </tr>
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
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  criterios: Array,
  lineasInvestigacion: Array
});

const userRole = 'evaluador';

const criteriosPorLinea = (lineaId) => {
  return props.criterios.filter(c => c.linea_investigacion_id == lineaId);
};

const handleMenuSelected = (menu) => {
  // Redirigir según la opción del menú seleccionada
  if (menu === 'evaluación') {
    router.visit(route('evaluacion.index'));
  } else if (menu === 'proyectos asignados') {
    router.visit(route('proyectos-asignados.index'));
  }
};
</script>