<template>
  <div class="p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">Registro de Evaluadores</h2>
    <form @submit.prevent="handleSubmit">
      <div class="mb-4">
        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
        <input id="nombre" v-model="nombre" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
      </div>
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
        <input id="email" v-model="email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
      </div>
      <div class="flex justify-end">
        <button type="button" @click="$emit('close')" class="mr-4 bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Registrar</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const nombre = ref('');
const email = ref('');

const handleSubmit = () => {
  // Lógica para enviar los datos del evaluador al servidor
  console.log('Registrando evaluador:', { nombre: nombre.value, email: email.value });
  // Emitir evento para cerrar el formulario después del registro
  $emit('close');
};
</script>
