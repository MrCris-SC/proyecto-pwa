<template>
    <div class="p-6">
        <h1 class="text-2xl font-semibold mb-4">Editar Concurso</h1>
        <form @submit.prevent="guardarEdicion">
            <div class="mb-4">
                <label class="block text-gray-700">Nombre</label>
                <input v-model="form.nombre" class="w-full px-3 py-2 border rounded-lg" />
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Descripción</label>
                <textarea v-model="form.descripcion" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Fecha de Inicio</label>
                <input v-model="form.fecha_inicio" type="date" class="w-full px-3 py-2 border rounded-lg" />
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Fecha de Terminación</label>
                <input v-model="form.fecha_terminacion" type="date" class="w-full px-3 py-2 border rounded-lg" />
            </div>
            <div class="flex justify-end">
                <button type="button" @click="cancelar" class="mr-2 px-4 py-2 bg-gray-300 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Guardar</button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Props
const props = defineProps({
    concurso: Object,
});

// Formulario
const form = ref({
    nombre: props.concurso.nombre,
    descripcion: props.concurso.descripcion,
    fecha_inicio: props.concurso.fecha_inicio,
    fecha_terminacion: props.concurso.fecha_terminacion,
});

// Estado de carga
const cargando = ref(false);

// Función para guardar la edición
const guardarEdicion = () => {
    // Validar campos obligatorios
    if (!form.value.nombre || !form.value.descripcion || !form.value.fecha_inicio || !form.value.fecha_terminacion) {
        alert('Todos los campos son obligatorios.');
        return;
    }

    // Activar estado de carga
    cargando.value = true;

    // Enviar los datos al backend
    router.put(`/concursos/${props.concurso.id}`, form.value, {
        onSuccess: () => {
            // Redirigir a la página de concursos después de guardar
            alert('Concurso actualizado correctamente.');
            router.visit('/concursos');
        },
        onError: (errors) => {
            console.error('Error al guardar:', errors);
            alert('Hubo un error al guardar los cambios. Por favor, inténtalo de nuevo.');
        },
        onFinish: () => {
            // Desactivar estado de carga
            cargando.value = false;
        }
    });
};

// Función para cancelar la edición
const cancelar = () => {
    if (confirm('¿Estás seguro de que deseas cancelar la edición? Los cambios no se guardarán.')) {
        router.visit('/concursos');
    }
};
</script>