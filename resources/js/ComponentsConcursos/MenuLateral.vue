<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const { props } = usePage();
const emit = defineEmits(['menu-selected']);
const selectedMenu = ref('Concursos');
const isMenuMinimized = ref(false);
import '@fortawesome/fontawesome-free/css/all.min.css';

const menuItems = ref([]);

// Configuración del menú según rol
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
        { name: 'Registro de Criterios', icon: 'fas fa-list-check' },
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
    selectedMenu.value = item.name.toLowerCase();
    emit('menu-selected', selectedMenu.value);
    
    if (props.auth.user.rol === 'evaluador') {
        handleEvaluadorMenu(item.name);
    } else if (props.auth.user.rol === 'admin') {
        handleAdminMenu(item.name);
    }
};

const toggleMenu = () => {
    isMenuMinimized.value = !isMenuMinimized.value;
};

const handleEvaluadorMenu = (menuName) => {
    const routesMap = {
        'Evaluación': 'evaluacion.index',
        'Proyectos Asignados': 'proyectos.asignados',
        'Criterios': 'criterios.index',
        'Reportes': 'reportes.index',
        'Perfil': 'perfil.index'
    };

    if (routesMap[menuName] && route().has(routesMap[menuName])) {
        router.get(route(routesMap[menuName]));
    }
};

const handleAdminMenu = (menuName) => {
    const routesMap = {
        'Usuarios': 'new.user',
        'Concursos': 'concursos.index',
        'Registro de Criterios': 'criterios.registro',
        'Configuración': 'configuracion.index',
        'Reportes': 'reportes.index'
    };

    if (routesMap[menuName] && route().has(routesMap[menuName])) {
        router.get(route(routesMap[menuName]));
    }
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