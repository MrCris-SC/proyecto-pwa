<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import TarjetaCrearConcurso from '@/Components/TarjetaCrearConcurso.vue';
import TarjetaConcurso from '@/Components/TarjetaConcurso.vue';
import NuevoConcurso from '@/ComponentsConcursos/NuevoConcurso.vue';
import RegistroProyectos from '@/ComponentsConcursos/RegistroProyectos.vue';
import GestionProyectos from '@/Pages/ConcursosLayouts/GestionProyectos.vue';
import ModalConfirmacion from '@/Components/ModalConfirmacion.vue';
import RegistroCriterios from '@/Pages/ConcursosLayouts/RegistroCriterios.vue';
import ModalEvaluaciones from '@/Components/ModalEvaluaciones.vue';
import axios from 'axios';

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
    concurso = concursos.value[0];
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

  if (selectedMenu.value === 'concursos') {
    // Asegúrate de que se muestre la lista de concursos
    showForm.value = false;
  } else if (selectedMenu.value === 'gestión de proyectos') {
    router.get(route('gestion.proyectos')); // Redirige a gestión de proyectos
  } else if (selectedMenu.value === 'registro de criterios') {
    // Cambia el componente dinámico sin recargar la página
    showForm.value = true;
  } else if (selectedMenu.value === 'equipos registrados') {
    router.get(route('equipos.registrados')); // Redirige a la ruta actualizada para todos los equipos
  }
};

const handleDownloadPDF = () => {
  window.open(route('proyectos.pdf'), '_blank');
};

const handleCreateClick = () => {
  selectedMenu.value = 'Nuevo concurso';
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

const mostrarModalCerrar = ref(false);
const concursoParaCerrar = ref(null);

const handleCerrar = (concurso) => {
  concursoParaCerrar.value = concurso;
  mostrarModalCerrar.value = true;
};

const confirmarCerrarConcurso = () => {
  if (concursoParaCerrar.value && concursoParaCerrar.value.id) {
    router.post(route('concursos.cambiar.estado', concursoParaCerrar.value.id), {
      nuevo_estado: 'cerrado',
    }, {
      onSuccess: () => {
        mostrarModalCerrar.value = false;
        router.reload();
      },
      onError: (error) => {
        console.error('Error al cerrar el concurso:', error);
      },
    });
  }
};

const mostrarModalInscripcion = ref(false);

const handleConcursoClick = (concurso) => {
  if (concurso && concurso.id) {
    if (userRole === 'evaluador') {
      // Use the 'inscrito' flag from the backend
      if (concurso.inscrito) {
        router.get(route('evaluacion.index')); // Redirect to Evaluacion.vue
      } else {
        concursoSeleccionado.value = concurso;
        mostrarModalInscripcion.value = true; // Show the modal
      }
    } else if (inscrito.value) {
      router.get(route('gestion.proyectos'));
    } else {
      selectedMenu.value = 'Registro';
      showForm.value = true;
      concursoSeleccionado.value = concurso.id;
    }
  } else {
    console.error('Invalid concurso object:', concurso);
  }
};

const inscribirEvaluador = () => {
  if (concursoSeleccionado.value && concursoSeleccionado.value.id) {
    router.post(route('evaluadores.inscribir', concursoSeleccionado.value.id), {}, {
      onSuccess: () => {
        mostrarModalInscripcion.value = false; // Close the modal
        router.reload(); // Reload the page to reflect changes
      },
      onError: (error) => {
        console.error('Error al inscribirse como evaluador:', error);
      },
    });
  }
};

const mostrarModalEvaluaciones = ref(false);
const evaluaciones = ref([]);
const resumenEvaluaciones = ref({ pendientes: 0, completadas: 0 });
const concursoSeleccionadoParaEvaluaciones = ref(null);

const handleConfiguracion = async (concurso) => {
  console.log('Concurso seleccionado para configuración:', concurso); // Log para verificar el concurso seleccionado
  concursoSeleccionadoParaEvaluaciones.value = concurso;

  try {
    const response = await axios.get(route('concursos.evaluaciones', concurso.id));
    if (response.data.success) {
      evaluaciones.value = response.data.evaluaciones;

      // Obtener el resumen de evaluaciones
      const resumenResponse = await axios.get(route('concursos.resumen.evaluaciones', concurso.id));
      if (resumenResponse.data.success) {
        resumenEvaluaciones.value = resumenResponse.data.resumen;
      } else {
        resumenEvaluaciones.value = { pendientes: 0, completadas: 0 };
        console.error('Error al obtener el resumen de evaluaciones:', resumenResponse.data.message);
      }
    } else {
      evaluaciones.value = [];
      resumenEvaluaciones.value = { pendientes: 0, completadas: 0 };
      console.error('Error al obtener las evaluaciones:', response.data.message);
    }
  } catch (error) {
    evaluaciones.value = [];
    resumenEvaluaciones.value = { pendientes: 0, completadas: 0 };
    console.error('Error al obtener las evaluaciones o el resumen:', error);
  }

  mostrarModalEvaluaciones.value = true;
};

const handleFinalizarConcurso = async (concurso) => {
  if (!concurso || !concurso.id) {
    alert('El concurso no es válido.');
    return;
  }

  const confirmacion = confirm(`¿Estás seguro de que deseas finalizar el concurso "${concurso.nombre}"?`);
  if (!confirmacion) return;

  try {
    const response = await axios.post(route('concursos.finalizar', concurso.id));

    if (response.data.success) {
      alert('El concurso ha sido finalizado exitosamente.');
      mostrarModalEvaluaciones.value = false; // Cerrar el modal
      router.reload(); // Recargar la página para reflejar los cambios
    } else {
      alert(response.data.message || 'No se pudo finalizar el concurso. Intenta nuevamente.');
    }
  } catch (error) {
    console.error('Error al finalizar el concurso:', error);
    alert('Ocurrió un error inesperado. Intenta nuevamente.');
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
      <main 
        class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg relative"
        :class="{ 'opacity-50 pointer-events-none': mostrarModalEvaluaciones || mostrarModalInscripcion || mostrarModalCerrar || showForm }"
      >
        <!-- Overlay for modal -->
        <div 
          v-if="mostrarModalEvaluaciones || mostrarModalInscripcion || mostrarModalCerrar || showForm" 
          class="absolute inset-0 bg-gray-800 bg-opacity-50 z-10"
        ></div>

        <div v-if="!mostrarModalEvaluaciones && !mostrarModalInscripcion && !mostrarModalCerrar && !showForm">
          <h2 class="text-2xl font-bold mb-6 text-[#611232]">
            {{ selectedMenu }}
          </h2>

          <!-- Contenedor flex para el botón -->
          <div class="flex justify-end">
            <button 
              v-if="selectedMenu === 'Concursos'" 
              @click="handleDownloadPDF" 
              class="mt-3 bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-700"
            >
              Descargar FOREG
            </button>
          </div>

          <!-- Formulario para Registro -->
          <div v-if="showForm" class="relative">
            <NuevoConcurso v-if="selectedMenu === 'Nuevo concurso'" @close="handleCloseForm" />
            <RegistroProyectos v-if="selectedMenu === 'Registro'" :concurso-id="concursoSeleccionado" @close="handleCloseForm" />
          </div>

          <!-- Tarjetas de concursos -->
          <div v-if="selectedMenu === 'Concursos'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
              :estado="concurso.estado"
              :inscrito="inscrito"
              :isAdmin="$page.props.auth.user.rol === 'admin'"
              @click="handleConcursoClick(concurso)"
              @editar="handleEditar"
              @eliminar="handleEliminar"
              @cerrar="handleCerrar"
              @configuracion="handleConfiguracion"
              @podio="router.get(route('concursos.podio', { id: concurso.id }))"
              class="transition-transform transform hover:scale-105 hover:shadow-lg"
            />
          </div>
        </div>
      </main>
    </div>
    <ModalConfirmacion
      v-if="mostrarModalCerrar"
      @confirmar="confirmarCerrarConcurso"
      @cancelar="mostrarModalCerrar = false"
      mensaje="¿Estás seguro de que deseas cerrar este concurso?"
    />
    <!-- Modal for Evaluator Registration -->
    <ModalConfirmacion
      v-if="mostrarModalInscripcion"
      :mensaje="'¿Deseas inscribirte como evaluador para el concurso ' + concursoSeleccionado?.nombre + '?'"
      @confirmar="inscribirEvaluador"
      @cancelar="mostrarModalInscripcion = false"
    />
    <ModalEvaluaciones
      v-if="mostrarModalEvaluaciones"
      :evaluaciones="evaluaciones"
      :concurso="concursoSeleccionadoParaEvaluaciones" 
      :resumenEvaluaciones="resumenEvaluaciones"
      @finalizar="handleFinalizarConcurso"
      @close="mostrarModalEvaluaciones = false"
    />
  </AuthenticatedLayout>
</template>