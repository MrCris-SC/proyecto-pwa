<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import TarjetaCrearConcurso from '@/Components/TarjetaCrearConcurso.vue';
import TarjetaConcurso from '@/Components/TarjetaConcurso.vue';
import NuevoConcurso from '@/ComponentsConcursos/NuevoConcurso.vue';
import RegistroProyectos from '@/ComponentsConcursos/RegistroProyectos.vue';

const selectedMenu = ref('Concursos');
const showForm = ref(false);
const concursoSeleccionado = ref(null);

const { props } = usePage();
const userRole = props.auth.user.rol;
const concursos = ref(props.concursos || []);

const handleMenuSelected = (menu) => {
  selectedMenu.value = menu;
  showForm.value = false;
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
    selectedMenu.value = 'registro';
    showForm.value = true;
    concursoSeleccionado.value = concurso.id;
  } else {
    console.error('Invalid concurso object:', concurso);
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Crear Concurso
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <!-- Menú lateral -->
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />

      <!-- Contenido principal -->
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          {{ selectedMenu.charAt(0).toUpperCase() + selectedMenu.slice(1) }}
        </h2>

        <!-- Formulario para Registro -->
        <div v-if="showForm" class="relative">
          <NuevoConcurso v-if="selectedMenu === 'nuevo concurso'" @close="handleCloseForm" />
          <RegistroProyectos v-if="selectedMenu === 'registro'" :concurso-id="concursoSeleccionado" @close="handleCloseForm" />
        </div>

        <!-- Tarjetas de concursos -->
        <div v-if="selectedMenu === 'Concursos' && !showForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
