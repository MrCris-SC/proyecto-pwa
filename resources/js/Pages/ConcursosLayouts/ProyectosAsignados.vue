<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Proyectos Asignados
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
      
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          Proyectos Asignados
        </h2>
        
        <div v-if="proyectos.length > 0" class="space-y-4">
          <div v-for="proyecto in proyectos" :key="proyecto.id" class="p-4 border rounded-lg hover:bg-gray-50">
            <h3 class="font-semibold text-lg">{{ proyecto.nombre_proyecto }}</h3>
            <div class="grid grid-cols-2 gap-2 mt-2 text-sm">
              <div><span class="font-medium">Modalidad:</span> {{ proyecto.modalidad }}</div>
              <div><span class="font-medium">Concurso:</span> {{ proyecto.concurso }}</div>
              <div><span class="font-medium">Estado:</span> 
                <span :class="{
                  'text-green-600': proyecto.estado === 'completada',
                  'text-yellow-600': proyecto.estado !== 'completada'
                }">
                  {{ proyecto.estado === 'completada' ? 'Evaluado' : 'Pendiente' }}
                </span>
              </div>
              <div><span class="font-medium">Asignado el:</span> {{ proyecto.fecha_asignacion }}</div>
            </div>
          </div>
        </div>
        
        <div v-else class="p-8 bg-white rounded-lg shadow-lg">
          <p class="text-gray-700">No tienes proyectos asignados actualmente.</p>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  proyectos: {
    type: Array,
    default: () => []
  }
});

const userRole = 'evaluador';
const handleMenuSelected = (menu) => {
  console.log('Menú seleccionado:', menu);
};
</script>