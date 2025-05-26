<template>
  <div class="fixed inset-0 z-30 flex items-center justify-center bg-gray-800 bg-opacity-75">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
      <!-- Botón cerrar arriba a la derecha -->
      <button
        @click="$emit('close')"
        class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl font-bold"
        aria-label="Cerrar"
      >&times;</button>
      <h2 class="text-xl font-bold text-[#611232] mb-4">Crear Evaluación Manual</h2>
      <form @submit.prevent="crearEvaluacion">
        <div class="mb-4">
          <label class="block text-gray-700 mb-1">Equipo / Proyecto</label>
          <select v-model="equipo_id" class="w-full border rounded px-3 py-2">
            <option value="">Seleccione un equipo</option>
            <option v-for="equipo in equipos" :key="equipo.id" :value="equipo.id">
              {{ equipo.nombre ? equipo.nombre : 'Equipo ' + equipo.id }} 
              - 
              {{ equipo.proyecto && equipo.proyecto.nombre ? equipo.proyecto.nombre : 'Sin proyecto' }}
            </option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-1">Evaluador</label>
          <select v-model="evaluador_id" class="w-full border rounded px-3 py-2">
            <option value="">Seleccione un evaluador</option>
            <option v-for="evaluador in evaluadores" :key="evaluador.id" :value="evaluador.id">
              {{ evaluador.name }}
            </option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-1">Estado</label>
          <select v-model="estado" class="w-full border rounded px-3 py-2">
            <option value="pendiente">Pendiente</option>
            <option value="completada">Completada</option>
          </select>
        </div>
        <div class="flex justify-end">
          <button type="button" @click="$emit('close')" class="bg-gray-400 text-white px-4 py-2 rounded mr-2 hover:bg-gray-600">
            Cancelar
          </button>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-800">
            Crear Evaluación
          </button>
        </div>
        <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  concursoId: {
    type: [Number, String],
    required: true,
  }
});

const emit = defineEmits(['close', 'created']);

const equipos = ref([]);
const evaluadores = ref([]);
const equipo_id = ref('');
const evaluador_id = ref('');
const estado = ref('pendiente');
const error = ref('');

onMounted(async () => {
  try {
    // Obtener equipos con su proyecto relacionado
    const equiposRes = await axios.get(route('concursos.equipos', props.concursoId));
    equipos.value = (equiposRes.data.equipos || []).map(e => ({
      ...e,
      proyecto: e.proyecto // Asegúrate que el backend incluya la relación proyecto
    }));

    // Obtener usuarios con rol evaluador
    const evaluadoresRes = await axios.get(route('concursos.evaluadores', props.concursoId));
    evaluadores.value = evaluadoresRes.data.evaluadores || [];
  } catch (e) {
    error.value = 'Error al cargar datos.';
  }
});

const crearEvaluacion = async () => {
  error.value = '';
  if (!equipo_id.value || !evaluador_id.value || !estado.value) {
    error.value = 'Todos los campos son obligatorios.';
    return;
  }
  try {
    await axios.post(route('evaluaciones.store'), {
      concurso_id: props.concursoId,
      equipo_id: equipo_id.value,
      evaluador_id: evaluador_id.value,
      estado: estado.value,
    });
    emit('created');
    emit('close');
  } catch (e) {
    error.value = 'No se pudo crear la evaluación.';
  }
};
</script>
