<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Módulo de Evaluación
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <!-- Eliminar :rol="userRole" ya que el menú ya obtiene el rol del usuario -->
      <MenuLateral @menu-selected="handleMenuSelected" />
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          Evaluación
        </h2>

        <div class="p-8 bg-white rounded-lg shadow-lg">
          <!-- Cambiar esto por el contenido real de evaluación -->
          <template v-if="currentView === 'evaluacion'">
            <!-- Componente de evaluación -->
          </template>
          <template v-else-if="currentView === 'proyectos'">
            <!-- Componente de proyectos asignados -->
          </template>
          <!-- Agregar más vistas según sea necesario -->
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Estado para controlar la vista actual
const currentView = ref('evaluacion');

const handleMenuSelected = (menu) => {
  console.log('Menú seleccionado:', menu);
  
  // Mapear la selección del menú a las vistas
  switch(menu) {
    case 'evaluación':
      currentView.value = 'evaluacion';
      break;
    case 'proyectos asignados':
      currentView.value = 'proyectos';
      break;
    case 'criterios':
      currentView.value = 'criterios';
      break;
    // Agregar más casos según sea necesario
    default:
      currentView.value = 'evaluacion';
  }
};
</script>