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
const concursos = ref(props.concursos);

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
                    <NuevoConcurso @close="handleCloseForm" />
                </div>

                <!-- Tarjetas de concursos -->
                <div v-if="selectedMenu === 'concursos' && !showForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <TarjetaCrearConcurso 
                    v-if="$page.props.auth.user.rol === 'admin' || $page.props.auth.user.rol === 'vinculador'"
                    @click="handleCreateClick" 
                    class="transition-transform transform hover:scale-105 hover:shadow-lg" />
                    <TarjetaConcurso                        
                        v-for="concurso in concursos"
                        :key="concurso.id"
                        :titulo="concurso.nombre"
                        :fechaInicio="concurso.fecha_inicio"
                        :fechaApertura="concurso.fecha_apertura"
                        :fechaFinalizacion="concurso.fecha_terminacion"
                        class="transition-transform transform hover:scale-105 hover:shadow-lg"
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
