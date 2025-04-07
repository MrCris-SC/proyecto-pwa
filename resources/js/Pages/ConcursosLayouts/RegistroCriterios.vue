<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-[#611232]">
        Registro de Criterios
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
      <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
      <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-[#611232]">
          Registro de Criterios de Evaluación
        </h2>

        <!-- Selector de concurso -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Concurso</label>
          <select 
            v-model="concursoId"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-[#611232] focus:border-[#611232]"
            @change="cargarCriterios"
          >
            <option value="" disabled selected>Seleccione un concurso</option>
            <option 
              v-for="concurso in concursos" 
              :key="concurso.id" 
              :value="concurso.id"
            >
              {{ concurso.nombre }} ({{ concurso.fase }})
            </option>
          </select>
        </div>

        <!-- Lista de criterios -->
        <div v-if="concursoId" class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-[#611232]">Criterios de Evaluación</h2>
            <button 
              @click="agregarCriterio"
              class="px-4 py-2 bg-[#8A1C4A] text-white rounded-lg hover:bg-[#9C2755] transition"
            >
              <i class="fas fa-plus mr-2"></i> Agregar Criterio
            </button>
          </div>

          <div v-if="criterios.length === 0" class="text-center py-4 text-gray-500">
            No hay criterios registrados para este concurso
          </div>

          <div v-else>
            <div 
              v-for="(criterio, index) in criterios" 
              :key="index"
              class="border-b border-gray-200 py-4"
            >
              <div class="flex items-start mb-3">
                <div class="flex-grow">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Criterio</label>
                  <input
                    v-model="criterio.nombre"
                    type="text"
                    class="w-full p-2 border border-gray-300 rounded focus:ring-[#611232] focus:border-[#611232]"
                    placeholder="Ej. Originalidad, Presentación, etc."
                  >
                </div>
                <div class="ml-4 w-32">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Puntaje Máximo</label>
                  <input
                    v-model="criterio.puntaje_maximo"
                    type="number"
                    min="1"
                    class="w-full p-2 border border-gray-300 rounded focus:ring-[#611232] focus:border-[#611232]"
                  >
                </div>
                <button
                  @click="eliminarCriterio(index)"
                  class="ml-4 p-2 text-red-500 hover:text-red-700"
                  title="Eliminar criterio"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>

            <div class="mt-4 flex justify-between items-center">
              <span class="text-sm font-medium">
                Total: {{ sumaPuntajes }} puntos
              </span>
              <button
                @click="guardarCriterios"
                :disabled="guardando"
                class="px-6 py-2 bg-[#611232] text-white rounded-lg hover:bg-[#8A1C4A] disabled:opacity-50 transition"
              >
                <span v-if="guardando">
                  <i class="fas fa-spinner fa-spin mr-2"></i> Guardando...
                </span>
                <span v-else>
                  <i class="fas fa-save mr-2"></i> Guardar Criterios
                </span>
              </button>
            </div>
          </div>
        </div>

        <!-- Mensajes de éxito/error -->
        <div v-if="mensaje" class="p-4 mb-4 rounded-lg" :class="{
          'bg-green-100 text-green-800': mensaje.tipo === 'exito',
          'bg-red-100 text-red-800': mensaje.tipo === 'error'
        }">
          {{ mensaje.texto }}
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  concursos: {
    type: Array,
    required: true
  },
  criteriosExistentes: {
    type: Array,
    default: () => []
  }
});

const concursoId = ref('');
const criterios = ref([]);
const guardando = ref(false);
const mensaje = ref(null);

// Computed para calcular la suma de puntajes
const sumaPuntajes = computed(() => {
  return criterios.value.reduce((sum, criterio) => {
    return sum + (parseFloat(criterio.puntaje_maximo) || 0);
  }, 0);
});

// Cargar criterios existentes cuando cambia el concurso
const cargarCriterios = () => {
  if (!concursoId.value) return;

  const criteriosDelConcurso = props.criteriosExistentes.filter(
    c => c.concurso_id == concursoId.value
  );

  criterios.value = criteriosDelConcurso.length > 0 
    ? [...criteriosDelConcurso]
    : [{ nombre: '', puntaje_maximo: 10 }];
};

// Agregar nuevo criterio
const agregarCriterio = () => {
  criterios.value.push({ nombre: '', puntaje_maximo: 10 });
};

// Eliminar criterio
const eliminarCriterio = (index) => {
  if (criterios.value.length > 1) {
    criterios.value.splice(index, 1);
  } else {
    mostrarMensaje('Debe haber al menos un criterio', 'error');
  }
};

// Guardar criterios
const guardarCriterios = async () => {
  if (!validarCriterios()) return;

  guardando.value = true;

  try {
    await router.post(route('criterios.store'), {
      concurso_id: concursoId.value,
      criterios: criterios.value
    }, {
      onSuccess: () => {
        mostrarMensaje('Criterios guardados exitosamente', 'exito');
      },
      onError: (errors) => {
        mostrarMensaje(Object.values(errors).join(' '), 'error');
      }
    });
  } catch (error) {
    mostrarMensaje('Error al guardar los criterios', 'error');
  } finally {
    guardando.value = false;
  }
};

// Validar antes de guardar
const validarCriterios = () => {
  const nombres = criterios.value.map(c => c.nombre.trim().toLowerCase());
  if (new Set(nombres).size !== nombres.length) {
    mostrarMensaje('Los nombres de los criterios deben ser únicos', 'error');
    return false;
  }

  if (criterios.value.some(c => !c.nombre.trim())) {
    mostrarMensaje('Todos los criterios deben tener un nombre', 'error');
    return false;
  }

  if (criterios.value.some(c => !c.puntaje_maximo || c.puntaje_maximo <= 0)) {
    mostrarMensaje('Todos los criterios deben tener un puntaje máximo válido', 'error');
    return false;
  }

  return true;
};

// Mostrar mensajes temporales
const mostrarMensaje = (texto, tipo) => {
  mensaje.value = { texto, tipo };
  setTimeout(() => { mensaje.value = null; }, 5000);
};

// Inicializar si solo hay un concurso
onMounted(() => {
  if (props.concursos.length === 1) {
    concursoId.value = props.concursos[0].id;
    cargarCriterios();
  }
});

const handleMenuSelected = (menu) => {
  if (menu === 'Concursos') {
    router.get(route('concursos.index'));
  }
};
</script>

<style scoped>
/* Estilos personalizados si son necesarios */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>

