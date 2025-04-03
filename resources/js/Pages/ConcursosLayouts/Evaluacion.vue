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
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          Evaluación
        </h2>

        <div class="p-8 bg-white rounded-lg shadow-lg">
          <p class="text-gray-700">Contenido de evaluación aparecerá aquí</p>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';


const handleMenuSelected = (menu) => {
  console.log('Menú seleccionado:', menu);
};
</script>