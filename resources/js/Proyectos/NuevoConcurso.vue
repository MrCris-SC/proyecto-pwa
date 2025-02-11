<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    nombre: '',
    descripcion: '',
    fecha_inicio: '',
    fecha_terminacion: '',
});

const submit = () => {
    form.post(route('concursos.store'), {
        onSuccess: () => {
            form.reset();
            // Redirigir manualmente si es necesario:
            router.visit(route('concursos.index'));
        },
    });
};
</script>


<template>
    <div class="relative">
        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="nombre" value="Nombre del Concurso" class="text-[#611232]" />
                <TextInput id="nombre" type="text" class="mt-1 block w-full border-gray-300 focus:border-[#FF6B6B] focus:ring-[#FF6B6B]" v-model="form.nombre" required autofocus />
                <InputError class="mt-2" :message="form.errors.nombre" />
            </div>

            <div>
                <InputLabel for="descripcion" value="Descripción" class="text-[#611232]" />
                <textarea id="descripcion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-[#FF6B6B] focus:ring-[#FF6B6B] h-32" v-model="form.descripcion" required maxlength="150"></textarea>
                <InputError class="mt-2" :message="form.errors.descripcion" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="fecha_inicio" value="Fecha de Inicio" class="text-[#611232]" />
                    <TextInput id="fecha_inicio" type="date" class="mt-1 block w-full border-gray-300 focus:border-[#FF6B6B] focus:ring-[#FF6B6B]" v-model="form.fecha_inicio" required />
                    <InputError class="mt-2" :message="form.errors.fecha_inicio" />
                </div>

                <div>
                    <InputLabel for="fecha_terminacion" value="Fecha de Fin" class="text-[#611232]" />
                    <TextInput id="fecha_terminacion" type="date" class="mt-1 block w-full border-gray-300 focus:border-[#FF6B6B] focus:ring-[#FF6B6B]" v-model="form.fecha_terminacion" required />
                    <InputError class="mt-2" :message="form.errors.fecha_terminacion" />
                </div>
            </div>

            <div class="flex justify-end">
                <PrimaryButton class="bg-[#8A1C4A] hover:bg-[#FF6B6B] text-white px-6 py-2 rounded-lg transition duration-300" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Crear Concurso
                </PrimaryButton>
            </div>
        </form>
        <button @click="$emit('close')" class="absolute top-0 right-0 mt-0 mr-2 text-gray-600 hover:text-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>


