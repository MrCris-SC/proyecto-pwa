<script setup>
import { ref, defineProps, defineEmits } from 'vue';

import { usePage } from '@inertiajs/vue3';
const { props } = usePage();

const emit = defineEmits(['menu-selected']);

const selectedMenu = ref('registro');

const menuItems = ref([]);

if (props.auth.user.rol === 'lider') {
    menuItems.value = ['Registro', 'Gestión de proyectos', 'Proceso', 'Resultados', 'Equipos Registrados'];
} else if (props.auth.user.rol === 'admin') {
    menuItems.value = ['Usuarios', 'Nuevo Concurso', 'Configuración', 'Reportes'];
} else {
    menuItems.value = ['Inicio', 'Perfil', 'Ayuda'];
}

const selectMenu = (item) => {
    selectedMenu.value = item.toLowerCase();
    emit('menu-selected', selectedMenu.value);
};
</script>

<template>
    <aside class="w-full lg:w-64 bg-[#611232] text-white shadow-lg rounded-lg p-5 mb-6 lg:mb-0 lg:mr-6">
        <h3 class="text-lg font-semibold mb-6">Menú</h3>
        <ul class="space-y-3">
            <li 
                v-for="item in menuItems" 
                :key="item"
                class="p-3 rounded-lg cursor-pointer transition duration-200 ease-in-out"
                :class="selectedMenu === item.toLowerCase() ? 'bg-[#8A1C4A] text-white shadow-md' : 'hover:bg-[#9C2755] hover:text-white'"
                @click="selectMenu(item)"
            >
                <span class="flex items-center">
                    <!-- Icono (puedes usar una librería como FontAwesome) -->
                    <i class="fas fa-folder mr-2"></i>
                    {{ item }}
                </span>
            </li>
        </ul>
    </aside>
</template>
