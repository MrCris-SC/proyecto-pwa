<template>
  <div class="mb-8">
    <h2 class="text-xl font-semibold text-[#611232] mb-4">Documentación Requerida</h2>

    <!-- Tabla para pantallas grandes (md en adelante) -->
    <div class="hidden md:block">
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
          <tr v-for="(doc, index) in documentos" :key="doc.nombre" class="border-b hover:bg-gray-50">
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
                @click="openUploadModal(index)"
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

    <!-- Lista para pantallas pequeñas (sm y menos) -->
    <div class="md:hidden">
      <div v-if="documentos.length === 0" class="p-3 text-center text-gray-500">
        No hay documentos requeridos.
      </div>
      <div v-for="(doc, index) in documentos" :key="doc.nombre" class="border-b p-3 hover:bg-gray-50">
        <div class="flex justify-between items-center">
          <div>
            <p class="font-semibold">{{ doc.nombre }}</p>
            <p :class="doc.estado === 'Completado' ? 'text-green-600' : 'text-yellow-600'">
              {{ doc.estado }}
            </p>
          </div>
          <div>
            <button
              v-if="doc.estado === 'Pendiente'"
              class="bg-[#611232] text-white px-3 py-1 rounded hover:bg-[#8A1C4A] transition duration-200"
              @click="openUploadModal(index)"
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
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para subir archivos -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Subir archivo</h3>
        <div
          class="border-2 border-dashed border-[#8A1C4A] p-6 rounded-lg text-center"
          @dragover.prevent="dragover"
          @drop.prevent="drop"
        >
          <p>Arrastra y suelta tus archivos aquí</p>
          <p class="text-sm text-gray-500">o</p>
          <input type="file" class="hidden" ref="fileInput" @change="onFileChange" />
          <button
            class="bg-[#611232] text-white px-4 py-2 rounded-lg hover:bg-[#8A1C4A] transition duration-200"
            @click="openFilePicker"
          >
            Seleccionar archivo
          </button>
        </div>
        <div class="mt-4 flex justify-end">
          <button
            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200"
            @click="closeUploadModal"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const fileInput = ref(null);
const showUploadModal = ref(false);
const selectedDocIndex = ref(null);
const isDragging = ref(false);

const openUploadModal = (index) => {
  selectedDocIndex.value = index;
  showUploadModal.value = true;
};

const closeUploadModal = () => {
  showUploadModal.value = false;
};

const openFilePicker = () => {
  fileInput.value.click();
};

const onFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    emit('subir-documento', { index: selectedDocIndex.value, file });
    closeUploadModal();
  }
};

const dragover = () => {
  isDragging.value = true;
};

const drop = (event) => {
  isDragging.value = false;
  const file = event.dataTransfer.files[0];
  if (file) {
    emit('subir-documento', { index: selectedDocIndex.value, file });
    closeUploadModal();
  }
};

const emit = defineEmits(['subir-documento', 'editar-documento']);
defineProps({
  documentos: Array,
});
</script>