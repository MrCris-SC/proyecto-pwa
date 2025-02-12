<template>
    <div class="w-64 h-64 bg-gradient-to-r from-[#e0e0e0] to-[#c0c0c0] rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow duration-300">
        <h3 class="text-xl font-semibold text-[#611232]">{{ titulo }}</h3>
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
