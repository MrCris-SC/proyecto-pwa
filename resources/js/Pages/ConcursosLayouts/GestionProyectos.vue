<script setup>
import { ref, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import RegistroAsesores from '../../ComponentsConcursos/RegistroAsesores.vue';
import ResumenProyecto from '@/Components/ResumenProyecto.vue';
import InscripcionConcurso from '@/Components/InscripcionConcurso.vue';
import DocumentosTable from '@/Components/DocumentosTable.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import axios from 'axios';

const mostrarFormulario = ref(false);
const selectedMenu = ref('Gestión de proyectos'); // Agregar selectedMenu con valor inicial

const { props } = usePage();
const showForm = ref(false);
const proyecto = ref(props.proyecto || {});
const asesorescheck = ref(props.asesorescheck || false); // Agregar foregcheck


// Inicializa la lista de documentos con los tres documentos requeridos
const documentos = ref([
  { nombre: 'Formato de Registro (FOREG)', estado: 'Pendiente' },
  { nombre: 'Formato de Autorización de Participación (FOAPA)', estado: 'Pendiente' },
  { nombre: 'Compromiso de Ética y Originalidad (FOCOMO)', estado: 'Pendiente' },
  { nombre: 'Formato de Asesores (FOAS)', estado: 'Pendiente', archivo: null }
]);

const inscrito = ref(props.inscrito || false);

onMounted(async () => {
  if (inscrito.value) {
    try {
      const response = await axios.get(`/api/proyectos/${props.auth.user.proyecto_id}`);
      proyecto.value = response.data.proyecto || {};
      // Si hay documentos en la respuesta, actualiza la lista
      if (response.data.documentos && response.data.documentos.length > 0) {
        documentos.value = response.data.documentos;
      }
    } catch (error) {
      console.error('Error al obtener los datos del proyecto:', error);
    }
  } else {
    proyecto.value = {};
  }
});
// Manejar la carga del documento
// Manejar la carga de un documento
const subirDocumento = ({ index, file }) => {
  documentos.value[index].archivo = file;
  documentos.value[index].estado = 'Completado';
};

const editarDocumento = (doc) => {
  console.log('Editar documento:', doc);
};

const eliminarDocumento = (index) => {
  documentos.value[index].archivo = null;
  documentos.value[index].estado = 'Pendiente';
};

const handleFilesDropped = (event) => {
  console.log('Archivos arrastrados:', event);
};

// Enviar los documentos al servidor
const enviarDocumentos = async () => {
  const formData = new FormData();

  const documentosCompletos = documentos.value.every(doc => doc.archivo !== null);
  if (!documentosCompletos) {
    alert('Debes cargar los 4 documentos antes de enviarlos.');
    return;
  }

  documentos.value.forEach((doc, index) => {
    formData.append(`documentos[${index}]`, doc.archivo);
  });

  try {
    const response = await axios.post(`/api/proyectos/${proyecto.value.id}/documentos`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    if (response.status === 200) {
      alert('Documentos subidos exitosamente.');
      location.reload(); // Refresh the page
    } else {
      alert('Error al subir los documentos.');
    }
  } catch (error) {
    console.error('Error en la subida:', error);
    alert('Hubo un problema al subir los documentos.');
  }
};

const inscribirse = () => {
  router.post(`/concursos/${props.concursoId}/inscribirse`, {}, {
    onSuccess: () => {
      inscrito.value = true;
      router.visit('/gestion-de-proyectos');
    },
    onError: (errors) => {
      console.error('Error al inscribirse:', errors);
    },
  });
};

const handleRegistroAsesores = () => {
  mostrarFormulario.value = true;
  showForm.value = true;
};

const handleCloseForm = () => {
  mostrarFormulario.value = false;
  showForm.value = false;
};

const handleMenuSelected = (menu) => {
  if (menu === 'concursos') {
    router.get(route('concursos.index'));
  } else if (menu === 'gestion-proyectos') {
    router.get(route('gestion.proyectos'));
  }
};

</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Gestión de Proyectos
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          {{ selectedMenu ? selectedMenu.charAt(0).toUpperCase() + selectedMenu.slice(1) : '' }}
        </h2>

        <div class="p-8 bg-white rounded-lg shadow-lg">
          <!-- Mensaje de inscripción -->
          <div v-if="inscrito" class="mb-4"> <!-- Ajusta el margen inferior aquí -->
            <p class="text-green-600 font-semibold">
              Ya estás inscrito en este concurso.
            </p>
          </div>

          <!-- Resumen del Proyecto -->
          <ResumenProyecto v-if="!mostrarFormulario" :proyecto="proyecto" />

          <!-- Información sobre el registro de asesores -->
          <div class="mb-8" v-if="!mostrarFormulario && !asesorescheck">
            <h3 class="text-xl font-semibold text-[#611232] mb-2">Registro de Asesores</h3>
            <p class="text-gray-700 mb-4">
              Para completar tu inscripción, es necesario registrar a los asesores que guiarán tu proyecto. 
              Debes registrar un asesor técnico y un asesor metodológico.
            </p>
            <button 
              class="bg-[#611232] text-white px-6 py-2 rounded-lg hover:bg-[#8A1C4A] transition duration-200"
              @click="handleRegistroAsesores"
            >
              Registrar Asesores
            </button>
          </div>

          <!-- Registro de Asesores -->
          <div v-if="showForm" class="relative">
            <div v-if="mostrarFormulario" class="mb-8 relative">
              <RegistroAsesores @close="handleCloseForm"/>
            </div>
          </div>

          <!-- Descargar Formatos -->
          <div class="mb-8">
            <h3 class="text-xl font-semibold text-[#611232] mb-4">Descargar Formatos</h3>
            <p class="text-gray-700 mb-4">
              Descarga los formatos requeridos, llénalos y súbelos una vez completos.
            </p>

            <!-- Lista de archivos incluidos en el archivo a descargar -->
            <div class="mb-6">
              <h4 class="text-lg font-semibold text-[#611232] mb-2">Archivos incluidos:</h4>
              <ul class="list-disc list-inside text-gray-700">
                <li>Formato de Registro (FOREG)</li>
                <li>Formato de Autorización de Participación (FOAPA)</li>
                <li>Compromiso de Ética y Originalidad (FOCOMO)</li>
              </ul>
            </div>

            <!-- Botón de descarga -->
            <a :href="route('descargar.formatos')" class="bg-[#611232] text-white px-6 py-2 rounded-lg hover:bg-[#8A1C4A] transition duration-200">
              <i class="fas fa-download mr-2"></i> Descargar Formatos
            </a>
          </div>

          <!-- Documentación Requerida -->
          <DocumentosTable
            v-if="!mostrarFormulario"
            :documentos="documentos"
            @subir-documento="subirDocumento"
            @eliminar-documento="eliminarDocumento"
            @files-dropped="handleFilesDropped"
          />
          <button
            v-if="!mostrarFormulario"
            class="bg-[#611232] text-white px-6 py-2 rounded-lg hover:bg-[#8A1C4A] transition duration-200 mt-4"
            @click="enviarDocumentos"
          >
            Enviar Documentos
          </button>

        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>