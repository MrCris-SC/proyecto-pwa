<template>
    <div class="w-64 h-64 bg-white rounded-lg shadow-md p-4">
        <h3 class="text-xl font-semibold text-gray-800">{{ titulo }}</h3>
        <p class="text-gray-600 mt-2">Fecha de inicio: {{ fechaInicio }}</p>
        <p class="text-gray-600 mt-2">Fecha de apertura: {{ fechaApertura }}</p>
        <p class="text-gray-600 mt-2">Fecha de finalización: {{ fechaFinalizacion }}</p>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    titulo: {
        type: String,
        required: true,
    },
    fechaInicio: {
        type: String,
        required: true,
    },
    fechaApertura: {
        type: String,
        required: true,
    },
    fechaFinalizacion: {
        type: String,
        required: true,
    },
});
</script>

<style scoped>
/* Puedes agregar estilos adicionales aquí si es necesario */
</style>
