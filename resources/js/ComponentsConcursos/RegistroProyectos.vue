<script>
import axios from 'axios';
import { router } from '@inertiajs/vue3';

export default {
  props: {
    concursoId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      paso: 1, // Controla el cambio de formularios
      form: {
        nombre: '',
        categoria: 'alumno',
        modalidad_id: '',
        linea_investigacion_id: '',
        concurso_id: this.concursoId, // Inicializar con el ID del concurso recibido
      },
      equipo: [{ nombre: '', correo: '', genero: '', telefono: '', direccion: '' }], // Lista de integrantes
      modalidades: [],
      lineas: [],
      concursos: [], // Lista de concursos
      proyecto_id: null,
      selectedTipoProyecto: 'prototipos', // Nueva propiedad para filtrar por tipo de proyecto
      errores: {}, // Objeto para almacenar errores de validación
      mensajeError: '', // Mensaje de error general
      mensajeExito: '', // Nuevo estado para mensajes de éxito
      busquedaPerfil: '', // Búsqueda de perfiles de jurado
      perfilJurado: [], // Perfiles de jurado seleccionados
      mostrarListaPerfiles: true, // Controla si se muestra la lista de perfiles
      perfilesDisponibles: [
        "Ingeniería ambiental",
        "Ingeniería civil",
        "Ingeniería agrícola",
        "Ingeniería en alimentos",
        "Ingeniería en análisis de sistemas",
        "Ingeniería en bioingeniería",
        "Ingeniería en bioquímica",
        "Ingeniería en ciencias de la computación",
        "Ingeniería en desarrollo de software",
        "Ingeniería en energías renovables",
        "Ingeniería en electrónica",
        "Ingeniería en logística",
        "Ingeniería mecánica",
        "Ingeniería en mecatrónica",
        "Ingeniería química",
        "Ingeniería en sistemas computacionales",
        "Ingeniería en tics",
        "Ingeniería industrial",
        "Ingeniería forestal",
        "Ingeniería y gestión de proyectos",
        "Enfermería",
        "Medicina",
        "Psicología",
        "Sociología",
        "Trabajo social",
        "Educación",
        "Educación especial e inclusión",
        "Profesorado",
        "Administración de empresas",
        "Administración de empresas turísticas",
        "Administración comercial",
        "Administración de gestión y personal",
        "Agronomía",
        "Análisis de sistemas",
        "Análisis de mercados",
        "Antropología",
        "Arquitectura",
        "Biología marina",
        "Biología",
        "Biotecnología",
        "Ciencias de la computación",
        "Ciencias de la educación",
        "Comunicación y medios de información",
        "Consultoría empresarial",
        "Contabilidad",
        "Contaduría",
        "Desarrollo de software",
        "Desarrollo sustentable",
        "Dietética",
        "Diseño gráfico",
        "Economía",
        "Educación infantil",
        "Educación primaria",
        "Electricidad",
        "Electrónica",
        "Emprendimiento",
        "Emprendimiento de negocios",
        "Enseñanza interactiva",
        "Especialista en el área de aplicación",
        "Farmacia",
        "Fisioterapia",
        "Gastronomía",
        "Gerencia de empresas",
        "Gestión empresarial",
        "Informática",
        "Ingeniería química en alimentos",
        "Innovación educativa",
        "Innovación tecnológica",
        "Innovación y desarrollo empresarial",
        "Licenciatura en informática",
        "Literatura",
        "Matemáticas",
        "Medicina general",
        "Mercadotecnia",
        "Nutrición",
        "Pedagogía",
        "Preparación física deportiva",
        "Profesorado universitario",
        "Psicopedagogía",
        "Psiquiatría",
        "Publicidad",
        "Recursos humanos",
        "Sistemas computacionales",
        "Traducción",
        "Turismo",
        "Medicina veterinaria",
        
      ],
      mostrarModalLider: false, // No mostrar el modal al cargar
    };
  },
  computed: {
    // Filtra las modalidades según el tipo de proyecto seleccionado
    modalidadesFiltradas() {
      return this.modalidades.filter(
        (modalidad) => modalidad.tipo.toLowerCase() === this.selectedTipoProyecto
      );
    },
    // Filtra los perfiles disponibles según la búsqueda
    categoriasPerfiles() {
      const categorias = [
        { nombre: 'Ingenierías', perfiles: [] },
        { nombre: 'Ciencias de la Salud', perfiles: [] },
        { nombre: 'Ciencias Sociales', perfiles: [] },
        { nombre: 'Educación', perfiles: [] },
        { nombre: 'Otros', perfiles: [] },
      ];

      this.perfilesDisponibles.forEach(perfil => {
        if (perfil.toLowerCase().includes(this.busquedaPerfil.toLowerCase())) {
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
    },
  },
  methods: {
    // Método para manejar errores
    manejarError(mensaje, index = null) {
      if (index !== null) {
        this.errores[index] = mensaje;
      }
      this.mensajeError = mensaje;
    },

    // Método para validar un integrante
    validarIntegrante(integrante) {
      if (!integrante.nombre || !integrante.correo || !integrante.genero || !integrante.telefono || !integrante.direccion) {
        this.manejarError('Por favor, completa todos los campos del integrante.');
        return false;
      }

      if (!this.validarCorreo(integrante.correo)) {
        this.manejarError('Por favor, ingresa un correo electrónico válido.');
        return false;
      }

      if (!this.validarTelefono(integrante.telefono)) {
        this.manejarError('El teléfono debe tener exactamente 10 dígitos.');
        return false;
      }

      return true;
    },

    // Método para validar el formulario del proyecto
    validarFormularioProyecto() {
      if (!this.form.nombre || !this.form.modalidad_id || !this.form.linea_investigacion_id) {
        this.manejarError('Por favor, completa todos los campos del proyecto.');
        return false;
      }
      return true;
    },

    // Método para validar el formulario completo
    validarFormularioCompleto() {
      this.errores = {}; // Reiniciar errores

      // Validar campos del proyecto
      if (!this.validarFormularioProyecto()) {
        return false;
      }

      // Validar campos de los integrantes
      for (let i = 0; i < this.equipo.length; i++) {
        if (!this.validarIntegrante(this.equipo[i])) {
          return false;
        }
      }

      return true;
    },

    // Método para avanzar al siguiente paso
    avanzarPaso() {
      if (this.validarFormularioProyecto()) {
        this.paso = 2; // Avanzar al formulario de integrantes
        this.mostrarModalLider = true; // Mostrar modal al avanzar de paso
      }
    },

    // Método para agregar un nuevo integrante
    agregarIntegrante() {
      const ultimoIntegrante = this.equipo[this.equipo.length - 1];

      if (!this.validarIntegrante(ultimoIntegrante)) {
        return;
      }

      // Validar límite de integrantes
      if (this.equipo.length >= 4) {
        this.manejarError('No puedes agregar más de 4 integrantes.');
        return;
      }

      // Agregar nuevo integrante
      this.equipo.push({ nombre: '', correo: '', genero: '', telefono: '', direccion: '' });
    },

    // Método para eliminar un integrante
    eliminarIntegrante(index) {
      this.equipo.splice(index, 1);
    },

    // Método para enviar el formulario completo
    async submitFormularioCompleto() {
      if (!this.validarFormularioCompleto()) {
        return;
      }

      try {
        const respuesta = await axios.post('/api/proyectos', {
          ...this.form,
          equipo: this.equipo,
          perfil_jurado: this.perfilJurado, // Enviar perfiles de jurado
        });
        this.proyecto_id = respuesta.data.id;
        this.mensajeExito = 'Proyecto y equipo registrados correctamente';
        this.mensajeError = '';
        // Redirige agregando el mensaje como parámetro en la URL
        router.visit(`${route('concursos.index')}?success=${encodeURIComponent(this.mensajeExito)}`);
      } catch (error) {
        this.manejarError('Error al registrar el proyecto. Por favor, inténtalo de nuevo.');
        console.error(error);
      }
    },

    // Método para obtener datos iniciales
    async fetchData() {
      try {
        const { data: { modalidades, lineas, concursos } } = await axios.get('/api/modalidades');
        this.modalidades = modalidades;
        this.lineas = lineas;
        this.concursos = concursos;
      } catch (error) {
        this.manejarError('Error al cargar los datos. Por favor, inténtalo de nuevo.');
        console.error(error);
      }
    },

    // Método para editar la selección de perfiles
    editarSeleccionPerfiles() {
      this.mostrarListaPerfiles = true; // Mostrar la lista de perfiles nuevamente
    },

    // Métodos de validación de campos
    validarCorreo(correo) {
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regex.test(correo);
    },
    validarTelefono(telefono) {
      const regex = /^\d{10}$/; // Solo 10 dígitos
      return regex.test(telefono);
    },
    validarDireccion(direccion) {
      const regex = /^[\w\s.,#-]+$/;
      return regex.test(direccion);
    },
  },
  watch: {
    // Observar cambios en la selección de perfiles
    perfilJurado(newVal) {
      if (newVal.length >= 3) {
        this.mostrarListaPerfiles = false; // Ocultar la lista cuando se seleccionan 3 perfiles
      }
    },
  },
  mounted() {
    this.fetchData();
    this.mostrarModalLider = true; // Mostrar modal al montar el componente
  },
};
</script>

<template>
  <div class="p-6 bg-white rounded-lg shadow-md">
    <!-- Modal de advertencia para el líder -->
    <div v-if="mostrarModalLider" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
      <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full text-center">
        <h3 class="text-lg font-bold mb-4 text-[#611232]">Aviso Importante</h3>
        <p class="mb-6">NO es necesario registrarte como líder, el sistema te registra automáticamente.</p>
        <button
          @click="mostrarModalLider = false"
          class="bg-[#611232] text-white px-6 py-2 rounded-md hover:bg-[#8A1C4A] transition duration-300"
        >
          Entendido
        </button>
      </div>
    </div>

    <!-- Indicador de pasos -->
    <div class="flex justify-center mb-6">
      <div class="flex space-x-4">
        <div :class="{ 'bg-guinda text-white': paso === 1, 'bg-gray-300': paso !== 1 }" class="w-8 h-8 rounded-full flex items-center justify-center">1</div>
        <div :class="{ 'bg-guinda text-white': paso === 2, 'bg-gray-300': paso !== 2 }" class="w-8 h-8 rounded-full flex items-center justify-center">2</div>
      </div>
    </div>

    <!-- Mensaje de error -->
    <div v-if="mensajeError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
      <span class="block sm:inline">{{ mensajeError }}</span>
    </div>

    <!-- Mensaje de éxito -->
    <div v-if="mensajeExito" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
      <span class="block sm:inline">{{ mensajeExito }}</span>
    </div>

    <!-- Formulario de registro de proyectos -->
    <form v-if="paso === 1" @submit.prevent="avanzarPaso">
      <h2 class="text-xl font-bold mb-4 text-gris-oscuro">Registro del Proyecto</h2>
      <!-- Campo de nombre del proyecto -->
      <div class="mb-4">
        <label for="nombre" class="block text-gris-oscuro font-bold mb-2">Nombre del Proyecto</label>
        <input type="text" v-model="form.nombre" id="nombre" class="block w-full p-2 border border-gray-300 rounded-md" required>
      </div>
      <!-- Campo de categoría -->
      <div class="mb-4">
        <label for="categoria" class="block text-gris-oscuro font-bold mb-2">Categoría</label>
        <select v-model="form.categoria" id="categoria" class="block w-full p-2 border border-gray-300 rounded-md">
          <option value="alumno">Alumno</option>
          <option value="docente">Docente</option>
        </select>
      </div>
      <!-- Selector de tipo de proyecto -->
      <div class="mb-4">
        <label for="tipoProyecto" class="block text-gris-oscuro font-bold mb-2">Tipo de Proyecto</label>
        <select v-model="selectedTipoProyecto" id="tipoProyecto" class="block w-full p-2 border border-gray-300 rounded-md">
          <option value="prototipos">Prototipos</option>
          <option value="emprendimientos">Emprendimientos</option>
        </select>
      </div>
      <!-- Campo de modalidad -->
      <div class="mb-4">
        <label for="modalidad" class="block text-gris-oscuro font-bold mb-2">Modalidad</label>
        <select v-model="form.modalidad_id" id="modalidad" class="block w-full p-2 border border-gray-300 rounded-md">
          <option v-for="modalidad in modalidadesFiltradas" :key="modalidad.id" :value="modalidad.id">
            {{ modalidad.nombre }}
          </option>
        </select>
      </div>
      <!-- Campo de línea de investigación -->
      <div class="mb-4">
        <label for="linea" class="block text-gris-oscuro font-bold mb-2">Línea de Investigación</label>
        <select v-model="form.linea_investigacion_id" id="linea" class="block w-full p-2 border border-gray-300 rounded-md">
          <option v-for="linea in lineas" :key="linea.id" :value="linea.id">
            {{ linea.nombre }}
          </option>
        </select>
      </div>

    <!-- Sección de Perfil de Jurado -->
    <!-- Sección de Perfil de Jurado -->
    <div class="mt-8">
      <h3 class="text-xl font-bold mb-4">Perfil de Jurado (Máximo 3)</h3>

      <!-- Mostrar lista de perfiles solo si mostrarListaPerfiles es true -->
      <div v-if="mostrarListaPerfiles">
        <input
          v-model="busquedaPerfil"
          type="text"
          placeholder="Buscar perfil..."
          class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232] mb-4"
        />
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
      </div>

      <!-- Mostrar perfiles seleccionados y botón para editar -->
      <div v-if="perfilJurado.length > 0" class="mt-4 mb-6"> <!-- Aumenté el margen inferior (mb-6) -->
        <h4 class="font-semibold text-[#611232] mb-2">Perfiles seleccionados:</h4>
        <ul>
          <li v-for="(perfil, index) in perfilJurado" :key="index" class="text-sm mb-2">{{ perfil }}</li> <!-- Aumenté el margen inferior (mb-2) -->
        </ul>
        <button
          v-if="!mostrarListaPerfiles"
          @click="editarSeleccionPerfiles"
          class="mt-4 bg-[#611232] text-white px-6 py-2 rounded-md hover:bg-[#8A1C4A] transition duration-300 focus:outline-none focus:ring-2 focus:ring-[#611232] focus:ring-offset-2"
        >
          Editar selección
        </button>
      </div>
    </div>


      <!-- Botón para avanzar al siguiente paso -->
      <button type="submit" class="w-full bg-guinda text-white font-bold py-2 px-4 rounded-md hover:bg-guinda-oscuro transition duration-300">
        Siguiente
      </button>
    </form>

    <!-- Formulario de registro de integrantes -->
    <form v-if="paso === 2" @submit.prevent="submitFormularioCompleto">
      <h2 class="text-xl font-bold mb-4">Registro de Integrantes</h2>

      <div v-for="(integrante, index) in equipo" :key="index" class="mb-4">
        <input type="text" v-model="equipo[index].nombre" placeholder="Nombre completo (Apellidos Nombre(s))" required class="block w-full p-2 border border-gray-300 rounded-md mb-2">
        
        <!-- Correo electrónico -->
        <input type="email" v-model="equipo[index].correo" placeholder="Correo Electrónico" required class="block w-full p-2 border border-gray-300 rounded-md mb-2">
        
        <!-- Género -->
        <select v-model="equipo[index].genero" required class="block w-full p-2 border border-gray-300 rounded-md mb-2">
          <option value="" disabled>Seleccionar Género</option>
          <option value="femenino">Femenino</option>
          <option value="masculino">Masculino</option>
        </select>
        
        <!-- Teléfono -->
        <input type="tel" v-model="equipo[index].telefono" placeholder="Teléfono" required class="block w-full p-2 border border-gray-300 rounded-md mb-2">
        
        <!-- Dirección -->
        <input type="text" v-model="equipo[index].direccion" placeholder="Dirección" required class="block w-full p-2 border border-gray-300 rounded-md mb-2">
        
        <button @click="eliminarIntegrante(index)" type="button" class="mt-2 text-red-500">Eliminar</button>
      </div>

      <button type="button" @click="agregarIntegrante" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-md hover:bg-green-700 transition duration-300">
        Agregar Integrante
      </button>

      <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300 mt-4">
        Registrar Proyecto y Equipo
      </button>
    </form>
  </div>
</template>

<style scoped>
.bg-guinda {
  background-color: #6a1b1a;
}

.bg-guinda-oscuro:hover {
  background-color: #4e1413;
}

.bg-dorado {
  background-color: #c7a32e;
}

.bg-dorado-oscuro:hover {
  background-color: #a88c26;
}

.text-gris-oscuro {
  color: #333333;
}

/* Estilo para el mensaje de éxito */
.bg-green-100 {
  background-color: #f0fff4;
}

.border-green-400 {
  border-color: #48bb78;
}

.text-green-700 {
  color: #2f855a;
}
</style>