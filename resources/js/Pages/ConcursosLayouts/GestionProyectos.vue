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
import DocumentosOpcionales from '@/ComponentsConcursos/DocumentosOpcionales.vue';

const mostrarFormulario = ref(false);
const selectedMenu = ref('Gestión de proyectos');

const { props } = usePage();
const showForm = ref(false);
const proyecto = ref(props.proyecto || {});
const asesorescheck = ref(props.asesorescheck || false);

const documentos = ref([
  { nombre: 'Formato de Registro (FOREG)', estado: 'Pendiente', archivo: null },
  { nombre: 'Formato de Autorización de Participación (FOAPA)', estado: 'Pendiente', archivo: null },
  { nombre: 'Compromiso de Ética y Originalidad (FOCOMO)', estado: 'Pendiente', archivo: null },
  { nombre: 'Formato de Asesores (FOAS)', estado: 'Pendiente', archivo: null },
]);
const opcionales = ref([]);
const inscrito = ref(props.inscrito || false);

onMounted(async () => {
  if (inscrito.value) {
    try {
      const response = await axios.get(`/api/proyectos/${props.auth.user.proyecto_id}`);
      proyecto.value = response.data.proyecto || {};
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
      location.reload();
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
  if (menu === 'Concursos') {
    router.get(route('concursos.index'));
  }
  // No necesitamos hacer nada si el menú es 'Gestión de proyectos'
};

const handleOpcionalesSubidos = (archivos) => {
  opcionales.value = archivos;
};

const enviarDocumentosOpcionales = async () => {
  if (opcionales.value.length === 0) {
    alert('No has seleccionado ningún documento opcional.');
    return;
  }

  const formData = new FormData();
  opcionales.value.forEach((archivo, index) => {
    formData.append(`opcionales[${index}]`, archivo);
  });

  try {
    const response = await axios.post(`/api/proyectos/${proyecto.value.id}/opcionales`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    if (response.status === 200) {
      alert('Documentos opcionales subidos exitosamente.');
      opcionales.value = [];
    } else {
      alert('Error al subir los documentos opcionales.');
    }
  } catch (error) {
    console.error('Error en la subida de documentos opcionales:', error);
    alert('Hubo un problema al subir los documentos opcionales.');
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
          {{ selectedMenu }}
        </h2>

        <div class="p-8 bg-white rounded-lg shadow-lg">
          <!-- Mensaje de inscripción -->
          <div v-if="inscrito" class="mb-4">
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
          <div class="mb-8" v-if="!mostrarFormulario">
            <h3 class="text-xl font-semibold text-[#611232] mb-4">Descargar Formatos</h3>
            <p class="text-gray-700 mb-4">
              Descarga los formatos requeridos, llénalos y súbelos una vez completos.
            </p>

            <!-- Documentos Comunes (Prototipos y Emprendimientos) -->
            <div class="mb-6">
              <h4 class="text-lg font-semibold text-[#611232] mb-2">Documentos Comunes (Prototipos y Emprendimientos):</h4>
              <ul class="list-disc list-inside text-gray-700">
                <li>
                  <strong>Formato de Registro (FOREG)</strong> - <span class="text-blue-600">(Plantilla disponible)</span>
                </li>
                <li>
                  <strong>Formato de Autorización de Participación (FOAPA)</strong> - <span class="text-blue-600">(Plantilla disponible)</span>
                </li>
                <li>
                  <strong>Compromiso de Ética y Originalidad (FOCOMO)</strong> - <span class="text-blue-600">(Plantilla disponible)</span>
                </li>
                <li>
                  <strong>Formato de Asesores (FOAS)</strong> - <span class="text-blue-600">(Plantilla disponible)</span>
                </li>
                <li>
                  <strong>Bitácora</strong> - <span class="text-red-600">(Sin plantilla, el participante lo sube)</span>
                </li>
                <li>
                  <strong>Informe de Investigación</strong> - <span class="text-red-600">(Sin plantilla, el participante lo sube)</span>
                </li>
              </ul>
            </div>

            <!-- Documentos Específicos para Prototipos -->
            <div class="mb-6">
              <h4 class="text-lg font-semibold text-[#611232] mb-2">Documentos para Prototipos:</h4>
              <ul class="list-disc list-inside text-gray-700">
                <li>
                  <strong>Manual de Instalación, Operación y/o Usuario</strong> - <span class="text-red-600">(Sin plantilla, el participante lo sube)</span>
                </li>
                <li>
                  <strong>Formato de Continuidad de Proyecto (FOCP)</strong> - <span class="text-blue-600">(Plantilla disponible)</span>
                </li>
                <li>
                  <strong>Formato de Humanos como Sujetos de Estudio (FOHE)</strong> - <span class="text-blue-600">(Plantilla disponible)</span>
                </li>
                <li>
                  <strong>Formato para Uso de Tejidos u Órganos de Animales Vertebrados (FOTAV)</strong> - <span class="text-blue-600">(Plantilla disponible)</span>
                </li>
                <li>
                  <strong>Formato para Uso de Animales Vertebrados (FOPAV)</strong> - <span class="text-blue-600">(Plantilla disponible)</span>
                </li>
              </ul>
            </div>

            <!-- Documentos Específicos para Emprendimientos -->
            <div class="mb-6">
              <h4 class="text-lg font-semibold text-[#611232] mb-2">Documentos para Emprendimientos:</h4>
              <ul class="list-disc list-inside text-gray-700">
                <li>
                  <strong>Modelo CANVAS</strong> - <span class="text-red-600">(Sin plantilla, el participante lo sube)</span>
                </li>
                <li>
                  <strong>Estudio de Mercado</strong> - <span class="text-red-600">(Sin plantilla, el participante lo sube)</span>
                </li>
                <li>
                  <strong>Plan de Negocios</strong> - <span class="text-red-600">(Sin plantilla, el participante lo sube)</span>
                </li>
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

          <!-- Documentos Opcionales -->
          <div class="mt-8" v-if="!mostrarFormulario">
            <h3 class="text-xl font-semibold text-[#611232] mb-4">Documentos Opcionales</h3>
            <p class="text-gray-700 mb-4">
              Puedes subir documentos opcionales en formato Word o PDF.
            </p>
              <DocumentosOpcionales @file-selected="handleOpcionalesSubidos" />
              <button
                v-if="!mostrarFormulario"
                class="bg-[#611232] text-white px-6 py-2 rounded-lg hover:bg-[#8A1C4A] transition duration-200 mt-4"
                @click="enviarDocumentosOpcionales"
              >
                Enviar Documentos Opcionales
              </button>
          </div>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>