<template>
  <div class="p-8 bg-white rounded-lg shadow-lg">
    <!-- Botón para inscribirse -->
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

    <!-- Resumen del Proyecto -->
    <div class="mb-8 p-6 bg-gray-50 rounded-lg">
      <h2 class="text-xl font-semibold text-[#611232] mb-4">Resumen del Proyecto</h2>
      <div class="space-y-2">
        <p><strong>Nombre del Proyecto:</strong> {{ proyecto.nombre || 'No disponible' }}</p>
        <p><strong>Estado:</strong> {{ proyecto.estado || 'No disponible' }}</p>
        <p><strong>Progreso:</strong> {{ proyecto.progreso || '0' }}% completado</p>
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
        <li v-if="tareas.length === 0" class="p-3 bg-gray-50 rounded-lg text-gray-500">
          No hay tareas pendientes.
        </li>
        <li v-for="tarea in tareas" :key="tarea.descripcion" class="p-3 bg-gray-50 rounded-lg">
          <p class="font-medium">{{ tarea.descripcion }}</p>
          <p class="text-sm text-gray-600">Fecha límite: {{ tarea.fechaLimite || 'No definida' }}</p>
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
import { usePage, router } from '@inertiajs/vue3';

const { props } = usePage();
const proyecto = ref({});
const documentos = ref([]);
const tareas = ref([]);
const mensaje = ref('');
const inscrito = ref(props.inscrito || false);

onMounted(async () => {
  if (inscrito.value) {
    // Obtener datos del proyecto desde el backend
    try {
      const response = await axios.get(`/api/proyectos/${props.auth.user.proyecto_id}`);
      proyecto.value = response.data.proyecto || {};
      documentos.value = response.data.documentos || [];
      tareas.value = response.data.tareas || [];
    } catch (error) {
      console.error('Error al obtener los datos del proyecto:', error);
    }
  } else {
    // Si no está inscrito, mostrar datos vacíos
    proyecto.value = {};
    documentos.value = [];
    tareas.value = [];
  }
});

const subirDocumento = (documento) => {
  console.log('Subir documento:', documento);
  // Lógica para subir el documento
};

const editarDocumento = (documento) => {
  console.log('Editar documento:', documento);
  // Lógica para editar el documento
};

const enviarMensaje = () => {
  if (mensaje.value.trim()) {
    console.log('Mensaje enviado:', mensaje.value);
    mensaje.value = '';
  } else {
    alert('Por favor, escribe un mensaje.');
  }
};

const inscribirse = () => {
  router.post(`/concursos/${props.concursoId}/inscribirse`, {}, {
    onSuccess: () => {
      // Actualizar el estado y redirigir
      inscrito.value = true;
      router.visit('/gestion-de-proyectos');
    },
    onError: (errors) => {
      console.error('Error al inscribirse:', errors);
    },
  });
};
</script>

<style scoped>
/* Estilos personalizados */
</style>