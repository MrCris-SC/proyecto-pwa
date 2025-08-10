<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { defineProps, computed } from 'vue';

const props = defineProps({
    rolesParaRegistrar: Array // Definir que es un array
});

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    password: '',
    password_confirmation: '',
    rol: props.user?.rol || props.rolesParaRegistrar[0] || ''
});

const submit = () => {
    if (props.isEditing) {
        form.put(route('usuarios.update', props.user.id)), {
            onFinish: () => form.reset('password', 'password_confirmation'),
            onSuccess: () => {
                // Redirigir después de editar
                router.visit(route('gestion.usuarios.index'));
            }
        };
    } else {
        form.post(route('register.store'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
            onSuccess: () => {
                // Redirigir después de crear
                window.location.href = 'http://127.0.0.1:8000/new-user';
            }
        });
    }
};

// Mapear nombres de roles más legibles
const roleNames = {
    'admin': 'Administrador',
    'vinculador': 'Vinculador',
    'evaluador': 'Evaluador',
    'lider': 'Líder de equipo',
    'asesor': 'Asesor',
    'participante': 'Participante'
};

// Computed para título dinámico
const pageTitle = computed(() => {
    return props.isEditing ? 'Editar Usuario' : 'Registro de Usuarios';
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-[#611232]">
                {{ pageTitle }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
                <h2 class="text-2xl font-bold mb-6 text-[#611232]">{{ pageTitle }}</h2>

                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Nombre -->
                        <div>
                            <InputLabel for="name" value="Nombre Completo" />
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

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" value="Correo Electrónico" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                :disabled="isEditing"
                                required
                                autocomplete="email"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Contraseña -->
                        <div v-if="!isEditing">
                            <InputLabel for="password" value="Contraseña" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- Nueva Contraseña (solo en edición) -->
                        <div v-if="isEditing">
                            <InputLabel for="password" value="Nueva Contraseña (opcional)" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password"
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div v-if="!isEditing">
                            <InputLabel for="password_confirmation" value="Confirmar Contraseña" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Rol -->
                        <div>
                            <InputLabel for="rol" value="Rol del Usuario" />
                            <select
                                id="rol"
                                v-model="form.rol"
                                class="mt-1 block w-full border-gray-300 focus:border-[#611232] focus:ring-[#611232] rounded-md shadow-sm"
                                required
                            >
                                <option v-if="!isEditing" disabled value="">Selecciona un rol</option>
                                <option v-for="rol in rolesParaRegistrar" :key="rol" :value="rol">
                                    {{ roleNames[rol] || rol }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.rol" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <Link 
                            :href="isEditing ? route('gestion.usuarios.index') : route('dashboard')" 
                            class="text-[#611232] hover:text-[#8a1a4a] font-medium"
                        >
                            Volver
                        </Link>
                        <PrimaryButton
                            class="ml-4 bg-[#611232] hover:bg-[#8a1a4a] focus:bg-[#4a0d27]"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ isEditing ? 'Actualizar Usuario' : 'Registrar Usuario' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>