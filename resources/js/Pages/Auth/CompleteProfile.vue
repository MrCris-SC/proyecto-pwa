<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    genero: '',
    telefono: '',
    direccion: '',
    grado_estudio: '',
});

const submit = () => {
    form.post(route('perfil.guardar'));
};
</script>

<template>
    <div class="max-w-lg mx-auto p-4 bg-white shadow-md rounded">
        <h2 class="text-xl font-semibold mb-4">Completa tu perfil</h2>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block text-gray-700">Género</label>
                <select v-model="form.genero" class="w-full border p-2 rounded">
                    <option value="Mujer">Mujer</option>
                    <option value="Hombre">Hombre</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Teléfono</label>
                <input type="text" v-model="form.telefono" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Dirección</label>
                <textarea v-model="form.direccion" class="w-full border p-2 rounded"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Grado de estudio</label>
                <input type="text" v-model="form.grado_estudio" class="w-full border p-2 rounded">
            </div>

            <button type="submit" class="bg-blue-500 text-white p-2 rounded w-full">Guardar</button>
        </form>
    </div>
</template>
