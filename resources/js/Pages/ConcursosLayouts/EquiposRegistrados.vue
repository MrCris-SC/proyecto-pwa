<script>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';

export default {
  data() {
    return {
      equipos: [],
      loading: true,
    };
  },
  methods: {
    async fetchEquipos() {
      try {
        const response = await fetch('/api/concursos/equipos'); // Replace with your API endpoint
        if (!response.ok) throw new Error('Error al cargar los equipos');
        this.equipos = await response.json();
      } catch (error) {
        console.error(error);
        this.equipos = [];
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.fetchEquipos();
  },
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-[#611232]">
                Crear Concurso
            </h2>
        </template>
    </AuthenticatedLayout>

    <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
        <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
        
        <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-bold mb-6 text-[#611232]">
            {{ selectedMenu }}
            </h2>
            
            <div class="equipos-registrados">
                <h1>Equipos Registrados</h1>
                <div v-if="loading" class="loading">Cargando equipos...</div>
                <div v-else>
                <div v-if="equipos.length === 0" class="no-equipos">No hay equipos registrados.</div>
                <ul v-else>
                    <li v-for="equipo in equipos" :key="equipo.id" class="equipo-item">
                    <h2>{{ equipo.nombre }}</h2>
                    <p><strong>Integrantes:</strong> {{ equipo.integrantes.join(', ') }}</p>
                    <p><strong>Detalles:</strong> {{ equipo.detalles }}</p>
                    </li>
                </ul>
                </div>
            </div>


        </main>
    </div>

  
</template>

<style scoped>
.equipos-registrados {
  padding: 20px;
}

.loading {
  font-size: 18px;
  color: gray;
}

.no-equipos {
  font-size: 16px;
  color: red;
}

.equipo-item {
  margin-bottom: 20px;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.equipo-item h2 {
  margin: 0;
  font-size: 20px;
}

.equipo-item p {
  margin: 5px 0;
}
</style>