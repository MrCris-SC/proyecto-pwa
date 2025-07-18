<template>
  <AuthenticatedLayout> 
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{('Equipos Registrados') }}
      </h2>
    </template>
    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
    
      <main class="lg:ml-8">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          {{ selectedMenu }}
        </h2>
        <div class="relative overflow-auto max-w-full"> 
          <h1 class="text-xl font-semibold mb-4">Equipos Registrados</h1>
          <div class="w-full">
            <SearchablePaginatedTable :data="equipos" class="min-w-full" />
          </div>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import SearchablePaginatedTable from '@/Components/SearchablePaginatedTable.vue';

const { props } = usePage();
const equipos = ref(props.equipos);
</script>
