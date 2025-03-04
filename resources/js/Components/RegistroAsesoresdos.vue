<!-- resources/js/Components/RegistroAsesoresdos.vue -->
<template>
    <div class="mb-8 relative">
      <button
        class="absolute top-0 right-0 bg-gray-500 text-white px-2 py-1 rounded-lg hover:bg-gray-600 transition duration-200"
        @click="cerrar"
      >
        X
      </button>
      <h2 class="text-xl font-semibold text-[#611232] mb-4">Registro de Asesores</h2>
      <!-- Aquí puedes agregar el formulario de registro de asesores -->
    </div>
  </template>
  
  <script setup>
  const emit = defineEmits(['close']);
  
  const cerrar = () => {
    emit('close');
  };
  </script>