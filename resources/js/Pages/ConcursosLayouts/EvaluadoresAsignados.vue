<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MenuLateral from '@/ComponentsConcursos/MenuLateral.vue';

const props = defineProps({
    evaluadores: {
        type: Array,
        default: () => [],
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Evaluadores Asignados
            </h2>
        </template>

        <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
            <!-- Menú lateral -->
            <MenuLateral :rol="userRole" @menu-selected="handleMenuSelected" />
            
            <main 
                class="w-full max-w-4xl mx-auto p-8 bg-white shadow rounded-lg border border-gray-200 relative"
                :class="{ 'opacity-50 pointer-events-none': mostrarModalEvaluaciones || mostrarModalInscripcion || mostrarModalCerrar }"
            >
                <div v-if="evaluadores.length === 0" class="text-center text-gray-500 py-8">
                    No hay evaluadores asignados a tu equipo.
                </div>
                <ul v-else>
                    <li 
                        v-for="(ev, idx) in evaluadores" 
                        :key="ev.id" 
                        class="mb-4 bg-gray-50 rounded-md shadow-sm border border-gray-100 p-4 flex flex-col"
                    >
                        <strong class="text-blue-700">Evaluador {{ idx + 1 }}</strong>
                        <template v-if="ev.perfil && ev.perfil.length">
                            <span class="text-gray-600 mt-2">Perfiles: 
                                <span v-for="(p, i) in ev.perfil" :key="i">
                                    {{ p }}<span v-if="i < ev.perfil.length - 1">, </span>
                                </span>
                            </span>
                        </template>
                    </li>
                </ul>
            </main>
        </div>  
    </AuthenticatedLayout>
</template>