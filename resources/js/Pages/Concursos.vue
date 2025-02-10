<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Formulario de creación de concurso
const form = useForm({
    nombre: '',
    descripcion: '',
    fecha_inicio: '',
    fecha_fin: '',
});

const submit = () => {
    form.post(route('concursos.store'), {
        onFinish: () => form.reset(),
    });
};

// Estado del menú
const selectedMenu = ref('convocatoria');
const menuItems = ['Convocatoria', 'Bases', 'Jueces', 'Participantes', 'Resultados'];
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
            <aside class="w-full lg:w-64 bg-[#611232] text-white shadow-lg rounded-lg p-5 mb-6 lg:mb-0 lg:mr-6">
                <h3 class="text-lg font-semibold mb-6">Menú</h3>
                <ul class="space-y-3">
                    <li 
                        v-for="item in menuItems" 
                        :key="item"
                        class="p-3 rounded-lg cursor-pointer transition duration-200 ease-in-out"
                        :class="selectedMenu === item.toLowerCase() ? 'bg-[#8A1C4A] text-white shadow-md' : 'hover:bg-[#9C2755] hover:text-white'"
                        @click="selectedMenu = item.toLowerCase()"
                    >
                        <span class="flex items-center">
                            <!-- Icono (puedes usar una librería como FontAwesome) -->
                            <i class="fas fa-folder mr-2"></i>
                            {{ item }}
                        </span>
                    </li>
                </ul>
            </aside>

            <!-- Contenido principal -->
            <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
                <h2 class="text-2xl font-bold mb-6 text-[#611232]">
                    {{ selectedMenu.charAt(0).toUpperCase() + selectedMenu.slice(1) }}
                </h2>

                <!-- Formulario para Convocatoria -->
                <form v-if="selectedMenu === 'convocatoria'" @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="nombre" value="Nombre del Concurso" class="text-[#611232]" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full border-gray-300 focus:border-[#FF6B6B] focus:ring-[#FF6B6B]" v-model="form.nombre" required autofocus />
                        <InputError class="mt-2" :message="form.errors.nombre" />
                    </div>

                    <div>
                        <InputLabel for="descripcion" value="Descripción" class="text-[#611232]" />
                        <textarea id="descripcion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-[#FF6B6B] focus:ring-[#FF6B6B] h-32" v-model="form.descripcion" required></textarea>
                        <InputError class="mt-2" :message="form.errors.descripcion" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="fecha_inicio" value="Fecha de Inicio" class="text-[#611232]" />
                            <TextInput id="fecha_inicio" type="date" class="mt-1 block w-full border-gray-300 focus:border-[#FF6B6B] focus:ring-[#FF6B6B]" v-model="form.fecha_inicio" required />
                            <InputError class="mt-2" :message="form.errors.fecha_inicio" />
                        </div>

                        <div>
                            <InputLabel for="fecha_fin" value="Fecha de Fin" class="text-[#611232]" />
                            <TextInput id="fecha_fin" type="date" class="mt-1 block w-full border-gray-300 focus:border-[#FF6B6B] focus:ring-[#FF6B6B]" v-model="form.fecha_fin" required />
                            <InputError class="mt-2" :message="form.errors.fecha_fin" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <PrimaryButton class="bg-[#8A1C4A] hover:bg-[#FF6B6B] text-white px-6 py-2 rounded-lg transition duration-300" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Crear Concurso
                        </PrimaryButton>
                    </div>
                </form>

                <!-- Secciones en construcción -->
                <p v-if="selectedMenu !== 'convocatoria'" class="text-gray-600 text-center mt-6">
                    Sección en construcción: {{ selectedMenu.charAt(0).toUpperCase() + selectedMenu.slice(1) }}
                </p>
            </main>
        </div>
    </AuthenticatedLayout>
</template>
