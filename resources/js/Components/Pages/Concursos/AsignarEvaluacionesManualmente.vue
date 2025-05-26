<template>
  <div class="asignar-evaluaciones-manual">
    <h3>Asignación Manual de Evaluaciones</h3>
    <div v-if="equipos.length && evaluadores.length">
      <table>
        <thead>
          <tr>
            <th>Equipo</th>
            <th>Evaluador Asignado</th>
            <th>Asignar Evaluador</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="equipo in equipos" :key="equipo.id">
            <td>{{ equipo.nombre }}</td>
            <td>
              <span v-if="asignaciones[equipo.id]">
                {{ getEvaluadorNombre(asignaciones[equipo.id]) }}
              </span>
              <span v-else>
                No asignado
              </span>
            </td>
            <td>
              <select v-model="asignaciones[equipo.id]">
                <option value="">Seleccionar evaluador</option>
                <option v-for="ev in evaluadores" :key="ev.id" :value="ev.id">
                  {{ ev.name }}
                </option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
      <button @click="guardarAsignaciones">Guardar Asignaciones</button>
    </div>
    <div v-else>
      <p>No hay equipos o evaluadores disponibles.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  equipos: { type: Array, required: true },
  evaluadores: { type: Array, required: true },
  evaluaciones: { type: Array, required: false, default: () => [] }
});

const asignaciones = ref({});

watch(
  () => props.evaluaciones,
  (nuevas) => {
    nuevas.forEach(ev => {
      asignaciones.value[ev.equipo_id] = ev.evaluador_id;
    });
  },
  { immediate: true }
);

function getEvaluadorNombre(id) {
  const ev = props.evaluadores.find(e => e.id === id);
  return ev ? ev.name : 'No asignado';
}

function guardarAsignaciones() {
  alert('Asignaciones guardadas (simulado)');
}
</script>

<style scoped>
.asignar-evaluaciones-manual {
  margin-top: 2rem;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  border: 1px solid #ddd;
  padding: 0.5rem;
}
</style>
