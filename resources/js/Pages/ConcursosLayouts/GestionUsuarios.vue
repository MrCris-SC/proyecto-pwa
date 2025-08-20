<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Gestión de Usuarios
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
      
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <!-- Encabezado y botón -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
          <h2 class="text-2xl font-bold text-[#611232]">
            Gestión de Usuarios
          </h2>
          <button 
            @click="navigateToNewUser"
            class="px-4 py-2 bg-[#611232] text-white rounded-md hover:bg-[#4a0d24] transition-colors flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
            </svg>
            Nuevo Usuario
          </button>
        </div>

        <!-- Filtro por concurso -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700">Seleccionar concurso</label>
          <select 
            v-model="filters.concurso_id"
            @change="applyFilters"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="" disabled selected>-- Seleccione un concurso --</option>
            <option 
              v-for="concurso in concursos" 
              :key="concurso.id" 
              :value="concurso.id"
            >
              {{ concurso.nombre }}
            </option>
          </select>
        </div>

        <!-- Contenido principal -->
        <div v-if="filters.concurso_id">
          <!-- Mensaje de estado -->
          <div v-if="$page.props.status" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ $page.props.status }}
          </div>

          <!-- Tarjetas de resumen por rol -->
          <div class="mb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div 
              v-for="rol in rolesDisponibles" 
              :key="rol.value"
              class="p-4 border rounded-lg hover:shadow-md transition-shadow cursor-pointer"
              :class="{ 'ring-2 ring-[#611232]': filtroRol === rol.value }"
              @click="filtrarPorRol(rol.value)"
            >
              <h3 class="font-semibold text-[#611232]">{{ rol.label }}</h3>
              <p class="text-2xl font-bold mt-2">
                {{ usuariosPorRol(rol.value).length }}
              </p>
            </div>
          </div>

          <!-- Barra de búsqueda -->
          <div class="mb-4">
            <label for="busqueda" class="sr-only">Buscar</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
              </div>
              <input
                id="busqueda"
                v-model="filtroBusqueda"
                type="text"
                placeholder="Buscar por nombre o email..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#611232] focus:border-[#611232] sm:text-sm"
              />
            </div>
          </div>

          <!-- Tabla de usuarios -->
          <div class="overflow-x-auto border rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nombre
                  </th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Email
                  </th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Rol
                  </th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Acciones
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr 
                  v-for="usuario in usuariosFiltrados" 
                  :key="usuario.id" 
                  class="hover:bg-gray-50 transition-colors"
                >
                  <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ usuario.name }}</div>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ usuario.email }}</div>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span 
                      class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                      :class="claseColorRol(usuario.rol)"
                    >
                      {{ formatoRol(usuario.rol) }}
                    </span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap space-x-2">
                    <button 
                      v-if="usuario.rol !== 'asesor'"
                      @click.stop="editarUsuario(usuario)"
                      class="text-[#611232] hover:text-[#4a0d24] p-1 rounded hover:bg-gray-100 transition-colors"
                      title="Editar"
                      :disabled="procesando"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <button 
                      @click.stop="confirmarEliminar(usuario)"
                      class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-gray-100 transition-colors"
                      title="Eliminar"
                      :disabled="procesando"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </td>
                </tr>
                <tr v-if="usuariosFiltrados.length === 0">
                  <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">
                    No se encontraron usuarios en este concurso
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Mensaje cuando no hay concurso seleccionado -->
        <div v-else class="text-center py-12">
          <p class="text-gray-500">Seleccione un concurso para ver los usuarios registrados</p>
        </div>

        <!-- Modal para crear/editar usuario -->
        <Modal :show="showModal" @close="cerrarModal">
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-4 text-[#611232]">
              {{ usuarioEditando ? 'Editar Usuario' : 'Nuevo Usuario' }}
            </h3>
            
            <form @submit.prevent="guardarUsuario">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  required
                  class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#611232] focus:border-[#611232]"
                  :disabled="form.processing"
                >
              </div>
              
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input 
                  v-model="form.email" 
                  type="email" 
                  required
                  class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#611232] focus:border-[#611232]"
                  :disabled="form.processing"
                >
              </div>
              
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                <select 
                  v-model="form.rol" 
                  required
                  class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#611232] focus:border-[#611232]"
                  :disabled="form.processing"
                >
                  <option v-for="rol in rolesDisponibles.filter(r => r.value !== 'asesor')" :key="rol.value" :value="rol.value">
                    {{ rol.label }}
                  </option>
                </select>
              </div>
              
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  {{ usuarioEditando ? 'Nueva contraseña (dejar vacío para no cambiar)' : 'Contraseña' }}
                </label>
                <input 
                  v-model="form.password" 
                  type="password"
                  :required="!usuarioEditando"
                  class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#611232] focus:border-[#611232]"
                  :disabled="form.processing"
                >
              </div>
              
              <div class="flex justify-end space-x-3 mt-6">
                <button 
                  type="button" 
                  @click="cerrarModal"
                  class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                  :disabled="form.processing"
                >
                  Cancelar
                </button>
                <button 
                  type="submit"
                  class="px-4 py-2 bg-[#611232] text-white rounded-md hover:bg-[#4a0d24] transition-colors"
                  :disabled="form.processing"
                >
                  <span v-if="form.processing">Procesando...</span>
                  <span v-else>{{ usuarioEditando ? 'Actualizar' : 'Guardar' }}</span>
                </button>
              </div>
            </form>
          </div>
        </Modal>

        <!-- Modal de confirmación para eliminar -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-4 text-[#611232]">Confirmar eliminación</h3>
            <p>¿Estás seguro de eliminar {{ usuarioAEliminar?.rol === 'asesor' ? 'al asesor' : 'al usuario' }} <strong>{{ usuarioAEliminar?.name }}</strong>?</p>
            <p class="text-sm text-gray-500 mt-2">Esta acción no puede deshacerse.</p>
            
            <div class="flex justify-end space-x-3 mt-6">
              <button 
                @click="showDeleteModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                :disabled="procesando"
              >
                Cancelar
              </button>
              <button 
                @click="eliminarUsuario"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                :disabled="procesando"
              >
                <span v-if="procesando">Eliminando...</span>
                <span v-else>Eliminar</span>
              </button>
            </div>
          </div>
        </Modal>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  users: {
    type: Array,
    default: () => []
  },
  concursos: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  userRole: {
    type: String,
    default: 'admin'
  }
});

// Filtros
const filters = ref({
  concurso_id: props.filters.concurso_id || ''
});

// Roles disponibles con colores
const rolesDisponibles = [
  { value: 'admin', label: 'Administrador', color: 'bg-purple-100 text-purple-800' },
  { value: 'evaluador', label: 'Evaluador', color: 'bg-blue-100 text-blue-800' },
  { value: 'lider', label: 'Líder de equipo', color: 'bg-green-100 text-green-800' },
  { value: 'asesor', label: 'Asesor', color: 'bg-yellow-100 text-yellow-800' }
];

// Filtros adicionales
const filtroRol = ref('');
const filtroBusqueda = ref('');

// Modales
const showModal = ref(false);
const showDeleteModal = ref(false);
const usuarioEditando = ref(null);
const usuarioAEliminar = ref(null);
const procesando = ref(false);

// Formulario
const form = useForm({
  id: null,
  name: '',
  email: '',
  rol: 'evaluador',
  password: ''
});

// Aplicar filtros
const applyFilters = () => {
  router.get(route('gestion.usuarios.index'), {
    concurso_id: filters.value.concurso_id
  }, {
    preserveState: true,
    replace: true,
  });
};

// Funciones auxiliares
const formatoRol = (rol) => {
  const rolObj = rolesDisponibles.find(r => r.value === rol);
  return rolObj ? rolObj.label : rol;
};

const claseColorRol = (rol) => {
  const rolObj = rolesDisponibles.find(r => r.value === rol);
  return rolObj ? rolObj.color : 'bg-gray-100 text-gray-800';
};

const usuariosPorRol = (rol) => {
  return props.users.filter(u => u.rol === rol);
};

const filtrarPorRol = (rol) => {
  filtroRol.value = filtroRol.value === rol ? '' : rol;
};

// Filtrado combinado
const usuariosFiltrados = computed(() => {
  let usuarios = props.users;
  
  // Filtro por rol
  if (filtroRol.value) {
    usuarios = usuarios.filter(u => u.rol === filtroRol.value);
  }
  
  // Filtro por búsqueda
  if (filtroBusqueda.value) {
    const termino = filtroBusqueda.value.toLowerCase();
    usuarios = usuarios.filter(u => 
      u.name.toLowerCase().includes(termino) || 
      u.email.toLowerCase().includes(termino))
  }
  
  return usuarios;
});

// Métodos CRUD
const navigateToNewUser = () => {
  router.get(route('new.user'));
};

const abrirModalCrear = () => {
  form.reset();
  form.rol = 'evaluador';
  usuarioEditando.value = null;
  showModal.value = true;
};

const editarUsuario = (usuario) => {
  if (usuario.rol === 'asesor') return;
  
  form.id = usuario.id;
  form.name = usuario.name;
  form.email = usuario.email;
  form.rol = usuario.rol;
  form.password = '';
  usuarioEditando.value = usuario;
  showModal.value = true;
};

const cerrarModal = () => {
  showModal.value = false;
  // Resetear después de la animación
  setTimeout(() => {
    form.reset();
    usuarioEditando.value = null;
  }, 300);
};

const guardarUsuario = () => {
  if (usuarioEditando.value) {
    form.put(route('gestion.usuarios.update', form.id), {
      onSuccess: () => {
        cerrarModal();
      },
      onFinish: () => {
        form.processing = false;
      }
    });
  } else {
    form.post(route('gestion.usuarios.store')), {
      onSuccess: () => {
        cerrarModal();
      },
      onFinish: () => {
        form.processing = false;
      }
    };
  }
};

const confirmarEliminar = (usuario) => {
  usuarioAEliminar.value = usuario;
  showDeleteModal.value = true;
};

const eliminarUsuario = () => {
  procesando.value = true;
  
  if (usuarioAEliminar.value.rol === 'asesor') {
    router.delete(route('asesores.destroy', usuarioAEliminar.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false;
        procesando.value = false;
      },
      onError: () => {
        procesando.value = false;
      }
    });
  } else {
    router.delete(route('gestion.usuarios.destroy', usuarioAEliminar.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false;
        procesando.value = false;
      },
      onError: () => {
        procesando.value = false;
      }
    });
  }
};

const handleMenuSelected = (menu) => {
  const routes = {
    'evaluación': 'evaluacion.index',
    'proyectos asignados': 'proyectos.asignados',
    'criterios': 'criterios.index',
    'reportes': 'reportes.index',
    'gestión de usuarios': 'gestion.usuarios.index',
    'perfil': 'perfil.index'
  };
  
  if (routes[menu]) {
    router.visit(route(routes[menu]));
  }
};
</script>