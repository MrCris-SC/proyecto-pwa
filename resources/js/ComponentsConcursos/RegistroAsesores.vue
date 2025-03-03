<template>
  <div>
    <h2 class="text-2xl font-semibold text-[#611232] mb-6">Registro de Asesores</h2>
    <form @submit.prevent="registrarAsesores">
      <!-- Asesor Técnico -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold text-[#611232] mb-3">Asesor Técnico</h3>
        <div class="space-y-3">
          <input v-model="asesorTecnico.nombre" type="text" placeholder="Nombre completo" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]" required />
          <div class="grid grid-cols-2 gap-4">
            <select v-model="asesorTecnico.tipo" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]" required>
              <option value="" disabled>Selecciona el tipo de asesor</option>
              <option value="Docente DGETI">Docente DGETI</option>
              <option value="Asesor externo">Asesor externo</option>
            </select>
            <input v-if="asesorTecnico.tipo === 'Docente DGETI'" v-model="asesorTecnico.clavePresupuestal" type="text" placeholder="Clave presupuestal" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]" required />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <input v-model="asesorTecnico.nivelAcademico" type="text" placeholder="Nivel académico" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]" required />
            <input v-model="asesorTecnico.correo" type="email" placeholder="Correo electrónico" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]" required />
          </div>
          <input v-model="asesorTecnico.telefono" type="tel" placeholder="Teléfono (10 dígitos)" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]" required />
        </div>
      </div>

      <!-- Asesor Metodológico -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold text-[#611232] mb-3">Asesor Metodológico</h3>
        <div class="space-y-3">
          <input v-model="asesorMetodologico.nombre" type="text" placeholder="Nombre completo" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]" required />
          <div class="grid grid-cols-2 gap-4">
            <input v-model="asesorMetodologico.nivelAcademico" type="text" placeholder="Nivel académico" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]" required />
            <input v-model="asesorMetodologico.correo" type="email" placeholder="Correo electrónico" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]" required />
          </div>
          <input v-model="asesorMetodologico.telefono" type="tel" placeholder="Teléfono (10 dígitos)" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232]" required />
        </div>
      </div>

      <!-- Perfil de Jurado (Máximo 3) -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold text-[#611232] mb-3">Perfil de Jurado (Máximo 3)</h3>
        <input v-model="busquedaPerfil" type="text" placeholder="Buscar perfil..." class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232] mb-4" />
        <div class="space-y-2">
          <div v-for="(categoria, index) in categoriasPerfiles" :key="index" class="mb-4">
            <h4 class="font-semibold text-[#611232] mb-2">{{ categoria.nombre }}</h4>
            <div class="grid grid-cols-2 gap-4">
              <div v-for="perfil in categoria.perfiles" :key="perfil" class="flex items-center">
                <input
                  type="checkbox"
                  :id="perfil"
                  :value="perfil"
                  v-model="perfilJurado"
                  :disabled="perfilJurado.length >= 3 && !perfilJurado.includes(perfil)"
                  class="mr-2"
                />
                <label :for="perfil">{{ perfil }}</label>
              </div>
            </div>
          </div>
        </div>
        <p v-if="perfilJurado.length > 3" class="text-red-500 text-sm mt-2">Solo puedes seleccionar hasta 3 perfiles.</p>
        <div v-if="perfilJurado.length > 0" class="mt-4">
          <h4 class="font-semibold text-[#611232] mb-2">Perfiles seleccionados:</h4>
          <ul>
            <li v-for="(perfil, index) in perfilJurado" :key="index" class="text-sm">{{ perfil }}</li>
          </ul>
        </div>
      </div>

      <!-- Botones de Acción -->
      <div class="flex justify-end space-x-4">
        <button type="button" @click="$emit('cerrar')" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
          Cancelar
        </button>
        <button type="submit" class="bg-[#611232] text-white px-6 py-2 rounded-lg hover:bg-[#8A1C4A] transition duration-200">
          Registrar
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

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
const busquedaPerfil = ref('');

const perfilesDisponibles = [
  // Ingenierías
  "Ingeniera/o ambiental",
  "Ingeniera/o civil",
  "Ingeniera/o en agrícola",
  "Ingeniera/o en alimentos",
  "Ingeniera/o en análisis de sistemas",
  "Ingeniera/o en bioingeniería",
  "Ingeniera/o en bioquímico",
  "Ingeniera/o en ciencias de la computación",
  "Ingeniera/o en desarrollo de software",
  "Ingeniera/o en energías renovables",
  "Ingeniera/o en electrónica",
  "Ingeniera/o en logística",
  "Ingeniera/o en mecánico",
  "Ingeniera/o en mecatrónica",
  "Ingeniera/o en química",
  "Ingeniera/o en sistemas computacionales",
  "Ingeniera/o en tics",
  "Ingeniera/o industrial",
  "Ingeniería forestal",
  "Ingeniería y gestión de proyectos",

  // Ciencias de la Salud
  "Enfermera/o",
  "Médica/o",
  "Psicóloga/o",

  // Ciencias Sociales
  "Sociólogo",
  "Trabajador/a social",

  // Educación
  "Educación",
  "Educación especial e inclusión",
  "Profesor/a",

  // Otros
  "Administrador de empresas",
  "Administrador de empresas turísticas",
  "Administrativa/o",
  "Administrativa/o comercial",
  "Administrativa/o de gestión y personal",
  "Agrónoma/o",
  "Análisis de sistemas",
  "Analista de mercados",
  "Antropóloga/o",
  "Arquitecta/o",
  "Biología marina",
  "Biólogo",
  "Biotecnología",
  "Ciencias de la computación",
  "Ciencias de la educación",
  "Comunicación y medios de información",
  "Consultor/a empresarial",
  "Contable",
  "Contaduría",
  "Desarrollo de software",
  "Desarrollo sustentable",
  "Dietista",
  "Diseñador/a gráfico",
  "Economista",
  "Educador/a infantil",
  "Educador/a primaria",
  "Electricista",
  "Eléctrico",
  "Emprendedor",
  "Emprendedor de negocios",
  "Enseñanza interactiva",
  "Especialista en el área de aplicación",
  "Farmacéutica",
  "Fisioterapeuta",
  "Gastronomía",
  "Gerente de empresas",
  "Gestión empresarial",
  "Informática/o",
  "Ingeniero químico en alimentos",
  "Innovación educativa",
  "Innovación tecnológica",
  "Innovación y desarrollo empresarial",
  "Licenciatura en informática",
  "Literatura",
  "Matemática/o",
  "Medicina general",
  "Mercadólogo",
  "Nutricionista",
  "Pedagoga/o",
  "Preparador/a físico/a deportivo/a",
  "Profesor de universidad",
  "Psicopedagogo",
  "Psiquiatra",
  "Publicitaria/o",
  "Recursos humanos",
  "Sistemas computacionales",
  "Traductor/a",
  "Turismo",
  "Veterinaria/o",
  "Otro"
];

const categoriasPerfiles = computed(() => {
  const categorias = [
    { nombre: 'Ingenierías', perfiles: [] },
    { nombre: 'Ciencias de la Salud', perfiles: [] },
    { nombre: 'Ciencias Sociales', perfiles: [] },
    { nombre: 'Educación', perfiles: [] },
    { nombre: 'Otros', perfiles: [] },
  ];

  perfilesDisponibles.forEach(perfil => {
    if (perfil.toLowerCase().includes(busquedaPerfil.value.toLowerCase())) {
      if (perfil.includes('Ingeniera/o') || perfil.includes('Ingeniería')) {
        categorias[0].perfiles.push(perfil);
      } else if (perfil.includes('Médica/o') || perfil.includes('Enfermera/o') || perfil.includes('Psicóloga/o')) {
        categorias[1].perfiles.push(perfil);
      } else if (perfil.includes('Sociólogo') || perfil.includes('Trabajador/a social')) {
        categorias[2].perfiles.push(perfil);
      } else if (perfil.includes('Educación') || perfil.includes('Profesor/a')) {
        categorias[3].perfiles.push(perfil);
      } else {
        categorias[4].perfiles.push(perfil);
      }
    }
  });

  return categorias.filter(categoria => categoria.perfiles.length > 0);
});

// Función para registrar asesores
const registrarAsesores = async () => {
  if (perfilJurado.value.length > 3) {
    alert('Solo puedes seleccionar hasta 3 perfiles de jurado.');
    return;
  }

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