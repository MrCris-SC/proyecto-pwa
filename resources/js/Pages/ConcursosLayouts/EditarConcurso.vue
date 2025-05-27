<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-[#611232]">
                Editar Concurso
            </h2>
        </template>
        <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
            <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
                <div class="relative">
                    <div>
                        <div v-if="!concurso || !concurso.id" class="flex items-center justify-center h-screen">
                            <p>Cargando...</p>
                        </div>
                        <div v-else class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md">
                            <h2 class="text-2xl font-bold mb-4">Editar Concurso</h2>
                            <form @submit.prevent="submitForm" class="space-y-6">
                                <div>
                                    <label for="nombre" class="block text-gray-700 font-bold">Nombre del Concurso</label>
                                    <input type="text" id="nombre" v-model="form.nombre" class="mt-1 block w-full p-2 border border-gray-300 rounded" required />
                                </div>
                                <div>
                                    <label for="descripcion" class="block text-gray-700 font-bold">Descripción</label>
                                    <textarea id="descripcion" v-model="form.descripcion" class="mt-1 block w-full p-2 border border-gray-300 rounded" required></textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="fecha_inicio" class="block text-gray-700 font-bold">Fecha de Inicio</label>
                                        <input type="date" id="fecha_inicio" v-model="form.fecha_inicio" class="mt-1 block w-full p-2 border border-gray-300 rounded" required />
                                    </div>
                                    <div>
                                        <label for="fecha_apertura" class="block text-gray-700 font-bold">Fecha de Apertura</label>
                                        <input type="date" id="fecha_apertura" v-model="form.fecha_apertura" class="mt-1 block w-full p-2 border border-gray-300 rounded" required />
                                    </div>
                                    <div>
                                        <label for="fecha_terminacion" class="block text-gray-700 font-bold">Fecha de Terminación</label>
                                        <input type="date" id="fecha_terminacion" v-model="form.fecha_terminacion" class="mt-1 block w-full p-2 border border-gray-300 rounded" required />
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-300">
                                        Actualizar Concurso
                                    </button>
                                </div>
                            </form>
                            <div class="mt-4">
                                <button @click="goBack" class="text-gray-600 hover:text-gray-800">Volver a Concursos</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive, watch, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    concurso: {
        type: Object,
        default: () => ({})
    }
});

// Formulario reactivo con todos los campos necesarios
const form = reactive({
    id: props.concurso.id || null,
    nombre: props.concurso.nombre || '',
    descripcion: props.concurso.descripcion || '',
    fecha_inicio: props.concurso.fecha_inicio || '',
    fecha_apertura: props.concurso.fecha_apertura || '',
    fecha_terminacion: props.concurso.fecha_terminacion || '',
    fase: props.concurso.fase || 'local',
    estado: props.concurso.estado || null,
    plantel_id: props.concurso.plantel_id || null,
    _method: 'PUT' // Necesario para las actualizaciones con Inertia
});

// Actualizar el formulario si las props cambian
watch(() => props.concurso, (newVal) => {
    Object.assign(form, {
        id: newVal.id,
        nombre: newVal.nombre,
        descripcion: newVal.descripcion,
        fecha_inicio: newVal.fecha_inicio,
        fecha_apertura: newVal.fecha_apertura,
        fecha_terminacion: newVal.fecha_terminacion,
        fase: newVal.fase,
        estado: newVal.estado,
        plantel_id: newVal.plantel_id
    });
});

function submitForm() {
    router.post(route('concursos.update', form.id), form);
}

function goBack() {
    router.get(route('concursos.index'));
}
</script>