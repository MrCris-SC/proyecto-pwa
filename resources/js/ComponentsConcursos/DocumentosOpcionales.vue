<template>
  <div class="upload-container" @dragover.prevent="onDragOver" @drop.prevent="onDrop">
    <p v-if="files.length === 0" class="mb-4">Arrastra o selecciona documentos Word o PDF</p>
    <div v-else class="grid grid-cols-2 gap-4">
      <div
        v-for="(file, index) in files"
        :key="index"
        class="file-card relative flex flex-col items-center p-4 border rounded-lg bg-white shadow-sm"
      >
        <!-- Botón para eliminar, posicionado en la esquina superior derecha -->
        <button class="absolute top-1 right-1 text-red-500 hover:text-red-700" @click="removeFile(index)">
          ✖
        </button>
        <!-- Icono del archivo, más grande --> 
        <div class="file-icon text-5xl mb-2">
          <i v-if="isPdf(file.name)" class="fas fa-file-pdf text-red-500"></i>
          <i v-else-if="isWord(file.name)" class="fas fa-file-word text-blue-500"></i>
          <i v-else class="fas fa-file text-gray-500"></i>
        </div>
        <!-- Nombre del archivo -->
        <div class="file-name text-center text-sm break-all">{{ file.name }}</div>
      </div>
    </div>
    <button class="mt-4 bg-[#611232] text-white px-4 py-2 rounded hover:bg-[#8A1C4A] transition duration-200" @click="triggerFileInput">
      Seleccionar archivos
    </button>
    <input type="file" accept=".doc,.docx,.pdf" @change="onFileSelect" ref="fileInput" multiple hidden />
  </div>
</template>

<script setup>
import { ref, defineEmits } from 'vue';

const files = ref([]);
const fileInput = ref(null);

const emit = defineEmits(['file-selected', 'clear-files']);

// Función para abrir el input de archivos
const triggerFileInput = () => {
  fileInput.value.click();
};

const onFileSelect = (event) => {
  const selectedFiles = Array.from(event.target.files);
  addValidFiles(selectedFiles);
};

const onDragOver = (event) => {
  event.dataTransfer.dropEffect = "copy";
};

const onDrop = (event) => {
  const droppedFiles = Array.from(event.dataTransfer.files);
  addValidFiles(droppedFiles);
};

const addValidFiles = (incomingFiles) => {
  const validFiles = incomingFiles.filter(isValidFile);
  if (validFiles.length > 0) {
    files.value = [...files.value, ...validFiles];
    emit('file-selected', files.value);
  } else {
    alert("Solo se permiten archivos Word o PDF.");
  }
};

const clearFiles = () => {
  files.value = [];
  emit('clear-files'); // Emitir evento para notificar al padre
};

const removeFile = (index) => {
  files.value.splice(index, 1);
  emit('file-selected', files.value);
};

const isValidFile = (file) => {
  const allowedTypes = [
    "application/pdf",
    "application/msword",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
  ];
  return file && allowedTypes.includes(file.type);
};

const isPdf = (fileName) => {
  return fileName.toLowerCase().endsWith('.pdf');
};

const isWord = (fileName) => {
  return fileName.toLowerCase().endsWith('.doc') || fileName.toLowerCase().endsWith('.docx');
};
</script>

<style scoped>
.upload-container {
  border: 2px dashed #ccc;
  padding: 20px;
  text-align: center;
  cursor: pointer;
}
.upload-container:hover {
  border-color: #888;
}
.file-card {
  /* Aseguramos una forma cuadrada */
  width: 150px;
  height: 150px;
}
.file-icon {
  font-size: 3rem; /* Ajusta el tamaño según sea necesario */ 
}
.file-name {
  margin-top: auto;
}
</style>
