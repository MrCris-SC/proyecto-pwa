<!-- resources/js/Components/InscripcionConcurso.vue -->
<template>
    <div class="mb-8">
      <button
        v-if="!inscrito"
        class="bg-[#611232] text-white px-6 py-2 rounded-lg hover:bg-[#8A1C4A] transition duration-200"
        @click="inscribirse"
      >
        Inscribirse en el Concurso
      </button>
      <p v-else class="text-green-600 font-semibold">
        Ya estás inscrito en este concurso.
      </p>
    </div>
  </template>
  
  <script setup>
  defineProps({
    inscrito: Boolean,
  });
  
  const emit = defineEmits(['inscribirse']);
  
  const inscribirse = () => {
    emit('inscribirse');
  };
  </script>