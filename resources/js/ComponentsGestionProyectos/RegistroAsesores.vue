<template>
    <div>
      <h2 class="text-2xl font-semibold text-[#611232] mb-6">Registro de Asesores</h2>
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
            <div class="grid grid-cols-2 gap-4">
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
            <div class="grid grid-cols-2 gap-4">
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
            <div class="grid grid-cols-2 gap-4">
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
  
        <!-- Perfil de Jurado (Opcional) -->
        <div class="mb-6">
          <h3 class="text-lg font-semibold text-[#611232] mb-3">Perfil de Jurado</h3>
          <select
            v-model="perfilJurado"
            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]"
            multiple
          >
            <option value="Ingeniero en sistemas">Ingeniero en sistemas</option>
            <option value="Psicólogo">Psicólogo</option>
            <option value="Administrador de empresas">Administrador de empresas</option>
            <!-- Agrega más opciones según sea necesario -->
          </select>
        </div>
  
        <!-- Botones de Acción -->
        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="$emit('cerrar')"
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
    nivelAcademico: '',
    correo: '',
    telefono: '',
  });
  
  const perfilJurado = ref([]);
  
  // Función para registrar asesores
  const registrarAsesores = async () => {
    try {
      const response = await axios.post('/api/registrar-asesores', {
        asesorTecnico: asesorTecnico.value,
        asesorMetodologico: asesorMetodologico.value,
        perfilJurado: perfilJurado.value,
      });
      console.log('Asesores registrados:', response.data);
      alert('Asesores registrados exitosamente.');
    } catch (error) {
      console.error('Error al registrar asesores:', error);
      alert('Hubo un error al registrar los asesores.');
    }
  };
  </script>