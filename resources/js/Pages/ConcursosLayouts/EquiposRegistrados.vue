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
        <div class="relative"> 
          <h1>Equipos Registrados</h1>
          <table class="min-w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="border p-2">Equipo</th>
            <th class="border p-2">Proyecto</th>
            <th class="border p-2">Concurso</th>
            <th class="border p-2">Integrantes</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="equipo in equipos" :key="equipo.id" class="border-b hover:bg-gray-50">
            <td class="border p-2">{{ equipo.id }}</td>
            <td class="border p-2">{{ equipo.proyecto ? equipo.proyecto.nombre : 'N/A' }}</td>
            <td class="border p-2">{{ equipo.proyecto && equipo.proyecto.concurso ? equipo.proyecto.concurso.nombre : (equipo.concurso ? equipo.concurso.nombre : 'N/A') }}</td>
            <td class="border p-2">
          <ul v-if="equipo.participantes && equipo.participantes.length > 0">
            <li v-for="integrante in equipo.participantes" :key="integrante.id">{{ integrante.nombre }}</li>
          </ul>
            </td>
          </tr>
        </tbody>
          </table>
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
const { props } = usePage();
const equipos = ref(props.equipos);
</script>
