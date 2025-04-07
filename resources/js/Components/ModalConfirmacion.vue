<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirmación</h2>
      <p class="text-gray-600 mb-6">{{ mensaje }}</p>
      <div class="flex justify-end space-x-4">
        <button
          @click="$emit('cancelar')"
          class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
        >
          Cancelar
        </button>
        <button
          @click="$emit('confirmar')"
          class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
        >
          Confirmar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  mensaje: {
    type: String,
    required: true,
  },
});
</script>

<style scoped>
/* Estilos adicionales si son necesarios */
</style>