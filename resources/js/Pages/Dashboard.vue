<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const successMessage = ref('');
const page = usePage();

onMounted(() => {
    if (page.props.flash?.success) {
        successMessage.value = page.props.flash.success;
        setTimeout(() => { successMessage.value = ''; }, 5000);
    }
    if (page.props.completar_perfil) {
        router.get(route('perfil.completar'));
    }
    if (page.props.user.rol === 'evaluador' && !page.props.user.has_completed_profiles) {
        router.get(route('perfil.select'));
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-[#611232] rounded-full flex items-center justify-center text-[#FFD700] mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-900">
                        Inicio
                    </h2>
                    <p class="text-sm text-gray-700">Concurso Nacional de Prototipos y Proyectos de Emprendimiento</p>
                </div>
            </div>
        </template>

        <div class="py-4 sm:py-8">
            <div class="mx-auto px-4 sm:px-6 lg:px-8 space-y-6 max-w-7xl">
                <!-- Hero Section - Actualizado al color #611232 -->
                <div class="relative bg-gradient-to-r from-[#611232] to-[#450A1C] rounded-lg sm:rounded-xl shadow-xl overflow-hidden">
                    <div class="absolute inset-0 opacity-20 bg-[url('https://tailwindui.com/img/beams-pattern.png')] bg-cover"></div>
                    <div class="relative px-4 py-6 sm:p-8 md:p-10">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Innovación Tecnológica y Emprendimiento</h1>
                        <p class="mt-2 sm:mt-3 text-sm sm:text-base md:text-lg text-[#FFD700] max-w-2xl">
                            Formando bachilleres técnicos que desarrollen y preserven la cultura tecnológica para satisfacer las necesidades del país.
                        </p>
                    </div>
                </div>

                <!-- Cards Grid - Iconos actualizados -->
                <div class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Historia Card -->
                    <div class="overflow-hidden bg-white rounded-lg shadow transition-all duration-300 hover:shadow-lg border border-gray-200">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center mb-3 sm:mb-4">
                                <div class="flex-shrink-0 p-2 sm:p-3 bg-[#611232] bg-opacity-10 rounded-lg text-[#611232]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="ml-3 text-base sm:text-lg font-medium text-gray-900">Nuestra Historia</h3>
                            </div>
                            <div class="text-sm sm:text-base text-gray-700 space-y-2">
                                <p>Creado en 1991 como Exposición de Prototipos</p>
                                <p>En 1999 se convierte en Concurso Nacional</p>
                                <p>Desde 2010 incorpora categoría de emprendedores</p>
                            </div>
                        </div>
                    </div>

                    <!-- Objetivos Card -->
                    <div class="overflow-hidden bg-white rounded-lg shadow transition-all duration-300 hover:shadow-lg border border-gray-200">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center mb-3 sm:mb-4">
                                <div class="flex-shrink-0 p-2 sm:p-3 bg-[#FFD700] bg-opacity-20 rounded-lg text-[#611232]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="ml-3 text-base sm:text-lg font-medium text-gray-900">Nuestros Objetivos</h3>
                            </div>
                            <ul class="text-sm sm:text-base text-gray-700 space-y-2 list-disc list-inside">
                                <li>Fomentar la actividad científica</li>
                                <li>Promover la innovación tecnológica</li>
                                <li>Impulsar el emprendimiento</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Enfoque Card -->
                    <div class="overflow-hidden bg-white rounded-lg shadow transition-all duration-300 hover:shadow-lg border border-gray-200">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center mb-3 sm:mb-4">
                                <div class="flex-shrink-0 p-2 sm:p-3 bg-[#611232] bg-opacity-10 rounded-lg text-[#611232]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <h3 class="ml-3 text-base sm:text-lg font-medium text-gray-900">Enfoque Educativo</h3>
                            </div>
                            <div class="text-sm sm:text-base text-gray-700 space-y-2">
                                <p>Vinculado al Marco Curricular Común</p>
                                <p>Parte de la Nueva Escuela Mexicana</p>
                                <p>Promueve cultura de paz e inclusión</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modalidades - Secciones actualizadas -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Prototipos Section -->
                    <div class="overflow-hidden bg-white rounded-lg shadow border border-gray-200">
                        <div class="px-4 py-3 sm:px-6 sm:py-4 bg-[#611232]">
                            <h3 class="text-lg font-semibold text-white">Modalidades de Prototipos</h3>
                        </div>
                        <div class="grid grid-cols-1 gap-3 p-4 sm:gap-4 sm:p-6 md:grid-cols-3">
                            <!-- Tecnológicos -->
                            <div class="p-3 sm:p-4 border border-gray-200 rounded-lg hover:bg-[#611232]/5 transition-colors">
                                <div class="flex items-center mb-2">
                                    <div class="flex-shrink-0 p-1 sm:p-2 bg-[#611232]/10 rounded-md text-[#611232]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <h4 class="ml-2 text-sm sm:text-base font-medium text-gray-900">Tecnológicos</h4>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700">Bienes, innovaciones o procesos con características industriales/tecnológicas</p>
                            </div>
                            
                            <!-- Didácticos -->
                            <div class="p-3 sm:p-4 border border-gray-200 rounded-lg hover:bg-[#611232]/5 transition-colors">
                                <div class="flex items-center mb-2">
                                    <div class="flex-shrink-0 p-1 sm:p-2 bg-[#611232]/10 rounded-md text-[#611232]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <h4 class="ml-2 text-sm sm:text-base font-medium text-gray-900">Didácticos</h4>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700">Orientados a la práctica complementaria de la enseñanza-aprendizaje</p>
                            </div>
                            
                            <!-- Software -->
                            <div class="p-3 sm:p-4 border border-gray-200 rounded-lg hover:bg-[#611232]/5 transition-colors">
                                <div class="flex items-center mb-2">
                                    <div class="flex-shrink-0 p-1 sm:p-2 bg-[#611232]/10 rounded-md text-[#611232]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                        </svg>
                                    </div>
                                    <h4 class="ml-2 text-sm sm:text-base font-medium text-gray-900">Software</h4>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700">Propuestas lógicas de solución en forma de código programado</p>
                            </div>
                        </div>
                    </div>

                    <!-- Emprendimientos Section -->
                    <div class="overflow-hidden bg-white rounded-lg shadow border border-gray-200">
                        <div class="px-4 py-3 sm:px-6 sm:py-4 bg-[#611232]">
                            <h3 class="text-lg font-semibold text-white">Categorías de Emprendimiento</h3>
                        </div>
                        <div class="grid grid-cols-1 gap-3 p-4 sm:gap-4 sm:p-6 md:grid-cols-3">
                            <!-- Social -->
                            <div class="p-3 sm:p-4 border border-gray-200 rounded-lg hover:bg-[#FFD700]/10 transition-colors">
                                <div class="flex items-center mb-2">
                                    <div class="flex-shrink-0 p-1 sm:p-2 bg-[#FFD700]/20 rounded-md text-[#611232]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="ml-2 text-sm sm:text-base font-medium text-gray-900">Social</h4>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700">Soluciones innovadoras para problemas sociales</p>
                            </div>
                            
                            <!-- Verde -->
                            <div class="p-3 sm:p-4 border border-gray-200 rounded-lg hover:bg-[#FFD700]/10 transition-colors">
                                <div class="flex items-center mb-2">
                                    <div class="flex-shrink-0 p-1 sm:p-2 bg-[#FFD700]/20 rounded-md text-[#611232]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </div>
                                    <h4 class="ml-2 text-sm sm:text-base font-medium text-gray-900">Verde</h4>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700">Vinculados al medio ambiente y desarrollo sostenible</p>
                            </div>
                            
                            <!-- Tecnologías -->
                            <div class="p-3 sm:p-4 border border-gray-200 rounded-lg hover:bg-[#FFD700]/10 transition-colors">
                                <div class="flex items-center mb-2">
                                    <div class="flex-shrink-0 p-1 sm:p-2 bg-[#FFD700]/20 rounded-md text-[#611232]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="ml-2 text-sm sm:text-base font-medium text-gray-900">Tecnologías</h4>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700">Basados en investigación con tecnología propia e innovadora</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notificación actualizada -->
        <div v-if="successMessage" class="fixed top-5 right-5 bg-[#611232] text-[#FFD700] px-4 py-2 rounded shadow-lg animate-fade border border-[#FFD700]/30">
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