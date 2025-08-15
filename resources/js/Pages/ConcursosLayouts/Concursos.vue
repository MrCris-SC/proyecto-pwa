<script setup>
// Importaciones de Vue y librerías necesarias
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
import CrearEvaluacionManual from '@/ComponentsConcursos/CrearEvaluacionManual.vue';
import axios from 'axios';
import vistaConcursosAdmin from '@/ComponentsConcursos/vistaConcursosAdmin.vue';
import Swal from 'sweetalert2';

// Variables reactivas para controlar el estado de la vista y los formularios
const selectedMenu = ref('Concursos'); // Menú seleccionado actualmente
const showForm = ref(false); // Controla si se muestra un formulario
const concursoSeleccionado = ref(null); // Concurso seleccionado para acciones
const mensajeExito = computed(() => props.flash.success || ''); // Mensaje de éxito desde el backend

// Obtener propiedades globales de la página (usuario, concursos, etc.)
const { props } = usePage();
const userRole = props.auth.user.rol; // Rol del usuario autenticado
const concursos = ref(props.concursos || []); // Lista de concursos
const inscrito = ref(props.inscrito || false); // Si el usuario está inscrito en algún concurso
const concursoEnPantalla = ref(null); // Concurso principal mostrado en pantalla
const clasificaciones = ref(props.auth.user.clasificaciones || []); // Debes pasar esto desde el backend

// Busca el concurso en pantalla según el usuario líder o el primero disponible
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

// Maneja la selección de opciones en el menú lateral
const handleMenuSelected = (menu) => {
  selectedMenu.value = menu.toLowerCase();
  showForm.value = selectedMenu.value !== 'concursos';

  if (selectedMenu.value === 'concursos') {
    showForm.value = false;
  } else if (selectedMenu.value === 'gestión de proyectos') {
    router.get(route('gestion.proyectos'));
  } else if (selectedMenu.value === 'registro de criterios') {
    showForm.value = true;
  } else if (selectedMenu.value === 'equipos registrados') {
    router.get(route('equipos.registrados'));
  }
};

// Descarga el PDF de proyectos (FOREG)
const handleDownloadPDF = () => {
  window.open(route('proyectos.pdf'), '_blank');
};

// Muestra el formulario para crear un nuevo concurso
const handleCreateClick = () => {
  selectedMenu.value = 'Nuevo concurso';
  showForm.value = true;
};

// Redirige a la edición de un concurso
const handleEditar = (concurso) => {
  if (concurso && concurso.id) {
    router.get(route('concursos.edit', concurso.id));
  } else {
    console.error('Objeto concurso inválido:', concurso);
  }
};  

// Elimina un concurso después de confirmación
const handleEliminar = (concurso) => {
  if (confirm('¿Estás seguro de eliminar este concurso?')) {
    router.delete(route('concursos.destroy', concurso.id), {
      onSuccess: () => {
        router.reload();
      }
    });
  }
};

// Cierra el formulario y recarga la página
const handleCloseForm = () => {
  showForm.value = false;
  window.location.reload();
};

// Variables y funciones para el modal de confirmación de cierre de concurso
const mostrarModalCerrar = ref(false);
const concursoParaCerrar = ref(null);
const modalCerrarMensaje = ref('¿Estás seguro de que deseas cerrar este concurso?');

// Muestra el modal para cerrar un concurso
const handleCerrar = (concurso) => {
  concursoParaCerrar.value = concurso;
  mostrarModalCerrar.value = true;
  modalCerrarMensaje.value = '¿Estás seguro de que deseas cerrar este concurso?';
};

// Muestra el modal de cierre desde el componente de evaluaciones
const handleCerrarConcursoDesdeEvaluaciones = (concurso) => {
  concursoParaCerrar.value = concurso;
  mostrarModalCerrar.value = true;
  modalCerrarMensaje.value = '¿Está seguro que desea cambiar el estado del concurso a cerrado?';
};

// Confirma el cierre del concurso y actualiza el estado en el backend
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

// Variables y funciones para el modal de inscripción de evaluador
const mostrarModalInscripcion = ref(false);



const handleConcursoClick = async (concurso) => {
  if (!concurso || !concurso.id) {
    console.error('Invalid concurso object:', concurso);
    return;
  }

  const status = (concurso?.status ?? '').toString().toLowerCase().trim();
  const user = props.auth.user;

  // Si el concurso ya finalizó
  if (status === 'finalizado') {
    await Swal.fire({
      icon: 'warning',
      title: 'Concurso finalizado',
      text: 'El concurso ha finalizado y no se pueden hacer cambios.',
      confirmButtonText: 'Entendido'
    });
    return;
  }

  // Si está cerrado y el líder no está registrado
  if (userRole === 'lider' && status === 'cerrado' && !user.concurso_registrado_id) {
    await Swal.fire({
      icon: 'error',
      title: 'Inscripción no permitida',
      text: 'No puedes inscribirte en un concurso cerrado.',
      confirmButtonText: 'Ok'
    });
    return;
  }

  if (userRole === 'admin') {
    concursoSeleccionadoAdmin.value = concurso;
    await cargarEquiposConcurso(concurso.id);
    mostrarModalVistaConcursosAdmin.value = true;
    return;
  }

  if (userRole === 'evaluador') {
    if (concurso.inscrito) {
      router.get(route('evaluacion.index'));
    } else {
      concursoSeleccionado.value = concurso;
      mostrarModalInscripcion.value = true;
    }
    return;
  }

  if (userRole === 'lider') {
    if (inscrito.value) {
      router.get(route('gestion.proyectos'));
      return;
    }
    selectedMenu.value = 'Registro';
    showForm.value = true;
    concursoSeleccionado.value = concurso.id;
    return;
  }

  // Otros usuarios / casos generales
  if (inscrito.value) {
    router.get(route('gestion.proyectos'));
  } else {
    selectedMenu.value = 'Registro';
    showForm.value = true;
    concursoSeleccionado.value = concurso.id;
  }
};



// Inscribe al evaluador en el concurso seleccionado
const inscribirEvaluador = () => {
  if (concursoSeleccionado.value && concursoSeleccionado.value.id) {
    router.post(route('evaluadores.inscribir', concursoSeleccionado.value.id), {}, {
      onSuccess: () => {
        mostrarModalInscripcion.value = false;
        router.reload();
      },
      onError: (error) => {
        console.error('Error al inscribirse como evaluador:', error);
      },
    });
  }
};

// Variables y funciones para el modal de evaluaciones y creación manual de evaluaciones
const mostrarModalEvaluaciones = ref(false);
const evaluaciones = ref([]);
const resumenEvaluaciones = ref({ pendientes: 0, completadas: 0 });
const concursoSeleccionadoParaEvaluaciones = ref(null);
const mostrarCrearEvaluacionManual = ref(false);
const equiposEvaluacion = ref([]);
const evaluadoresEvaluacion = ref([]);

// Muestra el modal de configuración de evaluaciones para un concurso
const handleConfiguracion = async (concurso) => {
  console.log('Concurso seleccionado para configuración:', concurso);
  concursoSeleccionadoParaEvaluaciones.value = concurso;

  try {
    // Obtiene las evaluaciones del concurso
    const response = await axios.get(route('concursos.evaluaciones', concurso.id));
    if (response.data.success) {
      evaluaciones.value = response.data.evaluaciones;

      // Obtiene el resumen de evaluaciones (pendientes y completadas)
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

// Finaliza un concurso después de confirmación
 const handleFinalizarConcurso = async (concurso) => {
  if (!concurso || !concurso.id) {
    alert('El concurso no es válido.');
    return;
  }

  const confirmacion = confirm(`¿Estás seguro de que deseas finalizar el concurso "${concurso.nombre}"?`);
  if (!confirmacion) return;

  try {
    // Si usas Laravel Sanctum y sesión, primero asegúrate del token CSRF
    await axios.get('/sanctum/csrf-cookie');

    const response = await axios.post(
      route('concursos.finalizar', concurso.id),
      {},
      { withCredentials: true } // Esto envía cookies y sesión
    );

    if (response.data.success) {
      alert('El concurso ha sido finalizado exitosamente.');
      mostrarModalEvaluaciones.value = false;
      router.reload();
    } else {
      alert(response.data.message || 'No se pudo finalizar el concurso. Intenta nuevamente.');
    }
  } catch (error) {
    console.error('Error al finalizar el concurso:', error);
    alert('Ocurrió un error inesperado. Intenta nuevamente.');
  }
};


// Redirige a la vista del podio del concurso
const handlePodio = (concurso) => {
  console.log('Concurso clickeado para podio:', concurso.id, concurso.nombre);
  if (concurso && concurso.id) {
    router.get(route('concursos.podio', { id: concurso.id }));
  } else {
    console.error('Concurso inválido para podio:', concurso);
  }
};


// Abre el formulario para crear una evaluación manual
const handleAbrirCrearEvaluacionManual = async () => {
  if (concursoSeleccionadoParaEvaluaciones.value) {
    try {
      // Obtiene los equipos con su proyecto relacionado
      const equiposRes = await axios.get(route('concursos.equipos', concursoSeleccionadoParaEvaluaciones.value.id));
      equiposEvaluacion.value = (equiposRes.data.equipos || []).map(e => ({
        ...e,
        proyecto: e.proyecto || null
      }));

      // Obtiene los usuarios con rol evaluador
      const evaluadoresRes = await axios.get(route('concursos.evaluadores', concursoSeleccionadoParaEvaluaciones.value.id));
      evaluadoresEvaluacion.value = evaluadoresRes.data.evaluadores || [];
      
      mostrarCrearEvaluacionManual.value = true;
    } catch (e) {
      equiposEvaluacion.value = [];
      evaluadoresEvaluacion.value = [];
      mostrarCrearEvaluacionManual.value = false;
      alert('Error al cargar equipos o evaluadores. Verifica la relación en el backend.');
    }
  }
};

// Cierra el formulario de creación manual de evaluación
const handleCerrarCrearEvaluacionManual = () => {
  mostrarCrearEvaluacionManual.value = false;
};

// Recarga la lista de evaluaciones del concurso seleccionado
const recargarEvaluaciones = async () => {
  if (concursoSeleccionadoParaEvaluaciones.value) {
    await handleConfiguracion(concursoSeleccionadoParaEvaluaciones.value);
  }
};

// Elimina una evaluación después de confirmación
const eliminarEvaluacion = async (evaluacion) => {
  if (!evaluacion || !evaluacion.id) return;
  if (!confirm('¿Estás seguro de eliminar esta evaluación?')) return;
  try {
    await axios.delete(route('evaluaciones.destroy', evaluacion.id));
    await recargarEvaluaciones();
  } catch (e) {
    alert('No se pudo eliminar la evaluación.');
  }
};

// Cambia el estado del concurso (cerrado o abierto)
const handleCambioEstadoConcurso = ({ concurso, nuevoEstado }) => {
  if (!concurso || !concurso.id) return;
  if (nuevoEstado === 'cerrado') {
    // Muestra el modal de confirmación para cerrar el concurso
    concursoParaCerrar.value = concurso;
    mostrarModalCerrar.value = true;
    modalCerrarMensaje.value = '¿Está seguro que desea cambiar el estado del concurso a cerrado?';
  } else if (nuevoEstado === 'abierto') {
    // Llama a la ruta para abrir el concurso
    router.post(route('concursos.abrir', concurso.id), {}, {
      onSuccess: () => {
        mostrarModalEvaluaciones.value = false;
        router.reload();
      },
      onError: (error) => {
        alert('No se pudo abrir el concurso.');
        console.error(error);
      }
    });
  }
};

// Variables reactivas para el modal de vistaConcursosAdmin y equipos
const mostrarModalVistaConcursosAdmin = ref(false);
const equiposConcursoAdmin = ref([]);
const concursoSeleccionadoAdmin = ref(null);

// Función para cargar equipos de un concurso (para admin)
const cargarEquiposConcurso = async (concursoId) => {
  try {
    const res = await axios.get(route('concursos.equipos', concursoId));
    equiposConcursoAdmin.value = res.data.equipos || [];
  } catch (e) {
    equiposConcursoAdmin.value = [];
    alert('No se pudieron cargar los equipos del concurso.');
  }
};

// Inicializa el concurso en pantalla al cargar el componente
obtenerConcursoEnPantalla();

// Computed para verificar si el usuario puede inscribirse en el concurso
const puedeInscribirse = (concurso) => {
  const user = props.auth.user;
  if (concurso.fase === 'local') {
    return concurso.plantel && concurso.plantel.estado_id === user.estado_id;
  }
  if (concurso.fase === 'estatal') {
    // Solo si está clasificado en local y el estado coincide
    const clasificadoLocal = clasificaciones.value.find(c => c.fase === 'local');
    return clasificadoLocal && concurso.estado === user.estado_id;
  }
  if (concurso.fase === 'nacional') {
    // Solo si está clasificado en estatal
    return clasificaciones.value.some(c => c.fase === 'estatal');
  }
  return false;
};
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
        :class="{ 'opacity-50 pointer-events-none': mostrarModalEvaluaciones || mostrarModalInscripcion || mostrarModalCerrar }"
      >
        <!-- Overlay for modal (sin admin modal) -->
        <div 
          v-if="mostrarModalEvaluaciones || mostrarModalInscripcion || mostrarModalCerrar" 
          class="fixed inset-0 bg-gray-800 bg-opacity-50 z-40"
        ></div>

        <!-- Modal admin SOLO dentro del main -->
        <vistaConcursosAdmin
          v-if="mostrarModalVistaConcursosAdmin"
          :concurso="concursoSeleccionadoAdmin"
          :equipos="equiposConcursoAdmin"
          @close="mostrarModalVistaConcursosAdmin = false"
        />

        <!-- Contenido principal y formularios dentro del main -->
        <div v-if="!mostrarModalEvaluaciones && !mostrarModalInscripcion && !mostrarModalCerrar && !mostrarModalVistaConcursosAdmin">
          <h2 class="text-2xl font-bold mb-6 text-[#611232]">
            {{ selectedMenu }}
          </h2>


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
              @podio="handlePodio(concurso)" 
              class="transition-transform transform hover:scale-105 hover:shadow-lg"
            />
          </div>
        </div>
      </main>
    </div>
    <!-- Formularios fuera del main y del overlay, igual que los modales -->
    <!-- ...existing code for modals... -->
    <ModalConfirmacion
      v-if="mostrarModalCerrar"
      @confirmar="confirmarCerrarConcurso"
      @cancelar="mostrarModalCerrar = false"
      :mensaje="modalCerrarMensaje"
    />
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
      @crear-evaluacion-manual="handleAbrirCrearEvaluacionManual"
      @eliminar-evaluacion="eliminarEvaluacion"
      @cerrar-concurso="handleCerrarConcursoDesdeEvaluaciones"
      @cambiar-estado-concurso="handleCambioEstadoConcurso"
    />
    <CrearEvaluacionManual
      v-if="mostrarCrearEvaluacionManual"
      :concurso-id="concursoSeleccionadoParaEvaluaciones?.id"
      :equipos="equiposEvaluacion"
      :evaluadores="evaluadoresEvaluacion"
      @close="handleCerrarCrearEvaluacionManual"
      @created="recargarEvaluaciones"
    />
  </AuthenticatedLayout>
</template>