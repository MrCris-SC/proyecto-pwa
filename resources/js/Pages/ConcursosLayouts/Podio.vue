<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Podio del Concurso
      </h2>
    </template>
    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <!-- Menú lateral -->
      <MenuLateral :rol="$page.props.auth.user.rol" />

      <!-- Contenido principal -->
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <div>
          <h2 class="text-3xl font-bold mb-6 text-center text-[#611232]">Podio del Concurso</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Primer lugar -->
            <div class="col-span-1 md:col-span-1 md:order-2 bg-yellow-100 border border-yellow-300 rounded-lg shadow-md p-6 text-center">
              <h3 class="text-2xl font-bold text-yellow-700">🥇 Primer Lugar</h3>
              <p class="text-lg font-semibold text-gray-700 mt-4">{{ resultados[0]?.equipo.proyecto.nombre || 'N/A' }}</p>
              <p class="text-sm text-gray-500 mt-2">Promedio: {{ resultados[0]?.promedio_final || 'N/A' }}</p>
            </div>
            <!-- Segundo lugar -->
            <div class="col-span-1 md:col-span-1 md:order-1 bg-gray-100 border border-gray-300 rounded-lg shadow-md p-6 text-center">
              <h3 class="text-2xl font-bold text-gray-700">🥈 Segundo Lugar</h3>
              <p class="text-lg font-semibold text-gray-700 mt-4">{{ resultados[1]?.equipo.proyecto.nombre || 'N/A' }}</p>
              <p class="text-sm text-gray-500 mt-2">Promedio: {{ resultados[1]?.promedio_final || 'N/A' }}</p>
            </div>
            <!-- Tercer lugar -->
            <div class="col-span-1 md:col-span-1 md:order-3 bg-amber-200 border border-amber-300 rounded-lg shadow-md p-6 text-center">
              <h3 class="text-2xl font-bold text-amber-700">🥉 Tercer Lugar</h3>
              <p class="text-lg font-semibold text-gray-700 mt-4">{{ resultados[2]?.equipo.proyecto.nombre || 'N/A' }}</p>
              <p class="text-sm text-gray-500 mt-2">Promedio: {{ resultados[2]?.promedio_final || 'N/A' }}</p>
            </div>
          </div>
          <!-- Tabla de resultados -->
          <div class="mt-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Resultados Completos</h3>
            <div class="max-h-96 overflow-y-auto border border-gray-300 rounded-lg">
              <table class="table-auto w-full border-collapse">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Posición</th>
                    <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Equipo</th>
                    <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Promedio Final</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="(resultado, index) in resultados" 
                    :key="resultado.id" 
                    :class="[
                      index === 0 ? 'bg-yellow-50' : '', 
                      index < 3 ? 'bg-gray-50' : '',
                      'hover:bg-gray-100 transition-colors'
                    ]"
                    class="text-sm"
                  >
                    <td class="border border-gray-300 px-4 py-1 text-gray-600">{{ index + 1 }}</td>
                    <td class="border border-gray-300 px-4 py-1 text-gray-600">{{ resultado.equipo.proyecto.nombre }}</td>
                    <td class="border border-gray-300 px-4 py-1 text-gray-600">{{ resultado.promedio_final }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';

const props = defineProps({
  resultados: {
    type: Array,
    required: true,
  },
});
</script>
