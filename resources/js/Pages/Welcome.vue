<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';

// Props
defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
});

// Variables para el carrusel de bienvenida
const currentSlideWelcome = ref(0);
const imagesWelcome = [
    '/images/welcome/slide1.jpg',
    '/images/welcome/slide2.jpg',
    '/images/welcome/slide3.jpg',
    '/images/welcome/slide4.jpg',
    '/images/welcome/slide5.jpg',
];

// Variables para el slider de la sección "acerca"
const currentSlideAbout = ref(0);
const imagesAbout = [
    '/images/welcome/Portada1.jpg',
    '/images/welcome/Portada2.jpg',
    '/images/welcome/Portada3.jpg',
    '/images/welcome/Portada4.jpg',
];

const intervalTime = 5000; // Cambiar la imagen cada 5 segundos
let intervalWelcome;
let intervalAbout;

// Estado del menú móvil
const menuOpen = ref(false);

// Estado de visibilidad del header
const isHeaderVisible = ref(true);
const lastScrollPosition = ref(0);

// Función para alternar el menú móvil
const toggleMenu = () => {
    menuOpen.value = !menuOpen.value;
};

// Función para manejar el scroll y ocultar/mostrar el header
const handleScroll = () => {
    const currentScrollPosition = window.pageYOffset || document.documentElement.scrollTop;

    if (currentScrollPosition < 0) {
        return;
    }

    // Detectar la dirección del scroll
    if (Math.abs(currentScrollPosition - lastScrollPosition.value) < 60) {
        return;
    }

    // Mostrar u ocultar el header según la dirección del scroll
    isHeaderVisible.value = currentScrollPosition < lastScrollPosition.value;
    lastScrollPosition.value = currentScrollPosition;
};

// Funciones para el carrusel de bienvenida
const nextSlideWelcome = () => {
    currentSlideWelcome.value = (currentSlideWelcome.value + 1) % imagesWelcome.length;
};

const prevSlideWelcome = () => {
    currentSlideWelcome.value = (currentSlideWelcome.value - 1 + imagesWelcome.length) % imagesWelcome.length;
};

// Funciones para el slider de la sección "acerca"
const nextSlideAbout = () => {
    currentSlideAbout.value = (currentSlideAbout.value + 1) % imagesAbout.length;
};

const prevSlideAbout = () => {
    currentSlideAbout.value = (currentSlideAbout.value - 1 + imagesAbout.length) % imagesAbout.length;
};

const scrollToSection = (sectionId) => {
    document.getElementById(sectionId)?.scrollIntoView({ behavior: 'smooth' });
};

// Variables para el contador
const days = ref(0);
const hours = ref(0);
const minutes = ref(0);
const seconds = ref(0);

const { props } = usePage();

// Usar computed para isAuthenticated
const isAuthenticated = computed(() => props.auth?.user !== null);

// Recargar la página después de iniciar sesión
if (isAuthenticated.value) {
    router.reload(); // Recarga la página para asegurar que los datos se actualicen
}

// Función para actualizar el contador
const updateCountdown = () => {
    const eventDate = new Date('2025-05-30').getTime(); // Fecha del evento
    const now = new Date().getTime();
    const timeLeft = eventDate - now;

    if (timeLeft > 0) {
        days.value = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        hours.value = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        minutes.value = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        seconds.value = Math.floor((timeLeft % (1000 * 60)) / 1000);
    } else {
        // Si el evento ya pasó
        days.value = 0;
        hours.value = 0;
        minutes.value = 0;
        seconds.value = 0;
    }
};

onMounted(() => {
    if (isAuthenticated.value) {
        // Redirige automáticamente al dashboard si ya está autenticado
        router.push('/dashboard');
    } else {
        // Iniciar el carrusel de bienvenida
        intervalWelcome = setInterval(nextSlideWelcome, intervalTime);

        // Iniciar el slider de la sección "acerca"
        intervalAbout = setInterval(nextSlideAbout, intervalTime);

        // Iniciar el contador
        updateCountdown();
        setInterval(updateCountdown, 1000); // Actualizar cada segundo
    }

    // Event listener para el scroll
    window.addEventListener('scroll', handleScroll);
});

onBeforeUnmount(() => {
    clearInterval(intervalWelcome);
    clearInterval(intervalAbout);
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>

<!-- Header -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <header
        :class="{
            'fixed top-0 left-0 w-full bg-[#611232] shadow z-50 transition-transform duration-300': true,
            'transform -translate-y-full': !isHeaderVisible,
        }"
    >
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo ajustable -->
            <img src="/images/dgeti.png" alt="Logo Sistema DGETI" class="h-10 md:h-12 object-contain" />

            <!-- Botón de menú hamburguesa -->
            <button
                class="md:hidden text-white text-2xl focus:outline-none"
                @click="toggleMenu"
                aria-label="Abrir menú"
            >
                ☰
            </button>

            <!-- Navegación -->
            <nav
                :class="{
                    'absolute md:relative top-16 left-0 w-full bg-[#611232] md:flex md:gap-4 md:items-center md:w-auto md:top-0 md:bg-transparent transition-all duration-300 ease-in-out': true,
                    'flex flex-col items-center': menuOpen,
                    'hidden': !menuOpen,
                }"
            >
                <!-- Enlaces -->
                <a
                    href="#"
                    @click.prevent="scrollToSection('inicio')"
                    class="block md:inline-block text-white px-4 py-2 hover:text-red-500 transition-colors duration-200"
                    >Inicio</a
                >
                <a
                    href="#"
                    @click.prevent="scrollToSection('acerca')"
                    class="block md:inline-block text-white px-4 py-2 hover:text-red-500 transition-colors duration-200"
                    >Acerca</a
                >
                <a
                    href="#"
                    @click.prevent="scrollToSection('caracteristicas')"
                    class="block md:inline-block text-white px-4 py-2 hover:text-red-500 transition-colors duration-200"
                    >Características</a
                >
                <a
                    href="#"
                    @click.prevent="scrollToSection('contacto')"
                    class="block md:inline-block text-white px-4 py-2 hover:text-red-500 transition-colors duration-200"
                    >Contacto</a
                >

                <!-- Botones ajustables para móviles -->
                <div class="flex flex-col md:flex-row gap-2 mt-4 md:mt-0">
                    <Link
                        v-if="!isAuthenticated"
                        href="/login"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors duration-200 text-center"
                        >Iniciar Sesión</Link
                    >
                    <Link
                        v-if="!isAuthenticated"
                        href="/register"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-200 text-center"
                        >Registrarse</Link
                    >
                    <Link
                        v-if="isAuthenticated"
                        href="/dashboard"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors duration-200 text-center"
                        >Dashboard</Link
                    >
                </div>
            </nav>
        </div>
    </header>

    <style>
        .nav-enter-active,
        .nav-leave-active {
            transition: opacity 0.3s, transform 0.3s;
        }

        .nav-enter-from,
        .nav-leave-to {
            opacity: 0;
            transform: translateY(-10px);
        }

        /* Medida para pantallas pequeñas */
        @media (max-width: 768px) {
            .flex-col {
                flex-direction: column;
            }
            .text-center {
                text-align: center;
            }
            .px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .py-2 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
        }
    </style>

    <section id="inicio" class="relative h-screen flex items-center justify-center text-center bg-gray-900 text-white overflow-hidden mt-16">
    <!-- Fondo del Carrusel -->
    <div class="absolute inset-0 z-0">
        <div class="flex transition-transform duration-1000 ease-in-out" :style="{ transform: `translateX(-${currentSlideWelcome * 100}%)` }">
            <div v-for="(image, index) in imagesWelcome" :key="index" class="w-full h-screen flex-shrink-0">
                <img :src="image" :alt="`Slide ${index + 1}`" class="w-full h-full object-cover opacity-50" />
            </div>
        </div>
    </div>

    <!-- Contenido de Bienvenida -->
    <div class="relative z-10 max-w-4xl px-4">
        <h1 class="text-5xl md:text-6xl font-bold mb-6">Bienvenido al Sistema DGETI</h1>
        <p class="text-xl md:text-2xl mb-8">
            Gestión de proyectos para concursos de prototipos y emprendimiento.
        </p>
        <div class="flex justify-center gap-4">
            <Link v-if="!isAuthenticated" href="/register" class="bg-[#D39D55] text-white px-8 py-3 rounded-lg hover:bg-[#c58d4a] transition-colors">Registrarse</Link>
            <a href="#" @click.prevent="scrollToSection('acerca')" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg hover:bg-white hover:text-[#611232] transition-colors">Más información</a>
        </div>
        <p class="mt-6 text-lg text-gray-300">#DGETI</p>
    </div>
</section>

    <!-- Sección DGETI Mejorada -->
    <section id="acerca" class="bg-[#FFF8E6] py-16">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <!-- Título destacado con sombra y animación sutil -->
            <h2 class="text-4xl md:text-5xl font-bold text-[#611232] mb-8 animate-fade-in">
                ¿Qué es la <span class="text-[#8A1C4A]">DGETI</span>?
            </h2>

            <!-- Contenedor de información con fondo y sombra -->
            <div class="bg-white p-8 md:p-12 rounded-lg shadow-lg transform hover:scale-102 transition-transform duration-300">
                <!-- Información clave con mejor tipografía y espaciado -->
                <p class="text-lg md:text-xl text-[#555] leading-relaxed max-w-3xl mx-auto">
                    La <span class="font-semibold text-[#611232]">Dirección General de Educación Tecnológica Industrial y de Servicios</span> (DGETI) es una dependencia adscrita a la Subsecretaría de Educación Media Superior (SEMS), dependiente de la Secretaría de Educación Pública (SEP). Ofrece el servicio educativo del nivel medio superior tecnológico, formando a jóvenes con habilidades técnicas y profesionales para el futuro.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-[#FFF8E6] text-center">
        <div class="w-full overflow-hidden relative">
            <!-- Flechas de navegación -->
            <button @click="prevSlideAbout" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 p-2 rounded-full hover:bg-opacity-75 z-10 transition-opacity">
                &larr;
            </button>
            <button @click="nextSlideAbout" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 p-2 rounded-full hover:bg-opacity-75 z-10 transition-opacity">
                &rarr;
            </button>

            <!-- Slider -->
            <div class="flex transition-transform ease-in-out duration-500" :style="{ transform: `translateX(-${currentSlideAbout * 100}%)` }">
                <div v-for="(image, index) in imagesAbout" :key="index" class="w-full flex-shrink-0">
                    <img :src="image" :alt="`Slide ${index + 1}`" class="w-full h-auto object-cover max-h-[80vh]" />
                </div>
            </div>
        </div>
    </section>

    <!-- Características Section - Mejorada -->
    <section id="caracteristicas" class="py-20 bg-[#FBFBFB] overflow-hidden relative">
        <!-- Fondo decorativo -->
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <!-- Título -->
            <h2 class="text-4xl font-bold text-center text-[#212121] mb-12 animate-fade-in">
                Características principales
            </h2>
            <!-- Grid de tarjetas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Tarjeta 1 -->
                <div class="bg-[#FFF8E6] p-8 rounded-lg shadow-lg hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 relative overflow-hidden group animate-fade-in-up">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#611232] to-[#FF6B6B] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <!-- Ícono -->
                    <svg class="w-12 h-12 text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500">
                        Registro Centralizado
                    </h3>
                    <p class="text-[#212121] relative z-10 group-hover:text-white transition-colors duration-500">
                        Permite el registro de participantes y proyectos en una plataforma única, facilitando la gestión de la información.
                    </p>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-[#611232] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                </div>

                <!-- Tarjeta 2 -->
                <div class="bg-[#FFF8E6] p-8 rounded-lg shadow-lg hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 relative overflow-hidden group animate-fade-in-up delay-1">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#611232] to-[#FF6B6B] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <!-- Ícono -->
                    <svg class="w-12 h-12 text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500">
                        Evaluación Automatizada
                    </h3>
                    <p class="text-[#212121] relative z-10 group-hover:text-white transition-colors duration-500">
                        Incorpora rúbricas configurables para la evaluación de proyectos, asegurando criterios uniformes y minimizando errores.
                    </p>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-[#611232] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                </div>

                <!-- Tarjeta 3 -->
                <div class="bg-[#FFF8E6] p-8 rounded-lg shadow-lg hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 relative overflow-hidden group animate-fade-in-up delay-2">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#611232] to-[#FF6B6B] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <!-- Ícono -->
                    <svg class="w-12 h-12 text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500">
                        Generación de Reportes
                    </h3>
                    <p class="text-[#212121] relative z-10 group-hover:text-white transition-colors duration-500">
                        Genera reportes detallados y constancias automáticas para cada fase del concurso (local, estatal y nacional).
                    </p>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-[#611232] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                </div>

                <!-- Tarjeta 4 -->
                <div class="bg-[#FFF8E6] p-8 rounded-lg shadow-lg hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 relative overflow-hidden group animate-fade-in-up delay-3">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#611232] to-[#FF6B6B] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <!-- Ícono -->
                    <svg class="w-12 h-12 text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500">
                        Interacción entre Perfiles
                    </h3>
                    <p class="text-[#212121] relative z-10 group-hover:text-white transition-colors duration-500">
                        Facilita la comunicación y colaboración entre administradores, vinculadores, participantes, asesores y jurados.
                    </p>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-[#611232] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                </div>

                <!-- Tarjeta 5 -->
                <div class="bg-[#FFF8E6] p-8 rounded-lg shadow-lg hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 relative overflow-hidden group animate-fade-in-up delay-4">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#611232] to-[#FF6B6B] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <!-- Ícono -->
                    <svg class="w-12 h-12 text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500">
                        Transparencia y Equidad
                    </h3>
                    <p class="text-[#212121] relative z-10 group-hover:text-white transition-colors duration-500">
                        Garantiza la transparencia en los resultados y la equidad en la evaluación mediante herramientas digitales.
                    </p>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-[#611232] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                </div>

                <!-- Tarjeta 6 -->
                <div class="bg-[#FFF8E6] p-8 rounded-lg shadow-lg hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 relative overflow-hidden group animate-fade-in-up delay-5">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#611232] to-[#FF6B6B] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <!-- Ícono -->
                    <svg class="w-12 h-12 text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-[#611232] mb-4 relative z-10 group-hover:text-white transition-colors duration-500">
                        Plataforma Intuitiva
                    </h3>
                    <p class="text-[#212121] relative z-10 group-hover:text-white transition-colors duration-500">
                        Diseñada para ser fácil de usar, accesible y eficiente, mejorando la experiencia de todos los usuarios.
                    </p>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-[#611232] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                </div>
            </div>

        </div>
    </section>

    <!-- Animaciones personalizadas -->
    <style>
        @keyframes fade-in {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 1s ease-out;
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }
        .delay-4 { animation-delay: 0.8s; }
        .delay-5 { animation-delay: 1s; }
    </style>
        
    <!-- Contador de días -->
    <section class="text-center bg-[#611232] py-20">
        <div class="max-w-4xl mx-auto bg-[#FFF8E6] p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
            <h2 class="text-4xl font-bold text-[#212121] mb-8 animate-pulse">Faltan</h2>
            <div class="flex justify-center items-center space-x-6">
                <!-- Días -->
                <div class="text-center">
                    <div class="text-8xl font-bold text-[#611232] mb-2 animate-bounce">{{ String(days).padStart(2, '0') }}</div>
                    <p class="text-2xl text-[#212121] font-semibold">Días</p>
                </div>
                
                <!-- Separador -->
                <div class="text-6xl font-bold text-[#611232]">:</div>
                
                <!-- Horas -->
                <div class="text-center">
                    <div class="text-8xl font-bold text-[#611232] mb-2 animate-bounce">{{ String(hours).padStart(2, '0') }}</div>
                    <p class="text-2xl text-[#212121] font-semibold">Horas</p>
                </div>
                
                <!-- Separador -->
                <div class="text-6xl font-bold text-[#611232]">:</div>
                
                <!-- Minutos -->
                <div class="text-center">
                    <div class="text-8xl font-bold text-[#611232] mb-2 animate-bounce">{{ String(minutes).padStart(2, '0') }}</div>
                    <p class="text-2xl text-[#212121] font-semibold">Minutos</p>
                </div>
                
                <!-- Separador -->
                <div class="text-6xl font-bold text-[#611232]">:</div>
                
                <!-- Segundos -->
                <div class="text-center">
                    <div class="text-8xl font-bold text-[#611232] mb-2 animate-bounce">{{ String(seconds).padStart(2, '0') }}</div>
                    <p class="text-2xl text-[#212121] font-semibold">Segundos</p>
                </div>
            </div>
            <p class="text-2xl text-[#212121] mt-8">para el gran evento</p>
        </div>
    </section>

    <!-- Sección de Roles -->
    <section id="roles" class="py-20 bg-[#FFF8E6]">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-[#212121] mb-12">Roles en el Sistema</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Rol de Administrador -->
                <div class="bg-[#FBFBFB] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <i class="fas fa-user-cog text-6xl text-[#611232] mx-auto mb-4"></i>
                    <h3 class="text-2xl font-bold text-[#611232] mb-4">Administradores</h3>
                    <p class="text-[#212121]">Gestionan usuarios, proyectos y reportes, asegurando el correcto funcionamiento del sistema y la transparencia en todas las fases del concurso.</p>
                </div>
                <!-- Rol de Vinculador -->
                <div class="bg-[#FBFBFB] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <i class="fas fa-handshake text-6xl text-[#611232] mx-auto mb-4"></i>
                    <h3 class="text-2xl font-bold text-[#611232] mb-4">Vinculadores</h3>
                    <p class="text-[#212121]">Coordinan la relación con empresas, instituciones y otros entes externos para facilitar recursos y apoyo a los proyectos participantes.</p>
                </div>
                <!-- Rol de Participante -->
                <div class="bg-[#FBFBFB] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <i class="fas fa-users text-6xl text-[#611232] mx-auto mb-4"></i>
                    <h3 class="text-2xl font-bold text-[#611232] mb-4">Participantes</h3>
                    <p class="text-[#212121]">Estudiantes que presentan sus proyectos innovadores y compiten en las fases local, estatal y nacional del concurso de emprendimiento.</p>
                </div>
                <!-- Rol de Asesor -->
                <div class="bg-[#FBFBFB] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <i class="fas fa-chalkboard-teacher text-6xl text-[#611232] mx-auto mb-4"></i>
                    <h3 class="text-2xl font-bold text-[#611232] mb-4">Asesores</h3>
                    <p class="text-[#212121]">Brindan orientación y apoyo técnico a los participantes durante el desarrollo y presentación de sus proyectos.</p>
                </div>
                <!-- Rol de Jurado -->
                <div class="bg-[#FBFBFB] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <i class="fas fa-gavel text-6xl text-[#611232] mx-auto mb-4"></i>
                    <h3 class="text-2xl font-bold text-[#611232] mb-4">Jurado</h3>
                    <p class="text-[#212121]">Evalúan los proyectos participantes con base en criterios predefinidos, garantizando la equidad y transparencia en los resultados.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Contacto -->
    <section id="contacto" class="py-20 bg-[#611232] text-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-6 animate-fade-in-up">Contáctanos</h2>
            <p class="text-xl mb-8 animate-fade-in-up delay-100">Estamos aquí para apoyarte en tu camino hacia la innovación.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left animate-fade-in-up delay-200">
                <!-- Horario de Atención -->
                <div class="transform transition-transform duration-300 hover:scale-105">
                    <div class="flex items-center mb-4">
                        <!-- Icono de Reloj -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock mr-2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <h3 class="text-2xl font-bold">Horario de Atención</h3>
                    </div>
                    <p class="text-lg">Lunes - Viernes</p>
                    <p class="text-lg">8:00 am a 5:00 pm</p>
                </div>

                <!-- Dirección -->
                <div class="transform transition-transform duration-300 hover:scale-105">
                    <div class="flex items-center mb-4">
                        <!-- Icono de Mapa -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin mr-2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <h3 class="text-2xl font-bold">Nuestra Dirección</h3>
                    </div>
                    <p class="text-lg">Av. Educación Tecnológica 123</p>
                    <p class="text-lg">Ciudad Innovación, 54321</p>
                </div>

                <!-- Teléfonos -->
                <div class="transform transition-transform duration-300 hover:scale-105">
                    <div class="flex items-center mb-4">
                        <!-- Icono de Teléfono -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone mr-2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <h3 class="text-2xl font-bold">Contáctanos</h3>
                    </div>
                    <p class="text-lg">+52-800-INNOVAR</p>
                    <p class="text-lg">+52-800-EDUCACION</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Animaciones personalizadas -->
    <style>
        @keyframes fade-in-up {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }
    </style>

    <!-- Footer -->
    <footer class="bg-[#212121] text-white text-center py-8">
        <div class="max-w-7xl mx-auto px-6">
            <!-- Derechos de autor -->
            <p class="text-sm text-gray-400">&copy; 2025 Sistema DGETI. Todos los derechos reservados.</p>

            <!-- Enlaces -->
            <div class="mt-6 flex justify-center gap-8">
                <a href="#" @click.prevent="scrollToSection('inicio')" class="text-white hover:text-red-500 transition-colors duration-200">Inicio</a>
                <a href="#" @click.prevent="scrollToSection('acerca')" class="text-white hover:text-red-500 transition-colors duration-200">Acerca</a>
                <a href="#" @click.prevent="scrollToSection('caracteristicas')" class="text-white hover:text-red-500 transition-colors duration-200">Características</a>
                <a href="#" @click.prevent="scrollToSection('contacto')" class="text-white hover:text-red-500 transition-colors duration-200">Contacto</a>
            </div>

            <!-- Redes sociales (opcional) -->
            <div class="mt-4 flex justify-center gap-6">
                <a href="#" class="text-white hover:text-red-500 transition-colors duration-200"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white hover:text-red-500 transition-colors duration-200"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white hover:text-red-500 transition-colors duration-200"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </footer>
</template>