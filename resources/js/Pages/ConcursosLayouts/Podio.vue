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
          <!-- Podio circular alternativo -->
          <div class="flex justify-center items-end gap-4 mb-10">
            <!-- Segundo lugar -->
            <div class="flex flex-col items-center">
              <div class="w-28 h-28 md:w-32 md:h-32 bg-gradient-to-b from-[#BFC9CA] to-[#85929E] border-4 border-[#85929E] rounded-full flex items-center justify-center shadow-lg relative z-10">
                <span class="text-4xl md:text-5xl">🥈</span>
              </div>
              <div class="mt-2 text-center">
                <p class="text-base font-semibold text-[#34495E]">{{ resultados[1]?.equipo.proyecto.nombre || 'N/A' }}</p>
                <p class="text-xs text-gray-500">Promedio: {{ resultados[1]?.promedio_final || 'N/A' }}</p>
              </div>
            </div>
            <!-- Primer lugar -->
            <div class="flex flex-col items-center">
              <div class="w-36 h-36 md:w-44 md:h-44 bg-gradient-to-b from-[#F7DC6F] to-[#F1C40F] border-4 border-[#F1C40F] rounded-full flex items-center justify-center shadow-2xl relative z-20 scale-110">
                <span class="text-6xl md:text-7xl">🥇</span>
              </div>
              <div class="mt-2 text-center">
                <p class="text-lg font-bold text-[#B7950B]">{{ resultados[0]?.equipo.proyecto.nombre || 'N/A' }}</p>
                <p class="text-sm text-gray-600">Promedio: {{ resultados[0]?.promedio_final || 'N/A' }}</p>
              </div>
            </div>
            <!-- Tercer lugar -->
            <div class="flex flex-col items-center">
              <div class="w-24 h-24 md:w-28 md:h-28 bg-gradient-to-b from-[#FAD7A0] to-[#CA6F1E] border-4 border-[#CA6F1E] rounded-full flex items-center justify-center shadow-md relative z-10">
                <span class="text-3xl md:text-4xl">🥉</span>
              </div>
              <div class="mt-2 text-center">
                <p class="text-base font-semibold text-[#CA6F1E]">{{ resultados[2]?.equipo.proyecto.nombre || 'N/A' }}</p>
                <p class="text-xs text-gray-500">Promedio: {{ resultados[2]?.promedio_final || 'N/A' }}</p>
              </div>
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
