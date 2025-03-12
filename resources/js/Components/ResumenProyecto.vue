<!-- resources/js/Components/ResumenProyecto.vue -->
<template>
    <div class="mb-8 p-6 bg-gray-50 rounded-lg">
      <h2 class="text-xl font-semibold text-[#611232] mb-4">Resumen del Proyecto</h2>
      <div class="space-y-2">
        <p><strong>Nombre del Proyecto:</strong> {{ proyecto.nombre || 'No disponible' }}</p>
        <p><strong>Categotia:</strong> {{ proyecto.categoria || 'No disponible' }}</p>
        <p><strong>Modalidad:</strong> {{ proyecto.modalidad_id || 'No disponible' }}</p>
        <p><strong>Linea de Investigacion:</strong> {{ proyecto.linea_investigacion_id|| 'No disponible' }}</p>
        <p><strong>Concurso:</strong> {{ proyecto.concurso_id || 'No disponible' }}</p>
        <p><strong>Estado:</strong> {{ proyecto.estado || 'No disponible' }}</p>
        <p><strong>Progreso:</strong> {{ proyecto.progreso || '0' }}% completado</p>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    proyecto: Object,
  });
  </script>