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
import RegistroCriterios from '@/Pages/ConcursosLayouts/RegistroCriterios.vue';

const selectedMenu = ref('Concursos');
const showForm = ref(false);
const concursoSeleccionado = ref(null);
const mensajeExito = computed(() => props.flash.success || '');

const { props } = usePage();
const userRole = props.auth.user.rol;
const concursos = ref(props.concursos || []);
const inscrito = ref(props.inscrito || false);
const concursoEnPantalla = ref(null);
const criterios = ref(props.criterios || []);

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
    selectedMenu.value = menu.toLowerCase(); // Actualiza el menú seleccionado
    showForm.value = selectedMenu.value !== 'concursos'; // Controla si se muestra un formulario

    if (selectedMenu.value === 'gestión de proyectos') {
        router.get(route('gestion.proyectos')); // Redirige a gestión de proyectos
    } else if (selectedMenu.value === 'registro de criterios') {
        // Asegúrate de que esta ruta sea válida y no se sobrescriba
        router.get(route('criterios.registro'));
    } else if (selectedMenu.value === 'concursos') {
        concursoSeleccionado.value = null;
        obtenerConcursoEnPantalla(); // Restablece el concurso en pantalla
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
        {{ selectedMenu ? selectedMenu.charAt(0).toUpperCase() + selectedMenu.slice(1) : '' }}
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <!-- Menú lateral -->
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />

      <!-- Contenido principal -->
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          {{ selectedMenu ? selectedMenu.charAt(0).toUpperCase() + selectedMenu.slice(1) : '' }}
        </h2>

        <!-- Mostrar contenido según el menú seleccionado -->
        <div v-if="selectedMenu === 'concursos'">
          <!-- Contenido de concursos -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
              :fase="concurso.fase" 
              :inscrito="inscrito"
              :isAdmin="$page.props.auth.user.rol === 'admin'"
              @click="handleConcursoClick(concurso)"
              @editar="handleEditar"
              @eliminar="handleEliminar"
              class="transition-transform transform hover:scale-105 hover:shadow-lg"
            />
          </div>
        </div>

        <div v-else-if="selectedMenu === 'registro de criterios'">
          <!-- Contenido de Registro de Criterios -->
          <RegistroCriterios :concursos="concursos" :criteriosExistentes="criterios" />
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>