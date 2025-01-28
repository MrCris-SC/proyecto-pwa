<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
});

const currentSlide = ref(0);
const totalSlides = 3;
const intervalTime = 3000;
let interval;

const menuOpen = ref(false);

function toggleMenu() {
    menuOpen.value = !menuOpen.value;
}

function nextSlide() {
    currentSlide.value = (currentSlide.value + 1) % totalSlides;
}

function scrollToSection(sectionId) {
    document.getElementById(sectionId)?.scrollIntoView({ behavior: 'smooth' });
}

onMounted(() => {
    interval = setInterval(nextSlide, intervalTime);
});

onBeforeUnmount(() => {
    clearInterval(interval);
});
</script>

<template>
    <!-- Header -->
    <header class="fixed top-0 left-0 w-full bg-[#611232] shadow z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo -->
            <img src="/images/dgeti.png" alt="Logo Sistema DGETI" class="h-12 object-contain">

            <!-- Menú hamburguesa -->
            <button class="md:hidden text-white text-2xl" @click="toggleMenu">☰</button>

            <!-- Navegación -->
            <nav :class="menuOpen ? 'flex' : 'hidden'" class="absolute md:relative top-16 left-0 w-full bg-[#611232] md:flex md:gap-4 md:items-center md:w-auto md:top-0 md:bg-transparent">
                <a href="#" @click.prevent="scrollToSection('inicio')" class="block md:inline-block text-white px-4 py-2 hover:text-red-500">Inicio</a>
                <a href="#" @click.prevent="scrollToSection('acerca')" class="block md:inline-block text-white px-4 py-2 hover:text-red-500">Acerca</a>
                <a href="#" @click.prevent="scrollToSection('caracteristicas')" class="block md:inline-block text-white px-4 py-2 hover:text-red-500">Características</a>
                <a href="#" @click.prevent="scrollToSection('contacto')" class="block md:inline-block text-white px-4 py-2 hover:text-red-500">Contacto</a>

                <!-- Botones -->
                <div class="flex flex-col md:flex-row gap-2 mt-4 md:mt-0">
                    <Link href="/login" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Iniciar Sesión</Link>
                    <Link href="/register" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Registrarse</Link>
                </div>
            </nav>
        </div>
    </header>

    <!-- Slider Section -->
    <section id="inicio" class="relative bg-gray-100 py-20 mt-16">
        <div class="max-w-7xl mx-auto overflow-hidden">
            <div class="flex transition-transform ease-in-out duration-500" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                <div v-for="index in totalSlides" :key="index" class="w-full flex-shrink-0">
                    <img :src="`/images/welcome/Portada${index}.jpg`" :alt="`Slide ${index}`" class="w-full h-72 sm:h-96 object-cover rounded-lg shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Inicio Section -->
    <section id="inicio" class="relative bg-gray-100 py-20 mt-16">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl font-bold text-gray-900">Bienvenido al Sistema DGETI</h1>
            <p class="mt-4 text-lg text-gray-600">Gestión de proyectos para concursos de prototipos y emprendimiento</p>
            <div class="mt-6 flex justify-center gap-4">
                <Link href="/register" class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600">Registrarse</Link>
                <a href="#" @click.prevent="scrollToSection('acerca')" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300">Más información</a>
            </div>
        </div>
    </section>

    <!-- Acerca Section -->
    <section id="acerca" class="py-20 bg-white text-center">
        <h2 class="text-3xl font-bold text-gray-900">Sobre nosotros</h2>
        <p class="mt-4 text-gray-600">DGETI organiza concursos de innovación y prototipos para impulsar el talento estudiantil.</p>
    </section>

    <!-- Características Section -->
    <section id="caracteristicas" class="py-20 bg-gray-50 text-center">
        <h2 class="text-3xl font-bold text-gray-900">Características principales</h2>
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="p-6 bg-white rounded-lg shadow hover:shadow-lg transition">
                <h3 class="text-xl font-bold">Gestión de Proyectos</h3>
                <p class="mt-2 text-gray-600">Administra tus ideas desde un solo lugar.</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow hover:shadow-lg transition">
                <h3 class="text-xl font-bold">Evaluación en Línea</h3>
                <p class="mt-2 text-gray-600">Proceso de evaluación automatizado.</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow hover:shadow-lg transition">
                <h3 class="text-xl font-bold">Reportes en Tiempo Real</h3>
                <p class="mt-2 text-gray-600">Datos actualizados para decisiones rápidas.</p>
            </div>
        </div>
    </section>

    <!-- Contacto Section -->
    <section id="contacto" class="py-20 bg-white text-center">
        <h2 class="text-3xl font-bold text-gray-900">Contáctanos</h2>
        <p class="mt-4 text-gray-600">Para más información, escríbenos al correo oficial: contacto@dgeti.gob.mx</p>
    </section>

    <!-- Footer -->
    <footer class="py-6 bg-gray-900 text-white text-center">
        <p class="text-sm">&copy; 2025 Sistema DGETI. Todos los derechos reservados.</p>
    </footer>
</template>
