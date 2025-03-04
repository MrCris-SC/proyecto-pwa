<script setup>

import { ref, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import RegistroAsesores from '../../ComponentsConcursos/RegistroAsesores.vue';

import RegistroAsesoresdos from '@/Components/RegistroAsesoresdos.vue';
import ResumenProyecto from '@/Components/ResumenProyecto.vue';
import InscripcionConcurso from '@/Components/InscripcionConcurso.vue';
import DocumentosTable from '@/Components/DocumentosTable.vue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';    
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import axios from 'axios';


const mostrarFormulario = ref(false);

const { props } = usePage();
const showForm = ref(false);
const proyecto = ref(props.proyecto || {});
const documentos = ref([]);
const inscrito = ref(props.inscrito || false);

onMounted(async () => {
  if (inscrito.value) {
    try {
      const response = await axios.get(`/api/proyectos/${props.auth.user.proyecto_id}`);
      proyecto.value = response.data.proyecto || {};
      documentos.value = response.data.documentos || [];
    } catch (error) {
      console.error('Error al obtener los datos del proyecto:', error);
    }
  } else {
    proyecto.value = {};
    documentos.value = [];
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

const handleCloseForm = () => {
  mostrarFormulario.value = false;
  showForm.value = false;
};

const handleRegistroAsesores = () => {
  mostrarFormulario.value = true;
  showForm.value = true;
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
          <!-- Inscripción al Concurso -->
          <InscripcionConcurso :inscrito="inscrito" @inscribirse="inscribirse" />

          <!-- Botón para abrir el formulario de registro de asesores -->
          <div class="mb-8" v-if="!mostrarFormulario">
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
              <RegistroAsesores @close="handleCloseForm" />
            </div>
          </div>

          <!-- Resumen del Proyecto -->
          <ResumenProyecto :proyecto="proyecto" />

          <!-- Documentación Requerida -->
          <DocumentosTable
            :documentos="documentos"
            @subir-documento="subirDocumento"
            @editar-documento="editarDocumento"
          />
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>