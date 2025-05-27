<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { faQuestionCircle, faTimes, faWindowMinimize, faArrowLeft, faArrowRight } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const showingNavigationDropdown = ref(false);
const showTutorial = ref(false);
const isMinimized = ref(false);
const currentStep = ref(1);

const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const userRole = computed(() => user.value?.rol || 'default');

// Contenido del tutorial
const tutorialContent = {
    lider: {
        title: 'Guía Rápida para Líderes de Equipo',
        steps: [
            {
                section: 'Registro en Concursos',
                content: 'Proceso para inscribirte y registrar tu equipo:',
                items: [
                    'Visualiza todas las convocatorias activas en la sección "Concursos"',
                    'Haz clic en la tarjeta del concurso para registrarte',
                    'Completa los datos de tu proyecto y equipo'
                ]
            },
            {
                section: 'Gestión de Proyectos',
                content: 'Pasos para completar tu inscripción:',
                items: [
                    '1 Registra a tus asesores (técnico y metodológico)',
                    '2 Descarga los formatos requeridos',
                    '   FOREG, FOAPA, FOCOMO, FOAS (obligatorios)',
                    '   Documentos opcionales que enriquecen tu proyecto',
                    '3 Llena y sube los documentos en "Documentación Requerida"',
                    '4 Opcionalmente sube documentos adicionales en la sección correspondiente'
                ]
            },
            {
                section: 'Seguimiento',
                content: 'Monitorea el progreso de tu proyecto:',
                items: [
                    'Revisa "Proceso" para ver el estado actual',
                    'Verifica el porcentaje de completado',
                    'Recibe notificaciones de avances'
                ]
            },
            {
                section: 'Resultados',
                content: 'Consulta evaluaciones y reportes:',
                items: [
                    'Visualiza historial de participaciones pasadas',
                    'Accede a reportes detallados de tu concurso actual',
                    'Revisa retroalimentación de evaluadores'
                ]
            },
            {
                section: 'Equipos',
                content: 'Administración de miembros:',
                items: [
                    'Gestiona participantes de tu equipo',
                    'Visualiza otros equipos del mismo concurso',
                    'Coordina con asesores y miembros'
                ]
            }
        ]
    },
    admin: {
        title: 'Guía Rápida para Administradores',
        steps: [
            {
                section: 'Registro de Usuarios',
                content: 'Desde el formulario podrás crear cuentas para:',
                items: [
                    'Asignar roles: admin, vinculador, evaluador, asesor o participante',
                    'Ingresar datos básicos: nombre, correo y contraseña',
                    'Gestionar permisos y accesos'
                ]
            },
            {
                section: 'Menú Principal',
                content: 'Opciones disponibles en el panel lateral:',
                items: [
                    '1 Concursos',
                    '2 Registro de Criterios', 
                    '3 Configuración',
                    '4 Reportes'
                ]
            },
            {
                section: 'Gestión de Concursos',
                content: 'Acciones disponibles:',
                items: [
                    'Crear/editar concursos con fechas (inicio, apertura, finalización)',
                    'Definir fase: local, estatal o nacional',
                    'Descargar formato FOREC',
                    'Visualizar podios y resultados'
                ]
            },
            {
                section: 'Criterios de Evaluación',
                content: 'Configuración por concurso:',
                items: [
                    'Agregar/editar criterios según modalidad',
                    'Asignar puntuaciones y pesos',
                    'Definir visibilidad para evaluadores',
                    'Distribuir criterios a equipos'
                ]
            },
            {
                section: 'Reportes',
                content: 'Seguimiento de evaluaciones:',
                items: [
                    'Monitorear progreso de evaluaciones',
                    'Identificar evaluaciones pendientes/completadas',
                    'Exportar reportes finales',
                    'Generar estadísticas por proyecto'
                ]
            }
        ]
    },
    evaluador: {
        title: 'Guía para Evaluadores',
        steps: [
            {
                section: 'Concursos Asignados',
                content: 'Acceso a concursos disponibles:',
                items: [
                    'Revisa la lista de concursos que te han sido asignados',
                    'Inscríbete en los concursos que evaluarás',
                    'Verifica fechas límite y requisitos específicos'
                ]
            },
            {
                section: 'Proceso de Evaluación',
                content: 'Cómo calificar proyectos:',
                items: [
                    '1 Accede a la sección "Evaluación"',
                    '2 Revisa cada proyecto asignado',
                    '3 Califica según los criterios establecidos',
                    '4 Proporciona retroalimentación constructiva',
                    '5 Guarda y envía tu evaluación'
                ]
            },
            {
                section: 'Proyectos Asignados',
                content: 'Información disponible:',
                items: [
                    'Listado completo de proyectos a evaluar',
                    'Detalles del proyecto: nombre, categoría, modalidad',
                    'Documentación adjunta por los participantes',
                    'Estado actual de evaluación'
                ]
            },
            {
                section: 'Criterios de Evaluación',
                content: 'Estructura de calificación:',
                items: [
                    'Visualiza todos los criterios por modalidad',
                    'Consulta puntuaciones y pesos asignados',
                    'Revisa categorías y subcriterios',
                    'Verifica rúbricas de evaluación'
                ]
            },
            {
                section: 'Reportes y Resultados',
                content: 'Generación de informes:',
                items: [
                    'Accede a tus evaluaciones completadas',
                    'Genera reportes individuales por proyecto',
                    'Visualiza resultados finales enviados',
                    'Exporta datos para tu registro'
                ]
            }
        ]
    },
    default: {
        title: 'Guía de Usuario',
        steps: [
            '1 Completa tu perfil para acceso completo',
            '2 Explora las secciones disponibles',
            '3 Consulta la ayuda cuando lo necesites'
        ]
    }
};

const currentTutorial = computed(() => tutorialContent[userRole.value] || tutorialContent.default);
const totalSteps = computed(() => currentTutorial.value.steps.length);

onMounted(() => {
    if (!localStorage.getItem('tutorialShown')) {
        showTutorial.value = true;
        localStorage.setItem('tutorialShown', 'true');
    }
});

const toggleTutorial = () => {
    showTutorial.value = !showTutorial.value;
    if (showTutorial.value) {
        isMinimized.value = false;
    }
};

const minimizeTutorial = (e) => {
    e.stopPropagation();
    isMinimized.value = true;
};

const closeTutorial = (e) => {
    if (e) e.stopPropagation();
    showTutorial.value = false;
    isMinimized.value = false;
    currentStep.value = 1;
};

const nextStep = (e) => {
    if (e) e.stopPropagation();
    if (currentStep.value < totalSteps.value) currentStep.value++;
    else closeTutorial();
};

const prevStep = (e) => {
    if (e) e.stopPropagation();
    if (currentStep.value > 1) currentStep.value--;
};

const getListStyle = (item) => {
    if (/^\d+\s/.test(item)) return 'decimal';
    if (/^-\s/.test(item)) return 'disc';
    if (/^\s{2,}/.test(item)) return 'none';
    return 'disc';
};

const cleanItemText = (item) => {
    return item.replace(/^(\d+\s|-\s|\s{2,})/, '').trim();
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

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex bg-customColor">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Inicio
                                </NavLink>

                                <NavLink v-if="userRole === 'admin'" :href="route('new.user')">
                                    Registrar Usuarios
                                </NavLink>

                                <NavLink :href="route('concursos.index')" :active="route().current('concursos.index')">
                                    Concursos
                                </NavLink>
                                
                                <NavLink v-if="userRole === 'vinculador'" :href="route('new.user')">
                                    Registrar Lideres
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                                {{ user.name }}
                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 111.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Perfil
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Cerrar Sesión
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Inicio
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Perfil
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Cerrar Sesión
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>

            <!-- Botón flotante de ayuda (global) -->
            <button 
                @click="toggleTutorial"
                class="fixed bottom-5 right-5 bg-[#611232] text-white p-3 sm:p-4 rounded-full shadow-xl hover:bg-[#8A1C4A] transition-all transform hover:scale-110 z-50 flex items-center"
                :class="{ '!bg-[#8A1C4A]': showTutorial && !isMinimized }"
                title="Mostrar/Ocultar tutorial"
            >
                <FontAwesomeIcon :icon="faQuestionCircle" class="text-lg sm:text-xl" />
            </button>

            <!-- Ventana flotante del tutorial (global) -->
            <div 
                v-if="showTutorial && userRole"
                class="fixed bottom-20 right-0 sm:right-5 w-full max-w-xs sm:max-w-md md:max-w-lg bg-white rounded-t-lg shadow-2xl border-2 border-[#611232] z-50 flex flex-col transition-all duration-300"
                :class="{
                    'h-16': isMinimized, 
                    'h-96 md:h-[32rem]': !isMinimized,
                    'w-auto min-w-[18rem]': isMinimized
                }"
                style="max-height: 90vh;"
            >
                <!-- Barra de título -->
                <div 
                    class="bg-[#611232] text-white p-3 rounded-t-[calc(0.25rem-2px)] flex justify-between items-center cursor-pointer"
                    @click="isMinimized = !isMinimized"
                >
                    <h3 class="font-bold text-sm sm:text-base truncate">
                        {{ currentTutorial.title }}
                    </h3>
                    <div class="flex space-x-2">
                        <button 
                            @click.stop="minimizeTutorial"
                            class="text-white hover:text-gray-200"
                            title="Minimizar"
                        >
                            <FontAwesomeIcon :icon="faWindowMinimize" />
                        </button>
                        <button 
                            @click.stop="closeTutorial"
                            class="text-white hover:text-gray-200"
                            title="Cerrar"
                        >
                            <FontAwesomeIcon :icon="faTimes" />
                        </button>
                    </div>
                </div>

                <!-- Contenido (visible cuando no está minimizado) -->
                <div v-if="!isMinimized" class="flex-1 overflow-y-auto p-4">
                    <!-- Barra de progreso -->
                    <div class="mb-4">
                        <div class="flex justify-between mb-1 text-xs sm:text-sm">
                            <span class="font-medium text-[#611232]">Paso {{ currentStep }} de {{ totalSteps }}</span>
                            <span class="font-medium text-[#611232]">{{ Math.round((currentStep/totalSteps)*100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div 
                                class="bg-[#611232] h-2 rounded-full" 
                                :style="`width: ${(currentStep/totalSteps)*100}%`"
                            ></div>
                        </div>
                    </div>

                    <!-- Paso actual -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm sm:text-base">
                        <template v-if="typeof currentTutorial.steps[currentStep - 1] === 'object'">
                            <h4 class="font-bold mb-2">{{ currentTutorial.steps[currentStep - 1].section }}</h4>
                            <p class="mb-2">{{ currentTutorial.steps[currentStep - 1].content }}</p>
                            <ul class="space-y-1">
                                <li 
                                    v-for="(item, index) in currentTutorial.steps[currentStep - 1].items" 
                                    :key="index" 
                                    class="text-sm"
                                    :style="{ listStyleType: getListStyle(item), marginLeft: /^\s{2,}/.test(item) ? '1.5rem' : '1rem' }"
                                >
                                    {{ cleanItemText(item) }}
                                </li>
                            </ul>
                        </template>
                        <template v-else>
                            <p>{{ currentTutorial.steps[currentStep - 1] }}</p>
                        </template>
                    </div>

                    <!-- Controles de navegación -->
                    <div class="flex justify-between mt-auto pt-3 border-t border-gray-200">
                        <button 
                            @click.stop="prevStep"
                            :disabled="currentStep === 1"
                            :class="currentStep === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                            class="px-3 py-1 text-[#611232] font-medium rounded border border-[#611232] text-xs sm:text-sm"
                        >
                            <FontAwesomeIcon :icon="faArrowLeft" class="mr-1" /> Anterior
                        </button>
                        
                        <div class="flex space-x-2">
                            <button 
                                @click.stop="closeTutorial"
                                class="px-3 py-1 text-gray-600 hover:text-[#8A1C4A] font-medium text-xs sm:text-sm"
                            >
                                Saltar
                            </button>
                            <button 
                                @click.stop="nextStep"
                                class="px-3 py-1 bg-[#611232] text-white rounded hover:bg-[#8A1C4A] transition text-xs sm:text-sm"
                            >
                                {{ currentStep === totalSteps ? 'Finalizar' : 'Siguiente' }}
                                <FontAwesomeIcon :icon="faArrowRight" class="ml-1" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Estado minimizado -->
                <div v-else class="flex items-center justify-center h-full px-4">
                    <span class="text-[#611232] font-medium text-sm truncate">Click para expandir tutorial</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@keyframes fade {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade {
    animation: fade 0.5s ease-in-out;
}

button {
    transition: all 0.2s ease;
}

::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

@media (max-width: 640px) {
    .fixed {
        max-width: 95vw;
    }
}

/* Estilos para listas */
ul {
    padding-left: 1rem;
}
li {
    padding-left: 0.5rem;
    text-indent: -0.5rem;
}

/* Estilos para el botón flotante */
.floating-help-btn {
    position: fixed;
    bottom: 1.25rem;
    right: 1.25rem;
    z-index: 9999;
    width: 3rem;
    height: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.floating-help-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Asegura que el modal esté sobre todo */
.modal-z-index {
    z-index: 99999;
}
</style>