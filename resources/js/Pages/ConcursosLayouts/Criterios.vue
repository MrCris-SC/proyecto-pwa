<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Criterios
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <!-- Menú lateral -->
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
      
      <!-- Contenido principal -->
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          Criterios
        </h2>
        <div class="p-8 bg-white rounded-lg shadow-lg">
          <p class="text-gray-700">Aquí se mostrarán los criterios de evaluación.</p>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const userRole = 'evaluador'; // Simula el rol del usuario
const handleMenuSelected = (menu) => {
  console.log('Menú seleccionado:', menu);
};
</script>