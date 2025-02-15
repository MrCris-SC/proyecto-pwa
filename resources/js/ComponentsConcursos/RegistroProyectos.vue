<script>
import axios from 'axios';

export default {
  props: {
    concursoId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      paso: 1, // Controla el cambio de formularios
      form: {
        nombre: '',
        categoria: 'alumno',
        modalidad_id: '',
        linea_investigacion_id: '',
        concurso_id: this.concursoId // Inicializar con el ID del concurso recibido
      },
      equipo: [{ nombre: '' }], // Lista de integrantes
      modalidades: [],
      lineas: [],
      concursos: [], // Lista de concursos
      proyecto_id: null
    };
  },
  
  methods: {
    async fetchData() {
      try {
        const response = await axios.get('/api/modalidades');
        this.modalidades = response.data.modalidades;
        this.lineas = response.data.lineas;
        this.concursos = response.data.concursos; // Obtener concursos
      } catch (error) {
        console.error(error);
      }
    },

    avanzarPaso() {
      this.paso = 2; // Avanzar al formulario de integrantes
    },

    agregarIntegrante() {
      this.equipo.push({ nombre: '' });
    },

    eliminarIntegrante(index) {
      this.equipo.splice(index, 1);
    },

    async submitFormularioCompleto() {
      try {
        const response = await axios.post('/api/proyectos', {
          ...this.form,
          equipo: this.equipo
        });
        this.proyecto_id = response.data.id;
        alert("Proyecto y equipo registrados correctamente");
        window.location.href = '/concursos'; // Redireccionar al usuario a /concursos
      
      } catch (error) {
        console.error(error);
      }
    }
  },

  mounted() {
    this.fetchData();
  }
};
</script>

<template>
  <div class="p-6 bg-white rounded-lg shadow-md">
    <form v-if="paso === 1" @submit.prevent="avanzarPaso">
      <h2 class="text-xl font-bold mb-4">Registro del Proyecto</h2>
      <div class="mb-4">
        <label for="nombre" class="block text-gray-700 font-bold mb-2">Nombre del Proyecto</label>
        <input type="text" v-model="form.nombre" id="nombre" class="block w-full p-2 border border-gray-300 rounded-md" required>
      </div>

      <div class="mb-4">
        <label for="categoria" class="block text-gray-700 font-bold mb-2">Categoría</label>
        <select v-model="form.categoria" id="categoria" class="block w-full p-2 border border-gray-300 rounded-md">
          <option value="alumno">Alumno</option>
          <option value="docente">Docente</option>
        </select>
      </div>

      <div class="mb-4">
        <label for="modalidad" class="block text-gray-700 font-bold mb-2">Modalidad</label>
        <select v-model="form.modalidad_id" id="modalidad" class="block w-full p-2 border border-gray-300 rounded-md">
          <option v-for="modalidad in modalidades" :key="modalidad.id" :value="modalidad.id">
            {{ modalidad.nombre }}
          </option>
        </select>
      </div>

      <div class="mb-4">
        <label for="linea" class="block text-gray-700 font-bold mb-2">Línea de Investigación</label>
        <select v-model="form.linea_investigacion_id" id="linea" class="block w-full p-2 border border-gray-300 rounded-md">
          <option v-for="linea in lineas" :key="linea.id" :value="linea.id">
            {{ linea.nombre }}
          </option>
        </select>
      </div>

      <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">
        Siguiente
      </button>
    </form>

    <form v-if="paso === 2" @submit.prevent="submitFormularioCompleto">
      <h2 class="text-xl font-bold mb-4">Registro de Integrantes</h2>

      <div v-for="(integrante, index) in equipo" :key="index" class="mb-4">
        <input type="text" v-model="equipo[index].nombre" placeholder="Nombre del Integrante" required class="block w-full p-2 border border-gray-300 rounded-md">
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
form {
  max-width: 600px;
  margin: auto;
}
</style>

