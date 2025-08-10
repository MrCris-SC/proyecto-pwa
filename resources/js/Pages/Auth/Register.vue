<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

import { defineProps, ref } from 'vue';

const props = defineProps({
    rolesParaRegistrar: Array // Definir que es un array
});


const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    rol: '', // Default rol
});

const submit = () => {
    form.post(route('register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Registro de usuarios
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
                <h2 class="text-2xl font-bold mb-6">Registro de usuarios</h2>

                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="name" value="Nombre" />

                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="email" value="Correo" />

                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password" value="Contraseña" />

                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            inputmode="text"
                        />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-4">
                        <InputLabel
                            for="password_confirmation"
                            value="Confirma tu Contraseña"
                        />

                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.password_confirmation"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="rol" value="Rol" />

                        <select id="rol" v-model="form.rol" required>
                            <option disabled value="">Selecciona un rol</option>
                            <option v-for="rol in rolesParaRegistrar" :key="rol" :value="rol">
                                {{ rol }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.rol" />
                    </div>

                    <div class="mt-4 flex items-center justify-end">
                        

                        <PrimaryButton
                            class="ml-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Registrar usuario
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>
