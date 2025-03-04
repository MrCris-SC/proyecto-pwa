<!-- resources/js/Components/DocumentosTable.vue -->
<template>
    <div class="mb-8">
      <h2 class="text-xl font-semibold text-[#611232] mb-4">Documentación Requerida</h2>
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-[#8A1C4A] text-white">
            <th class="p-3 text-left">Documento</th>
            <th class="p-3 text-left">Estado</th>
            <th class="p-3 text-left">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="documentos.length === 0">
            <td colspan="3" class="p-3 text-center text-gray-500">No hay documentos requeridos.</td>
          </tr>
          <tr v-for="doc in documentos" :key="doc.nombre" class="border-b hover:bg-gray-50">
            <td class="p-3">{{ doc.nombre }}</td>
            <td class="p-3">
              <span :class="doc.estado === 'Completado' ? 'text-green-600' : 'text-yellow-600'">
                {{ doc.estado }}
              </span>
            </td>
            <td class="p-3">
              <button
                v-if="doc.estado === 'Pendiente'"
                class="bg-[#611232] text-white px-3 py-1 rounded hover:bg-[#8A1C4A] transition duration-200"
                @click="$emit('subir-documento', doc)"
              >
                Subir
              </button>
              <button
                v-else
                class="bg-[#8A1C4A] text-white px-3 py-1 rounded hover:bg-[#611232] transition duration-200"
                @click="$emit('editar-documento', doc)"
              >
                Editar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script setup>
  defineProps({
    documentos: Array,
  });
  
  defineEmits(['subir-documento', 'editar-documento']);
  </script>