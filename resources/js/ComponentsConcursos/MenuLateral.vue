//D:\proyecto\proyecto-pwa\resources\js\ComponentsConcursos\MenuLateral.vue
<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3'; // Importación necesaria para manejar rutas

const { props } = usePage();
const emit = defineEmits(['menu-selected']);
const selectedMenu = ref('Concursos');
const isMenuMinimized = ref(false); // Estado para controlar la minimización del menú
import '@fortawesome/fontawesome-free/css/all.min.css';

const menuItems = ref([]);

if (props.auth.user.rol === 'lider') {
    menuItems.value = [
        { name: 'Concursos', icon: 'fas fa-file-alt' },
        { name: 'Gestión de proyectos', icon: 'fas fa-tasks' },
        { name: 'Proceso', icon: 'fas fa-cogs' },
        { name: 'Resultados', icon: 'fas fa-chart-line' },
        { name: 'Equipos Registrados', icon: 'fas fa-users' }
    ];
} else if (props.auth.user.rol === 'admin') {
    menuItems.value = [
        { name: 'Usuarios', icon: 'fas fa-users-cog' },
        { name: 'Concursos', icon: 'fas fa-trophy' },
        { name: 'Configuración', icon: 'fas fa-cog' },
        { name: 'Reportes', icon: 'fas fa-chart-pie' }
    ];
} else if (props.auth.user.rol === 'evaluador') {
    menuItems.value = [
        { name: 'Evaluación', icon: 'fas fa-clipboard-check' },
        { name: 'Proyectos Asignados', icon: 'fas fa-list-ul' },
        { name: 'Criterios', icon: 'fas fa-check-square' },
        { name: 'Reportes', icon: 'fas fa-chart-bar' },
        { name: 'Perfil', icon: 'fas fa-user' }
    ];
} else {
    menuItems.value = [
        { name: 'Inicio', icon: 'fas fa-home' },
        { name: 'Perfil', icon: 'fas fa-user' },
        { name: 'Ayuda', icon: 'fas fa-question-circle' }
    ];
}

const selectMenu = (item) => {
    selectedMenu.value = item.name.toLowerCase(); // Convierte el nombre a minúsculas
    emit('menu-selected', selectedMenu.value); // Emite el evento
    handleMenuSelected(selectedMenu.value); // Llama a la función para manejar la selección del menú
};

const toggleMenu = () => {
    isMenuMinimized.value = !isMenuMinimized.value;
};

const handleMenuSelected = (menu) => {
    if (menu === 'evaluación') {
        router.get(route('evaluacion.index')); // Redirige a Evaluación
    } else if (menu === 'proyectos asignados') {
        router.get(route('proyectos.asignados')); // Redirige a Proyectos Asignados
    } else if (menu === 'criterios') {
        router.get(route('criterios.index')); // Redirige a Criterios
    } else if (menu === 'reportes') {
        router.get(route('reportes.index')); // Redirige a Reportes
    } else if (menu === 'perfil') {
        router.get(route('perfil.index')); // Redirige a Perfil
    }
};

</script>

<template>
    <aside 
        class="fixed lg:relative bottom-0 left-0 right-0 w-full lg:w-64 bg-[#611232] text-white shadow-lg rounded-t-lg lg:rounded-lg p-3 lg:p-5 transition-all duration-300 ease-in-out z-50">

        <!-- Contenedor del menú -->
        <ul class="flex lg:flex-col justify-around lg:justify-start space-x-4 lg:space-x-0 lg:space-y-3">
            <li 
                v-for="item in menuItems" 
                :key="item.name"
                class="p-3 rounded-lg cursor-pointer flex flex-col lg:flex-row items-center text-center transition duration-200 ease-in-out relative group"
                :class="selectedMenu === item.name.toLowerCase() ? 'bg-[#8A1C4A] text-white shadow-md' : 'hover:bg-[#9C2755] hover:text-white'"
                @click="selectMenu(item)"
            >
                <!-- Ícono -->
                <i :class="item.icon" class="text-white text-xl lg:text-lg"></i>

                <!-- Texto en tooltip para móviles -->
                <span class="lg:hidden absolute bottom-full mb-2 px-3 py-1 bg-[#8A1C4A] text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    {{ item.name }}
                </span>

                <!-- Texto visible en pantallas grandes -->
                <span class="hidden lg:inline ml-2">{{ item.name }}</span>
            </li>
        </ul>
    </aside>
</template>