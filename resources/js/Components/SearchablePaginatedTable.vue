<template>
  <div class="max-w-full mx-auto p-4"> <!-- Updated max width to full -->
    <!-- Card Container -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Card Header -->
      <div class="bg-[#611232] px-6 py-4">
        <h2 class="text-white text-xl font-bold">Equipos Registrados</h2>
      </div>
      <!-- Card Body -->
      <div class="p-6">
        <!-- Buscador -->
        <div class="mb-4">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar..."
            class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
          />
        </div>
        <!-- Tabla Responsiva -->
        <div class="overflow-x-auto">
          <table class="w-full min-w-full divide-y divide-gray-200"> <!-- Added w-full -->
            <thead class="bg-[#611232]">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                  Equipo
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                  Proyecto
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                  Concurso
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                  Integrantes
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                    Asesores
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                    Acciones
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="equipo in paginatedData" :key="equipo.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">{{ equipo.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  {{ equipo.proyecto ? equipo.proyecto.nombre : 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  {{
                    equipo.proyecto && equipo.proyecto.concurso 
                      ? equipo.proyecto.concurso.nombre 
                      : (equipo.concurso ? equipo.concurso.nombre : 'N/A')
                  }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <ul v-if="equipo.participantes && equipo.participantes.length > 0" class="list-disc list-inside">
                    <li v-for="integrante in equipo.participantes" :key="integrante.id">
                      {{ integrante.nombre }}
                    </li>
                  </ul>
                </td>
              </tr>
              <tr v-if="paginatedData.length === 0">
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                  No hay equipos registrados.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Paginación -->
        <div class="flex justify-between items-center mt-4">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
          >
            Anterior
          </button>
          <span class="text-sm">Página {{ currentPage }} de {{ totalPages }}</span>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  data: {
    type: Array,
    required: true,
  },
});

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 5;

const filteredData = computed(() => {
  if (!searchQuery.value) return props.data;
  return props.data.filter((item) =>
    Object.values(item)
      .join(' ')
      .toLowerCase()
      .includes(searchQuery.value.toLowerCase())
  );
});

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage));

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredData.value.slice(start, start + itemsPerPage);
});

function nextPage() {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
}

function prevPage() {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
}
</script>

<style scoped>
/* Personaliza estilos adicionales si lo necesitas */
</style>
