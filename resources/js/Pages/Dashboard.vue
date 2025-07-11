<script setup>
// Importa el layout autenticado y utilidades de Inertia y Vue
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

// Variable reactiva para mostrar mensajes de éxito
const successMessage = ref('');
// Acceso a las propiedades globales de la página
const page = usePage();

// Al montar el componente, realiza verificaciones y redirecciones
onMounted(() => {
    // Si hay un mensaje flash de éxito, lo muestra temporalmente
    if (page.props.flash?.success) {
        successMessage.value = page.props.flash.success;

        setTimeout(() => {
            successMessage.value = '';
        }, 5000);
    }

    // Si el usuario debe completar su perfil, lo redirige
    if (page.props.completar_perfil) {
        router.get(route('perfil.completar'));
    }

    // Si el usuario es evaluador y no ha completado perfiles, lo redirige a seleccionar perfil
    if (page.props.user.rol === 'evaluador' && !page.props.user.has_completed_profiles) {
        router.get(route('perfil.select'));
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Inicio
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Bienvenido!</div>
                </div>
            </div>
        </div>

        <div
            v-if="successMessage"
            class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg animate-fade"
        >
            {{ successMessage }}
        </div>
    </AuthenticatedLayout>
</template>

<style>
@keyframes fade {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade {
    animation: fade 0.5s ease-in-out;
}
</style>
