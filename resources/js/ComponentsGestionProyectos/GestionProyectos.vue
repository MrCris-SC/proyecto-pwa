<template>
  <div class="p-8 bg-white rounded-lg shadow-lg">

    <!-- Resumen del Proyecto -->
    <div class="mb-8 p-6 bg-gray-50 rounded-lg">
      <h2 class="text-xl font-semibold text-[#611232] mb-4">Resumen del Proyecto</h2>
      <div class="space-y-2">
        <p><strong>Nombre del Proyecto:</strong> Prototipo de Energía Solar</p>
        <p><strong>Estado:</strong> Fase Local - Pendiente de evaluación</p>
        <p><strong>Progreso:</strong> 70% completado</p>
      </div>
    </div>

    <!-- Documentación Requerida -->
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
                @click="subirDocumento(doc)"
              >
                Subir
              </button>
              <button 
                v-else 
                class="bg-[#8A1C4A] text-white px-3 py-1 rounded hover:bg-[#611232] transition duration-200"
                @click="editarDocumento(doc)"
              >
                Editar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Tareas Pendientes -->
    <div class="mb-8">
      <h2 class="text-xl font-semibold text-[#611232] mb-4">Tareas Pendientes</h2>
      <ul class="space-y-2">
        <li v-for="tarea in tareas" :key="tarea.descripcion" class="p-3 bg-gray-50 rounded-lg">
          <p class="font-medium">{{ tarea.descripcion }}</p>
          <p class="text-sm text-gray-600">Fecha límite: {{ tarea.fechaLimite }}</p>
        </li>
      </ul>
    </div>

    <!-- Comunicación con el Equipo -->
    <div>
      <h2 class="text-xl font-semibold text-[#611232] mb-4">Comunicación con el Equipo</h2>
      <div class="bg-gray-50 p-6 rounded-lg">
        <textarea 
          class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
          placeholder="Escribe un mensaje..."
          v-model="mensaje"
        ></textarea>
        <button 
          class="mt-4 bg-[#611232] text-white px-6 py-2 rounded-lg hover:bg-[#8A1C4A] transition duration-200"
          @click="enviarMensaje"
        >
          Enviar Mensaje
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const proyecto = ref({});
const documentos = ref([]);
const tareas = ref([]);
const mensaje = ref('');

onMounted(async () => {
  // Obtener datos del proyecto desde el backend
  const response = await axios.get(`/api/proyectos/${props.auth.user.proyecto_id}`);
  proyecto.value = response.data.proyecto;
  documentos.value = response.data.documentos;
  tareas.value = response.data.tareas;
});

const subirDocumento = (documento) => {
  console.log('Subir documento:', documento);
};

const editarDocumento = (documento) => {
  console.log('Editar documento:', documento);
};

const enviarMensaje = () => {
  if (mensaje.value.trim()) {
    console.log('Mensaje enviado:', mensaje.value);
    mensaje.value = '';
  } else {
    alert('Por favor, escribe un mensaje.');
  }
};
</script>

<style scoped>
/* Estilos personalizados */
</style>