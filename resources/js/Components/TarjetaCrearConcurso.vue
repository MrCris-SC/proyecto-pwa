<template>
    <div @click="$emit('click')" class="flex items-center justify-center w-32 h-32 bg-white rounded-lg shadow-md cursor-pointer hover:bg-gray-100 transition transform hover:scale-105">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
    </div>
</template>

<script setup>
</script>

<style scoped>
/* Puedes agregar estilos adicionales aquí si es necesario */
</style>
