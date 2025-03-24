<template>
  <div class="mb-8 relative">
    <!-- Botón de cierre (X) -->
    <button
      class="absolute top-0 right-0 bg-gray-500 text-white px-2 py-1 rounded-lg hover:bg-gray-600 transition duration-200"
      @click="cerrar"
    >
      X
    </button>

    <!-- Información adicional para el usuario -->
    <div class="bg-blue-100 p-4 rounded-lg mb-6">
      <p class="text-blue-800">
        Por favor, complete el siguiente formulario para registrar a los asesores técnicos y metodológicos de su proyecto. 
        Asegúrese de que toda la información proporcionada sea correcta y esté actualizada.
      </p>
    </div>

    <!-- Título del formulario -->
    <h2 class="text-2xl font-semibold text-[#611232] mb-6">Registro de Asesores</h2>

    <!-- Formulario de registro de asesores -->
    <form @submit.prevent="registrarAsesores">
      <!-- Asesor Técnico -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold text-[#611232] mb-3">Asesor Técnico</h3>
        <div class="space-y-3">
          <input
            v-model="asesorTecnico.nombre"
            type="text"
            placeholder="Nombre completo"
            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
            required
          />
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <select
              v-model="asesorTecnico.tipo"
              class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
              required
            >
              <option value="" disabled>Selecciona el tipo de asesor</option>
              <option value="Docente DGETI">Docente DGETI</option>
              <option value="Asesor externo">Asesor externo</option>
            </select>
            <input
              v-if="asesorTecnico.tipo === 'Docente DGETI'"
              v-model="asesorTecnico.clavePresupuestal"
              type="text"
              placeholder="Clave presupuestal"
              class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
              required
            />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input
              v-model="asesorTecnico.nivelAcademico"
              type="text"
              placeholder="Nivel académico"
              class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
              required
            />
            <input
              v-model="asesorTecnico.correo"
              type="email"
              placeholder="Correo electrónico"
              class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
              required
            />
          </div>
          <input
            v-model="asesorTecnico.telefono"
            type="tel"
            placeholder="Teléfono (10 dígitos)"
            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
            required
          />
        </div>
      </div>

      <!-- Asesor Metodológico -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold text-[#611232] mb-3">Asesor Metodológico</h3>
        <div class="space-y-3">
          <input
            v-model="asesorMetodologico.nombre"
            type="text"
            placeholder="Nombre completo"
            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
            required
          />
          <!-- Nuevo campo   -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <select
              v-model="asesorMetodologico.tipo"
              class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
              required
            >
              <option value="" disabled>Selecciona el tipo de asesor</option>
              <option value="Docente DGETI">Docente DGETI</option>
              <option value="Asesor externo">Asesor externo</option>
            </select>
            <input
              v-if="asesorMetodologico.tipo === 'Docente DGETI'"
              v-model="asesorMetodologico.clavePresupuestal"
              type="text"
              placeholder="Clave presupuestal"
              class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
              required
            />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input
              v-model="asesorMetodologico.nivelAcademico"
              type="text"
              placeholder="Nivel académico"
              class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
              required
            />
            <input
              v-model="asesorMetodologico.correo"
              type="email"
              placeholder="Correo electrónico"
              class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
              required
            />
          </div>
          <input
            v-model="asesorMetodologico.telefono"
            type="tel"
            placeholder="Teléfono (10 dígitos)"
            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
            required
          />
        </div>
      </div>

      <!-- Botones de Acción -->
      <div class="flex flex-col md:flex-row justify-end space-y-4 md:space-y-0 md:space-x-4">
        <button
          type="button"
          @click="cerrar"
          class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-200"
        >
          Cancelar
        </button>
        <button
          type="submit"
          class="bg-[#611232] text-white px-6 py-2 rounded-lg hover:bg-[#8A1C4A] transition duration-200"
        >
          Registrar
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

// Datos del formulario de asesores
const asesorTecnico = ref({
  nombre: '',
  tipo: '',
  clavePresupuestal: '',
  nivelAcademico: '',
  correo: '',
  telefono: '',
});

const asesorMetodologico = ref({
  nombre: '',
  tipo: '',                            // Nuevo campo
  clavePresupuestal: '',               // Nuevo campo
  nivelAcademico: '',
  correo: '',
  telefono: '',
});

// Función para cerrar el modal
const emit = defineEmits(['close']);

const cerrar = () => {
  emit('close');
};

const registrarAsesores = async () => {
  const form = useForm({
    asesorTecnico: asesorTecnico.value,
    asesorMetodologico: asesorMetodologico.value,
  });

  form.post('/registrar-asesor', {
    onSuccess: () => {
      alert('Asesores registrados exitosamente.');
      cerrar();
    },
    onError: (errors) => {
      console.error('Error al registrar asesores:', errors);
      alert('Hubo un error al registrar los asesores.');
    },
  });
};

// Definir las props
const props = defineProps({
  equipoId: {
    type: Number,
    required: true,
  },
});
</script>