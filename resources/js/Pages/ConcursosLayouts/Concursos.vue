<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import TarjetaCrearConcurso from '@/Components/TarjetaCrearConcurso.vue';
import TarjetaConcurso from '@/Components/TarjetaConcurso.vue';
import NuevoConcurso from '@/ComponentsConcursos/NuevoConcurso.vue';
import RegistroProyectos from '@/ComponentsConcursos/RegistroProyectos.vue';
import GestionProyectos from '@/Pages/ConcursosLayouts/GestionProyectos.vue'; // nuevo componente

const selectedMenu = ref('Concursos');
const showForm = ref(false);
const concursoSeleccionado = ref(null);
const mensajeExito = computed(() => props.flash.success || '');

const { props } = usePage();
const userRole = props.auth.user.rol;
const concursos = ref(props.concursos || []);
const inscrito = ref(props.inscrito || false);
const concursoEnPantalla = ref(null);

const obtenerConcursoEnPantalla = () => {
  const usuarioLider = props.auth.user;
  let concurso = concursos.value.find(c => c.lider_id === usuarioLider.id);
  if (!concurso && concursos.value.length > 0) {
    concurso = concursos.value[0]; // Selecciona el primer concurso disponible
  }
  if (concurso) {
    concursoEnPantalla.value = concurso;
  } else {
    console.error('No se encontró un concurso para el usuario líder ni hay concursos disponibles.');
  }
};

const handleMenuSelected = (menu) => {
  selectedMenu.value = menu.toLowerCase(); // forzamos a minúsculas
  showForm.value = selectedMenu.value !== 'concursos';
  if (selectedMenu.value === 'gestión de proyectos') {
    router.get(route('gestion.proyectos'));
  } else if (selectedMenu.value === 'concursos') {
    concursoSeleccionado.value = null;
    obtenerConcursoEnPantalla();
  }
};



const handleDownloadPDF = () => {
  window.open(route('proyectos.pdf'), '_blank');
};


const handleCreateClick = () => {
  selectedMenu.value = 'nuevo concurso';
  showForm.value = true;
};

const handleEditar = (concurso) => {
  if (concurso && concurso.id) {
    router.get(route('concursos.edit', concurso.id));
  } else {
    console.error('Objeto concurso inválido:', concurso);
  }
};  

const handleEliminar = (concurso) => {
  if (confirm('¿Estás seguro de eliminar este concurso?')) {
    router.delete(route('concursos.destroy', concurso.id), {
      onSuccess: () => {
        router.reload();
      }
    });
  }
};

const handleCloseForm = () => {
  showForm.value = false;
};

const handleConcursoClick = (concurso) => {
  if (concurso && concurso.id) {
    if (inscrito.value) {
      // Si ya está inscrito, redirigir a "Gestión de proyectos"
      router.get(route('gestion.proyectos'));
    } else {
      // Si no está inscrito, mostrar el formulario de registro
      selectedMenu.value = 'registro';
      showForm.value = true;
      concursoSeleccionado.value = concurso.id;
    }
  } else {
    console.error('Invalid concurso object:', concurso);
  }
};
obtenerConcursoEnPantalla();
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Crear Concurso
      </h2>
    </template>
    <div v-if="mensajeExito" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
      <span class="block sm:inline">{{ mensajeExito }}</span>
    </div>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <!-- Menú lateral -->
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />

      <!-- Contenido principal -->
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          {{ selectedMenu ? selectedMenu.charAt(0).toUpperCase() + selectedMenu.slice(1) : '' }}
        </h2>

        <!-- Contenedor flex para el botón -->
        <div class="flex justify-end">
          <button 
            v-if="selectedMenu === 'concursos'" 
            @click="handleDownloadPDF" 
            class="mt-3 bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-700"
          >
            Descargar PDF
          </button>
        </div>

        <!-- Formulario para Registro -->
        <div v-if="showForm" class="relative">
          <NuevoConcurso v-if="selectedMenu === 'nuevo concurso'" @close="handleCloseForm" />
          <RegistroProyectos v-if="selectedMenu === 'registro'" :concurso-id="concursoSeleccionado" @close="handleCloseForm" />
        </div>

       
        <!-- Tarjetas de concursos -->
        <div v-if="selectedMenu === 'concursos'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <TarjetaCrearConcurso 
            v-if="$page.props.auth.user.rol === 'admin' || $page.props.auth.user.rol === 'vinculador'"
            @click="handleCreateClick" 
            class="transition-transform transform hover:scale-105 hover:shadow-lg" 
          />
          <TarjetaConcurso                        
            v-for="concurso in concursos"
            :key="concurso.id"
            :concurso="concurso"
            :titulo="concurso.nombre"
            :fechaInicio="concurso.fecha_inicio"
            :fechaApertura="concurso.fecha_apertura"
            :fechaFinalizacion="concurso.fecha_terminacion"
            :inscrito="inscrito"
            @click="handleConcursoClick(concurso)"
            @editar="handleEditar"
            @eliminar="handleEliminar"
            class="transition-transform transform hover:scale-105 hover:shadow-lg"
          />
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>