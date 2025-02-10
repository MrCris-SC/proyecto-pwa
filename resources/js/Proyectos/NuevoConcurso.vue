<template>
    <form @submit.prevent="submit" class="space-y-6">
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
</template>

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
    fecha_fin: '',
});

const submit = () => {
    form.post(route('concursos.store'), {
        onFinish: () => form.reset(),
    });
};
</script>
