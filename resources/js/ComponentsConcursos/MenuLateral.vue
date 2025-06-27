<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import { usePage, router } from '@inertiajs/vue3'; // Eliminar la importación de "route"

const { props } = usePage();
const emit = defineEmits(['menu-selected']);
const isMenuMinimized = ref(false);
import '@fortawesome/fontawesome-free/css/all.min.css';

const menuItems = ref([]);
const selectedMenu = ref(''); // Inicializar vacío

// Configuración del menú según rol
if (props.auth.user.rol === 'lider') {
    menuItems.value = [
        { name: 'Concursos', icon: 'fas fa-file-alt', route: 'concursos.index' },
        { name: 'Gestión de proyectos', icon: 'fas fa-tasks', route: 'gestion.proyectos' },
        { name: 'Evaluadores asignados', icon: 'fas fa-cogs', route: 'evaluadores.asignados' },
        { name: 'Resultados', icon: 'fas fa-chart-line', route: 'resultados.index' }, // <-- Asegúrate de tener esta línea
        { name: 'Equipos Registrados', icon: 'fas fa-users', route: 'equipos.registrados' }
    ];
} else if (props.auth.user.rol === 'admin') {
    menuItems.value = [
        { name: 'Usuarios', icon: 'fas fa-users-cog', route: 'new.user' },
        { name: 'Concursos', icon: 'fas fa-trophy', route: 'concursos.index' },
        { name: 'Registro de Criterios', icon: 'fas fa-list-check', route: 'criterios.registro' },
        { name: 'Configuración', icon: 'fas fa-cog', route: 'configuracion.index' },
        { name: 'Reportes', icon: 'fas fa-chart-pie', route: 'reportes.index' },
        
    ];
} else if (props.auth.user.rol === 'evaluador') {
    menuItems.value = [
        { name: 'Concursos', icon: 'fas fa-trophy', route: 'concursos.index' },
        { name: 'Evaluación', icon: 'fas fa-clipboard-check', route: 'evaluacion.index' },
        { name: 'Proyectos Asignados', icon: 'fas fa-list-ul', route: 'proyectos.asignados' },
        { name: 'Rubrica', icon: 'fas fa-check-square', route: 'criterios.index' },
        { name: 'Reportes', icon: 'fas fa-chart-bar', route: 'reportes.index' },
        { name: 'Perfil', icon: 'fas fa-user', route: 'perfil.index' }
    ];
} else {
    menuItems.value = [
        { name: 'Inicio', icon: 'fas fa-home', route: 'inicio.index' },
        { name: 'Perfil', icon: 'fas fa-user', route: 'perfil.index' },
        { name: 'Ayuda', icon: 'fas fa-question-circle', route: 'ayuda.index' }
    ];
}

// Establecer el menú seleccionado basado en la ruta actual
selectedMenu.value = menuItems.value.find(item => window.route().current(item.route))?.name.toLowerCase() || ''; // Usar "window.route"

// Función para manejar la selección del menú
const selectMenu = (item) => {
    selectedMenu.value = item.name.toLowerCase();
    emit('menu-selected', selectedMenu.value);

    if (item.route && window.route().has(item.route)) { // Usar "window.route"
        router.get(window.route(item.route)); // Navegar a la ruta
    }
};

const toggleMenu = () => {
    isMenuMinimized.value = !isMenuMinimized.value;
};
</script>

<template>
    <aside class="fixed lg:relative bottom-0 left-0 right-0 w-full lg:w-64 bg-[#611232] text-white shadow-lg rounded-t-lg lg:rounded-lg p-3 lg:p-5 transition-all duration-300 ease-in-out z-50">
        <ul class="flex lg:flex-col justify-around lg:justify-start space-x-4 lg:space-x-0 lg:space-y-3">
            <li 
                v-for="item in menuItems" 
                :key="item.name"
                class="p-3 rounded-lg cursor-pointer flex flex-col lg:flex-row items-center text-center transition duration-200 ease-in-out relative group"
                :class="selectedMenu === item.name.toLowerCase() ? 'bg-[#8A1C4A] text-white shadow-md' : 'hover:bg-[#9C2755] hover:text-white'"
                @click="selectMenu(item)"
            >
                <i :class="item.icon" class="text-white text-xl lg:text-lg"></i>
                <span class="lg:hidden absolute bottom-full mb-2 px-3 py-1 bg-[#8A1C4A] text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    {{ item.name }}
                </span>
                <span class="hidden lg:inline ml-2">{{ item.name }}</span>
            </li>
        </ul>
    </aside>
</template>