<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';

// Props
defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
});

// Estado reactivo
const currentSlide = ref(0);
const totalSlides = 5; // Número de imágenes en el carrusel
const intervalTime = 5000; // Cambiar la imagen cada 5 segundos
let interval;

const menuOpen = ref(false);

// Funciones
function toggleMenu() {
    menuOpen.value = !menuOpen.value;
}

function nextSlide() {
    currentSlide.value = (currentSlide.value + 1) % totalSlides;
}

function prevSlide() {
    currentSlide.value = (currentSlide.value - 1 + totalSlides) % totalSlides;
}

function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    }
}

const daysUntilEvent = ref(0);

// Autenticación
const { props } = usePage();
const isAuthenticated = props.auth.user !== null;

// Efectos
onMounted(() => {
    interval = setInterval(nextSlide, intervalTime);

    const eventDate = new Date('2025-06-01'); // Fecha del evento
    const currentDate = new Date();
    const timeDifference = eventDate - currentDate;
    daysUntilEvent.value = Math.ceil(timeDifference / (1000 * 3600 * 24)); // Convertir milisegundos a días
});

onBeforeUnmount(() => {
    clearInterval(interval);
});

// Lista de imágenes para el carrusel
const images = [
    '/images/welcome/slide1.jpg',
    '/images/welcome/slide2.jpg',
    '/images/welcome/slide3.jpg',
    '/images/welcome/slide4.jpg',
    '/images/welcome/slide5.jpg',
];
</script>

<template>
    <!-- Header -->
    <header class="fixed top-0 left-0 w-full bg-[#611232] shadow-lg z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo -->
            <img src="/images/dgeti.png" alt="Logo Sistema DGETI" class="h-12 object-contain">

            <!-- Botón de menú hamburguesa -->
            <button class="md:hidden text-white text-2xl" @click="toggleMenu">☰</button>

            <!-- Navegación -->
            <nav :class="menuOpen ? 'flex' : 'hidden'" class="absolute md:relative top-16 left-0 w-full bg-[#611232] md:flex md:gap-4 md:items-center md:w-auto md:top-0 md:bg-transparent transition-all duration-300 ease-in-out">
                <!-- Enlaces -->
                <a href="#" @click.prevent="scrollToSection('inicio')" class="block md:inline-block text-white px-4 py-2 hover:text-[#D39D55] transition-colors">Inicio</a>
                <a href="#" @click.prevent="scrollToSection('acerca')" class="block md:inline-block text-white px-4 py-2 hover:text-[#D39D55] transition-colors">Acerca</a>
                <a href="#" @click.prevent="scrollToSection('caracteristicas')" class="block md:inline-block text-white px-4 py-2 hover:text-[#D39D55] transition-colors">Características</a>
                <a href="#" @click.prevent="scrollToSection('contacto')" class="block md:inline-block text-white px-4 py-2 hover:text-[#D39D55] transition-colors">Contacto</a>

                <!-- Botones -->
                <div class="flex flex-col md:flex-row gap-2 mt-4 md:mt-0">
                    <Link v-if="!isAuthenticated" href="/login" class="bg-[#D39D55] text-white px-4 py-2 rounded-lg hover:bg-[#c58d4a] transition-colors">Iniciar Sesión</Link>
                    <Link v-if="!isAuthenticated" href="/register" class="bg-[#D6CFB4] text-[#212121] px-4 py-2 rounded-lg hover:bg-[#c5bda3] transition-colors">Registrarse</Link>
                    <Link v-if="isAuthenticated" href="/dashboard" class="bg-[#D39D55] text-white px-4 py-2 rounded-lg hover:bg-[#c58d4a] transition-colors">Dashboard</Link>
                </div>
            </nav>
        </div>
    </header>

    <!-- Sección de Bienvenida -->
    <section class="relative h-screen flex items-center justify-center text-center bg-gray-900 text-white overflow-hidden">
        <!-- Fondo del Carrusel -->
        <div class="absolute inset-0 z-0">
            <div class="flex transition-transform duration-1000 ease-in-out" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                <div v-for="(image, index) in images" :key="index" class="w-full h-screen flex-shrink-0">
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

    <!-- Slider Section -->
    <section id="acerca" class="bg-[#FFF8E6] text-center">
        <div class="w-full overflow-hidden relative">
            <!-- Flechas de navegación -->
            <button @click="prevSlide" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 p-2 rounded-full hover:bg-opacity-75 z-10 transition-opacity">
                &larr;
            </button>
            <button @click="nextSlide" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 p-2 rounded-full hover:bg-opacity-75 z-10 transition-opacity">
                &rarr;
            </button>

            <!-- Slider -->
            <div class="flex transition-transform ease-in-out duration-500" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                <div v-for="index in totalSlides" :key="index" class="w-full flex-shrink-0">
                    <img :src="`/images/welcome/Portada${index}.jpg`" alt="Slider Image" class="w-full h-auto object-cover max-h-[80vh]" />
                </div>
            </div>
        </div>
    </section>



    <!-- Características Section -->
    <section id="caracteristicas" class="py-20 bg-[#FBFBFB]">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-[#212121] mb-12">Características principales</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-[#FFF8E6] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <h3 class="text-2xl font-bold text-[#611232] mb-4">Gestión de Proyectos</h3>
                    <p class="text-[#212121]">Administra tus ideas desde un solo lugar, con herramientas intuitivas y colaborativas.</p>
                </div>
                <div class="bg-[#FFF8E6] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <h3 class="text-2xl font-bold text-[#611232] mb-4">Evaluación en Línea</h3>
                    <p class="text-[#212121]">Proceso de evaluación automatizado y transparente para garantizar la imparcialidad.</p>
                </div>
                <div class="bg-[#FFF8E6] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <h3 class="text-2xl font-bold text-[#611232] mb-4">Reportes en Tiempo Real</h3>
                    <p class="text-[#212121]">Accede a datos actualizados y métricas clave para tomar decisiones informadas.</p>
                </div>
            </div>
        </div>
    </section>

    
    <!-- Contador de días -->
    <section class="text-center bg-[#611232] py-20">
        <div class="max-w-4xl mx-auto bg-[#FFF8E6] p-8 rounded-lg shadow-lg">
            <h2 class="text-4xl font-bold text-[#212121] mb-4">Faltan</h2>
            <div class="text-8xl font-bold text-[#611232] mb-4">{{ daysUntilEvent }}</div>
            <p class="text-2xl text-[#212121]">días para el evento</p>
        </div>
    </section>


    <!-- Sección de Roles -->
    <section id="roles" class="py-20 bg-[#FFF8E6]">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-[#212121] mb-12">Roles en el Sistema</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-[#FBFBFB] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <img src="/images/icons/admin.png" alt="Admin" class="w-20 h-20 mx-auto mb-4">
                    <h3 class="text-2xl font-bold text-[#611232] mb-4">Administradores</h3>
                    <p class="text-[#212121]">Gestionan usuarios, proyectos y reportes, asegurando el correcto funcionamiento del sistema.</p>
                </div>
                <div class="bg-[#FBFBFB] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <img src="/images/icons/vinculador.png" alt="Vinculador" class="w-20 h-20 mx-auto mb-4">
                    <h3 class="text-2xl font-bold text-[#611232] mb-4">Vinculadores</h3>
                    <p class="text-[#212121]">Se encargan de coordinar la relación con empresas y otros entes para el desarrollo de los proyectos.</p>
                </div>
                <div class="bg-[#FBFBFB] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <img src="/images/icons/participante.png" alt="Participante" class="w-20 h-20 mx-auto mb-4">
                    <h3 class="text-2xl font-bold text-[#611232] mb-4">Participantes</h3>
                    <p class="text-[#212121]">Estudiantes que presentan sus proyectos y compiten en los concursos organizados por la DGETI.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Contacto -->
    <section id="contacto" class="py-20 bg-[#611232] text-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-6">Contáctanos</h2>
            <p class="text-xl mb-8">Estamos aquí para apoyarte en tu camino hacia la innovación.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                <!-- Horario de Atención -->
                <div>
                    <h3 class="text-2xl font-bold mb-4">Horario de Atención</h3>
                    <p class="text-lg">Lunes - Viernes</p>
                    <p class="text-lg">8:00 am a 5:00 pm</p>
                </div>

                <!-- Dirección -->
                <div>
                    <h3 class="text-2xl font-bold mb-4">Nuestra Dirección</h3>
                    <p class="text-lg">Av. Educación Tecnológica 123</p>
                    <p class="text-lg">Ciudad Innovación, 54321</p>
                </div>

                <!-- Teléfonos -->
                <div>
                    <h3 class="text-2xl font-bold mb-4">Contáctanos</h3>
                    <p class="text-lg">+52-800-INNOVAR</p>
                    <p class="text-lg">+52-800-EDUCION</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-6 bg-[#212121] text-white text-center">
        <div class="max-w-7xl mx-auto px-4">
            <p class="text-sm">&copy; 2025 Sistema DGETI. Todos los derechos reservados.</p>
            <div class="mt-4 flex justify-center gap-4">
                <a href="#" class="text-white hover:text-[#D39D55] transition-colors">Sobre Nosotros</a>
                <a href="#" class="text-white hover:text-[#D39D55] transition-colors">Nuestros Servicios</a>
                <a href="#" class="text-white hover:text-[#D39D55] transition-colors">Proyectos</a>
                <a href="#" class="text-white hover:text-[#D39D55] transition-colors">Soporte</a>
            </div>
        </div>
    </footer>
</template>