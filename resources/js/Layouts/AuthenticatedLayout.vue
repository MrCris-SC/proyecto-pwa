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

const tutorialContent = {
    lider: {
        title: 'Guía Rápida para Líderes de Equipo',
        steps: [
            {
                section: 'Registro en Concursos',
                content: 'Proceso para inscribirte y registrar tu equipo:',
                items: [
                    {
                        text: 'Visualiza todas las convocatorias activas en la sección "Concursos"',
                        image: '/public/images/Tutorial/lider1.png'
                    },
                    {
                        text: 'Haz clic en la tarjeta del concurso para registrarte',
                        image: '/public/images/Tutorial/lider3.png'
                    },
                    {
                        text: 'Completa los datos de tu proyecto y equipo',
                        image: '/public/images/Tutorial/lider2.png'
                    }
                ],
                },
            {
                section: 'Gestión de Proyectos',
                content: 'Pasos para completar tu inscripción:',
                items: [
                    {
                        text: '1 Registra a tus asesores (técnico y metodológico)',
                        image: '/public/images/Tutorial/lider4.png'
                    },
                    {
                        text: '2 Descarga los formatos requeridos para completar el registro de tu proyecto:',
                        image: '/public/images/Tutorial/lider5.png'
                    },
                    {
                        text: '3 Llena y sube los documentos en "Documentación Requerida"',
                        image: '/public/images/Tutorial/lider6.png'
                    },
                    {
                        text: '4 Los Documentos opcionales son a consideracion pero de gran ayuda para los evaluadores',
                        image: '/public/images/Tutorial/lider7.png'
                    }
                ],
            },
            {
                section: 'Resultados',
                content: 'Inspecionaras tu evaluacion con los resultados de tu proyecto:',
                items: [
                    {
                        text: 'Seleciona generar reporte y se deacargara un pdf con tus resultados',
                        image: '/public/images/Tutorial/lider8.png'
                    },

                ],
            },
            {
                section: 'Registro de equipos',
                content: 'Informacion de equipos registrados en el concurso',
                items: [
                    {
                        text: 'Visualiza los equipos registrados en el concurso',
                        image: '/public/images/Tutorial/lider9.png'
                    }
                ]
            }
        ]
    },
    admin: {
        title: 'Guía para Administradores del Sistema',
        steps: [
            {
                section: 'Gestión de Usuarios',
                content: 'Funcionalidades disponibles en el panel de administración de usuarios:',
                items: [
                    {
                        text: 'Creación de usuarios y asignación de roles (administrador, evaluador, vinculador, etc.)',
                        image: '/public/images/Tutorial/Admin.png',
                        note: 'Verifica que el correo electrónico sea válido para el envío de credenciales'
                    },
                    {
                        text: 'Edición de perfiles: actualización de datos básicos y ajuste de permisos',
                        image: '/public/images/Tutorial/Admin3.png'
                    },
                    {
                        text: 'Visualización y filtrado del listado completo de usuarios registrados',
                        image: '/public/images/Tutorial/Admin5.png',
                    }
                ]
            },
            {
                section: 'Configuración de Concursos',
                content: 'Proceso completo para la creación y gestión de concursos:',
                items: [
                    {
                        text: '1. Definición de fechas clave (inicio, evaluación y cierre) y etapas del concurso:',
                        subitems: [
                            'Accede al formulario mediante el botón (+) en la sección de Concursos',
                            'Completa todos los campos obligatorios marcados con *'
                        ],
                        image: '/images/Tutorial/Admin6.png'
                    },
                    {
                        text: '2. Procedimiento de registro completo:',
                        subitems: [
                            'Llena cuidadosamente todos los campos del formulario',
                            'Verifica la información antes de guardar',
                            'Confirma la creación con el botón "Crear Concurso"',
                            'Espera la notificación de confirmación'
                        ],
                        image: '/images/Tutorial/Admin7.png',
                        note: 'Asegúrate de que las fechas sean correctas para evitar problemas posteriores'
                    },
                    {
                        text: '3. Opciones post-registro:',
                        subitems: [
                            'Visualización del podio de participantes',
                            'Edición de datos básicos del concurso',
                            'Asignación de evaluadores a proyectos',
                            'Eliminación completa del concurso (acción irreversible)'
                        ],
                        image: '/images/Tutorial/Admin8.png',
                        warning: 'La eliminación del concurso borrará todos los datos asociados permanentemente'
                    }
                ]
            },
            {
                section: 'Criterios de Evaluación',
                content: 'Configuración detallada de los parámetros de calificación:',
                items: [
                    {
                        text: '1. Selección inicial:',
                        subitems: [
                            'Elige el concurso al que agregarás criterios',
                            'Selecciona la modalidad donde iniciara el registro de criterios'
                        ],
                        image: '/images/Tutorial/Admin9.png'
                    },
                    {
                        text: '2. Distribución de puntajes:',
                        subitems: [
                            'Asigna valores porcentuales a cada categoría',
                            'El total debe sumar exactamente 100%'
                        ],
                        image: '/images/Tutorial/Admin10.png',
                    },
                    {
                        text: '3. Registro detallado de criterios:',
                        subitems: [
                            'Ingresa nombre y descripción para cada criterio',
                            'Agrega con el botón "Agregar Criterio"',
                            'Repite el proceso para todas las modalidades'
                        ],
                        image: '/images/Tutorial/Admin11.png',
                        note: 'Los criterios deben ser claros y medibles para los evaluadores'
                    },
                    {
                        text: '4. Finalización del proceso:',
                        subitems: [
                            'Revisa minuciosamente todos los criterios',
                            'Confirma con "Guardar Todos los Criterios"',
                            'El sistema bloqueará ediciones posteriores'
                        ],
                        warning: 'Esta acción es irreversible - no podrás modificar los criterios después de guardar',
                    }
                ]
            }
        ]
    },
    evaluador: {
        title: 'Guía Completa para Evaluadores',
        steps: [
            {
                section: 'Proceso de Evaluación',
                content: 'Siga estos pasos meticulosamente para evaluar proyectos:',
                items: [
                    {
                        text: '1. Inscripción y preparación:',
                        subitems: [
                            {
                                text: 'Acceda a la sección "Concursos" y seleccione el concurso a evaluar',
                                image: '/images/Tutorial/eva1.png'
                            },
                            {
                                text: 'Confirme su participación como evaluador cuando el sistema lo solicite',
                                image: '/images/Tutorial/eva2.png'
                            },
                            {
                                text: 'Revise la documentación de los equipos en la carpeta de archivos antes del día del concurso',
                                text: 'Evalúe el informe técnico (i.informe) con anticipación y guarde su progreso',
                                image: '/images/Tutorial/eval.png',//agregar despues de que se agrege esta parte del proyecto
                                note: 'Recomendamos revisar el tutorial de evaluación formal antes de calificar los informes de los equipos',
                                note: 'Guarde cada avance usando el botón específico para cada sección',
                            },
                        ],
                    },
                    {
                        text: '2. Evaluación formal:',
                        subitems: [
                            {
                                text: 'En la sección "Evaluación", consulte los proyectos asignados',
                                text: 'Seleccione un proyecto para evaluar y revise sus detalles completos',
                                image: '/images/Tutorial/eva3.png'
                            },
                            {
                                text: 'Complete los tres tipos de criterios en orden:',
                                subitems: [
                                    {
                                        image: '/images/Tutorial/eva4.png',
                                        text: 'a) Informe '
                                    },
                                    {
                                        text: 'b) Modalidad',
                                    },
                                    {
                                        text: 'c) Exposición',
                                    }
                                ]

                            },
                            {
                                text: 'Guarde cada sección individualmente antes de continuar',
                                image: '/images/Tutorial/eva6.png',
                                warning: 'No avance sin guardar cada sección'
                            },
                            {
                                text: 'Agregue comentarios constructivos de retroalimentación',
                                image: '/images/Tutorial/eva7.png'
                            },
                            {
                                text: 'Finalice con "Enviar Evaluación Final Cuando los tres tipos de criterio esten completos"',
                                image: '/images/Tutorial/eva8.png',
                                image: '/images/Tutorial/eva9.png',
                                warning: 'Esta acción no puede deshacerse - verifique todo antes de enviar'
                            },
                            {
                                text: 'Comprueve que aparesca Evaluacion completa',
                                image: '/images/Tutorial/eva10.png'
                            },
                        ]
                    }
                ]
            },
            {
                section: 'Gestión de Proyectos Asignados',
                content: 'Acciones disponibles para los proyectos bajo su evaluación:',
                items: [
                    {
                        text: 'Visualización y descarga de documentación:',
                        subitems: [
                            {
                                text: 'Consulte el listado completo de proyectos asignados',
                                image: '/images/Tutorial/evaluador-paso3-item1.png'
                            },
                            {
                                text: 'Descargue la carpeta completa de documentos de cada equipo',
                                image: '/images/Tutorial/evaluador-paso3-item2.png'
                            }
                        ]
                    }
                ]
            },
            {
                section: 'Criterios de Evaluación',
                content: 'Estructura detallada de los parámetros de calificación:',
                items: [
                    {
                        text: 'Consulta de rúbricas:',
                        subitems: [
                            {
                                text: 'Revise los criterios específicos para cada modalidad',
                                image: '/images/Tutorial/eva11.png'
                            }
                        ],
                        note: 'Los criterios varían según la modalidad del proyecto'
                    }
                ]
            },
            {
                section: 'Reportes y Resultados',
                content: 'Seguimiento de sus evaluaciones:',
                items: [
                    {
                        text: 'Consulta de evaluaciones completadas:',
                        subitems: [
                            {
                                text: 'Revise el estado de todos los proyectos asignados',
                                text: 'Consulte puntuaciones finales y comentarios',
                                text: 'Identifique proyectos pendientes de evaluación',
                                text: 'Identifique proyectos pendientes de evaluación',
                                image: '/images/Tutorial/eva12.png',
                                image: '/images/Tutorial/eva13.png'

                            }
                        ]
                    }
                ]
            }
        ]
    },
    vinculador: {
        title: 'Guía para Vinculadores',
        steps: [
            {
                section: 'Gestión de Líderes',
                content: 'Funciones principales del vinculador:',
                items: [
                    {
                        text: 'Registro y seguimiento de líderes',
                        image: '/images/Tutorial/vinculador-paso1-item1.jpg'
                    },
                    {
                        text: 'Asignación de proyectos a concursos',
                        image: '/images/Tutorial/vinculador-paso1-item2.jpg'
                    }
                ],
                image: '/images/Tutorial/vinculador-paso1-main.jpg'
            },
            {
                section: 'Soporte a Equipos',
                content: 'Cómo apoyar a los participantes:',
                items: [
                    {
                        text: 'Resolución de dudas y problemas',
                        image: '/images/Tutorial/vinculador-paso2-item1.jpg'
                    },
                    {
                        text: 'Verificación de documentación',
                        image: '/images/Tutorial/vinculador-paso2-item2.jpg'
                    }
                ],
                image: '/images/Tutorial/vinculador-paso2-main.jpg'
            }
        ]
    },
    default: {
        title: 'Guía General de Usuario',
        steps: [
            {
                content: '1 Completa tu perfil para acceso completo',
                image: '/images/Tutorial/default-paso1.jpg'
            },
            {
                content: '2 Explora las secciones disponibles según tu rol',
                image: '/images/Tutorial/default-paso2.jpg'
            },
            {
                content: '3 Consulta esta guía cuando necesites ayuda',
                image: '/images/Tutorial/default-paso3.jpg'
            }
        ]
    }
};

const currentTutorial = computed(() => tutorialContent[userRole.value] || tutorialContent.default);
const totalSteps = computed(() => currentTutorial.value.steps.length);

// Funciones auxiliares para manejar items con imágenes
const cleanItemText = (item) => {
    if (typeof item === 'object') return item.text;
    return item.replace(/^(\d+\s|-\s|\s{2,})/, '').trim();
};

const hasItemImage = (item) => {
    return typeof item === 'object' && item.image;
};

const getListStyle = (item) => {
    const text = typeof item === 'object' ? item.text : item;
    if (/^\d+\s/.test(text)) return 'decimal';
    if (/^-\s/.test(text)) return 'disc';
    if (/^\s{2,}/.test(text)) return 'none';
    return 'disc';
};

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

            <!-- Botón flotante de ayuda -->
            <button 
                @click="toggleTutorial"
                class="fixed top-28 right-5 z-50 bg-[#611232] text-white p-3 rounded-full shadow-lg hover:bg-[#8A1C4A] transition-all transform hover:scale-110 flex items-center"
                :class="{ '!bg-[#8A1C4A]': showTutorial && !isMinimized }"
                title="Mostrar/Ocultar tutorial"
            >
                <FontAwesomeIcon :icon="faQuestionCircle" class="text-lg" />
            </button>
        </div>

        <!-- Ventana flotante del tutorial (centrada) -->
        <div 
            v-if="showTutorial && userRole"
            class="tutorial-modal fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
            @click.self="closeTutorial"
        >
            <div 
                class="tutorial-window bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col"
                :class="{ 'minimized': isMinimized }"
            >
                <!-- Barra de título -->
                <div 
                    class="title-bar bg-[#611232] text-white p-4 rounded-t-lg flex justify-between items-center cursor-pointer"
                    @click="isMinimized = !isMinimized"
                >
                    <h3 class="title font-bold text-lg">
                        {{ currentTutorial.title }}
                    </h3>
                    <div class="controls flex gap-2">
                        <button 
                            @click.stop="minimizeTutorial"
                            class="control-button text-white hover:text-gray-200"
                            title="Minimizar"
                        >
                            <FontAwesomeIcon :icon="faWindowMinimize" />
                        </button>
                        <button 
                            @click.stop="closeTutorial"
                            class="control-button text-white hover:text-gray-200"
                            title="Cerrar"
                        >
                            <FontAwesomeIcon :icon="faTimes" />
                        </button>
                    </div>
                </div>

                <!-- Contenido (visible cuando no está minimizado) -->
                <div v-if="!isMinimized" class="content flex-1 overflow-auto p-6">
                    <!-- Barra de progreso -->
                    <div class="progress-bar mb-6">
                        <div class="progress-info flex justify-between mb-1">
                            <span class="progress-text text-[#611232] font-medium">Paso {{ currentStep }} de {{ totalSteps }}</span>
                            <span class="progress-percent text-[#611232] font-medium">{{ Math.round((currentStep/totalSteps)*100) }}%</span>
                        </div>
                        <div class="progress-track w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div 
                                class="progress-fill h-full bg-[#611232] rounded-full transition-all duration-300"
                                :style="`width: ${(currentStep/totalSteps)*100}%`"
                            ></div>
                        </div>
                    </div>

                    <!-- Paso actual -->
                    <div class="step-content bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                        <template v-if="typeof currentTutorial.steps[currentStep - 1] === 'object'">
                            <h4 class="step-title text-xl font-bold text-gray-800 mb-3">{{ currentTutorial.steps[currentStep - 1].section }}</h4>
                            
                            <!-- Imagen principal del paso -->
                            <img 
                                v-if="currentTutorial.steps[currentStep - 1].image" 
                                :src="currentTutorial.steps[currentStep - 1].image" 
                                class="w-full max-h-64 object-contain mb-4 rounded-lg border border-gray-200 mx-auto"
                                :alt="`Imagen ilustrativa: ${currentTutorial.steps[currentStep - 1].section}`"
                            >
                            
                            <p class="step-description text-gray-700 mb-4">{{ currentTutorial.steps[currentStep - 1].content }}</p>
                            <ul class="step-items space-y-4 pl-5">
                                <li 
                                    v-for="(item, index) in currentTutorial.steps[currentStep - 1].items" 
                                    :key="index" 
                                    class="step-item text-gray-700"
                                    :style="{ listStyleType: getListStyle(item), marginLeft: /^\s{2,}/.test(cleanItemText(item)) ? '1.5rem' : '0' }"
                                >
                                    {{ cleanItemText(item) }}
                                    <!-- Imagen específica para este ítem -->
                                    <img 
                                        v-if="hasItemImage(item)" 
                                        :src="item.image" 
                                        class="item-image w-full max-h-48 object-contain mt-2 mb-3 rounded-lg border border-gray-200 mx-auto"
                                        :alt="`Imagen ilustrativa para: ${cleanItemText(item)}`"
                                    >
                                </li>
                            </ul>
                        </template>
                        <template v-else>
                            <!-- Para pasos simples sin sección -->
                            <img 
                                v-if="currentTutorial.steps[currentStep - 1].image" 
                                :src="currentTutorial.steps[currentStep - 1].image" 
                                class="w-full max-h-64 object-contain mb-4 rounded-lg border border-gray-200 mx-auto"
                                alt="Imagen ilustrativa del paso"
                            >
                            <p class="text-gray-700">{{ currentTutorial.steps[currentStep - 1].content || currentTutorial.steps[currentStep - 1] }}</p>
                        </template>
                    </div>

                    <!-- Controles de navegación -->
                    <div class="navigation-controls flex justify-between items-center pt-4 border-t border-gray-200">
                        <button 
                            @click.stop="prevStep"
                            :disabled="currentStep === 1"
                            class="nav-button px-4 py-2 rounded-md font-medium transition-colors"
                            :class="currentStep === 1 ? 'text-gray-400 cursor-not-allowed' : 'text-[#611232] hover:bg-[#611232] hover:text-white border border-[#611232]'"
                        >
                            <FontAwesomeIcon :icon="faArrowLeft" class="mr-2" /> Anterior
                        </button>
                        
                        <div class="main-controls flex gap-4">
                            <button 
                                @click.stop="closeTutorial"
                                class="skip-button px-4 py-2 text-gray-600 hover:text-[#611232] font-medium"
                            >
                                Saltar
                            </button>
                            <button 
                                @click.stop="nextStep"
                                class="next-button px-4 py-2 bg-[#611232] text-white rounded-md font-medium hover:bg-[#8A1C4A] transition-colors"
                            >
                                {{ currentStep === totalSteps ? 'Finalizar' : 'Siguiente' }}
                                <FontAwesomeIcon :icon="faArrowRight" class="ml-2" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Estado minimizado -->
                <div v-else class="minimized-content flex items-center justify-center h-full p-4">
                    <span class="minimized-text text-[#611232] font-medium">Click para expandir tutorial</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Estilos para el modal del tutorial */
.tutorial-modal {
    animation: fadeIn 0.3s ease-out;
}

.tutorial-window {
    animation: slideUp 0.3s ease-out;
    border: 2px solid #611232;
}

/* Animaciones */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0;
        transform: translateY(20px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

/* Estilos para las imágenes */
.step-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.step-item {
    display: flex;
    flex-direction: column;
}

.item-image {
    max-height: 200px;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    border-radius: 0.25rem;
    border: 1px solid #e2e8f0;
    align-self: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Estilos para scrollbar */
::-webkit-scrollbar {
    width: 8px;
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

/* Estilos responsivos */
@media (max-width: 768px) {
    .tutorial-window {
        max-width: 95%;
        max-height: 95vh;
    }
    
    .step-title {
        font-size: 1.1rem;
    }
    
    .step-description, .step-item {
        font-size: 0.95rem;
    }
    
    .navigation-controls {
        flex-direction: column;
        gap: 1rem;
    }
    
    .main-controls {
        width: 100%;
        justify-content: space-between;
    }
    
    .item-image {
        max-height: 150px;
    }
}

@media (max-width: 480px) {
    .content {
        padding: 1rem;
    }
    
    .step-content {
        padding: 1rem;
    }
    
    .nav-button, .skip-button, .next-button {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .item-image {
        max-height: 120px;
    }
}
</style>