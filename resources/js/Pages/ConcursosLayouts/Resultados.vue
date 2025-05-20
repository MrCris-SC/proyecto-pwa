<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();

const GenerarReporte = () => {
  // Llama a la ruta que genera el reporte, pasando el id del concurso y del equipo
  window.open(route('resultados.generarReporte', {
    concursoId: props.concursoId,
    equipoId: props.equipoId
  }), '_blank');
};
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Proceso de Concursos
      </h2>
    </template>
    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <!-- Menú lateral -->
      <MenuLateral />
      <!-- Contenido principal -->
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h3 class="text-2xl font-bold mb-4 text-[#611232]">Resultados</h3>
        <p class="mb-2">
          Aquí puedes visualizar un reporte de tu participacion en este concurso.
        </p>      
        <div class="flex mb-4">
          <button 
            class="bg-[#611232] text-white px-4 py-2 rounded hover:bg-[#4a0e26] transition-colors"
            @click="GenerarReporte"
          >
            Generar Reporte
          </button>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>
