<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NuevoConcurso from '@/Proyectos/NuevoConcurso.vue';
import MenuLateral from '@/Proyectos/MenuLateral.vue';
import TarjetaCrearConcurso from '@/Components/TarjetaCrearConcurso.vue';
import TarjetaConcurso from '@/Components/TarjetaConcurso.vue';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Estado del menú
const selectedMenu = ref('registro');
const showForm = ref(false);

const handleMenuSelected = (menu) => {
    selectedMenu.value = menu;
};

// Obtener el rol del usuario autenticado
const { props } = usePage();
const userRole = props.auth.user.rol;

// Datos de ejemplo para los concursos
const concursos = ref([
    {
        titulo: 'Concurso de Ciencia',
        fechaInicio: '2023-01-01',
        fechaApertura: '2023-01-15',
        fechaFinalizacion: '2023-02-01',
    },
    {
        titulo: 'Concurso de Tecnología',
        fechaInicio: '2023-02-01',
        fechaApertura: '2023-02-15',
        fechaFinalizacion: '2023-03-01',
    },
]);

const handleCreateClick = () => {
    showForm.value = true;
};

const handleCloseForm = () => {
    showForm.value = false;
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-[#611232]">
                Crear Concurso
            </h2>
        </template>

        <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
            <!-- Menú lateral -->
            <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />

            <!-- Contenido principal -->
            <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
                <h2 class="text-2xl font-bold mb-6 text-[#611232]">
                    {{ selectedMenu.charAt(0).toUpperCase() + selectedMenu.slice(1) }}
                </h2>

                <!-- Formulario para Registro -->
                <div v-if="showForm" class="relative">
                    <NuevoConcurso />
                    <button @click="handleCloseForm" class="absolute top-0 right-0 mt-0 mr-2 text-gray-600 hover:text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Tarjetas de concursos -->
                <div v-if="selectedMenu === 'nuevo concurso' && !showForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <TarjetaCrearConcurso @click="handleCreateClick" />
                    <TarjetaConcurso
                        v-for="concurso in concursos"
                        :key="concurso.titulo"
                        :titulo="concurso.titulo"
                        :fechaInicio="concurso.fechaInicio"
                        :fechaApertura="concurso.fechaApertura"
                        :fechaFinalizacion="concurso.fechaFinalizacion"
                    />
                </div>

                <!-- Secciones en construcción -->
                <p v-if="selectedMenu !== 'registro' && selectedMenu !== 'nuevo concurso'" class="text-gray-600 text-center mt-6">
                    Sección en construcción: {{ selectedMenu.charAt(0).toUpperCase() + selectedMenu.slice(1) }}
                </p>
            </main>
        </div>
    </AuthenticatedLayout>
</template>
