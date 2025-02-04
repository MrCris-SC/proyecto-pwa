<script setup>
import { useForm } from '@inertiajs/vue3';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

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
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-customColor">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 bg-customColor">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="max-w-lg mx-auto p-6 bg-white shadow-lg rounded-lg mt-6">
                <h2 class="text-2xl font-semibold mb-6 text-gray-800">Completa tu perfil</h2>

                <form @submit.prevent="submit">
                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Género</label>
                        <select v-model="form.genero" class="w-full border p-3 rounded-lg bg-gray-50">
                            <option value="Mujer">Mujer</option>
                            <option value="Hombre">Hombre</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Teléfono</label>
                        <input type="text" v-model="form.telefono" class="w-full border p-3 rounded-lg bg-gray-50">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Dirección</label>
                        <textarea v-model="form.direccion" class="w-full border p-3 rounded-lg bg-gray-50"></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Grado de estudio</label>
                        <input type="text" v-model="form.grado_estudio" class="w-full border p-3 rounded-lg bg-gray-50">
                    </div>

                    <button type="submit" class="bg-blue-600 text-white p-3 rounded-lg w-full hover:bg-blue-700 transition duration-150 ease-in-out">Guardar</button>
                </form>

                <ResponsiveNavLink href="/some-link" class="mt-6">Ir a algún lugar</ResponsiveNavLink>
            </div>
        </div>
    </div>
</template>
