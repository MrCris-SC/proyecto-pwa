<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-[#611232]">
                Editar Concurso
            </h2>
        </template>
        <div class="flex flex-col lg:flex-row min-h-screen py-6 px-4 lg:px-12 bg-[#F8F9FA]">
            <main class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg">
                <div class="relative ">
                    <div>
                        <div v-if="!concurso || !concurso.id" class="flex items-center justify-center h-screen">
                            <p>Cargando...</p>
                        </div>
                        <div v-else class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md">
                            <h2 class="text-2xl font-bold mb-4">Editar Concurso</h2>
                            <form @submit.prevent="submitForm" class="space-y-6">
                            <div>
                                <label for="nombre" class="block text-gray-700 font-bold">Nombre del Concurso</label>
                                <input type="text" id="nombre" v-model="localConcurso.nombre" class="mt-1 block w-full p-2 border border-gray-300 rounded" required />
                            </div>
                            <div>
                                <label for="descripcion" class="block text-gray-700 font-bold">Descripción</label>
                                <textarea id="descripcion" v-model="localConcurso.descripcion" class="mt-1 block w-full p-2 border border-gray-300 rounded" required></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                <label for="fecha_inicio" class="block text-gray-700 font-bold">Fecha de Inicio</label>
                                <input type="date" id="fecha_inicio" v-model="localConcurso.fecha_inicio" class="mt-1 block w-full p-2 border border-gray-300 rounded" required />
                                </div>
                                <div>
                                <label for="fecha_apertura" class="block text-gray-700 font-bold">Fecha de Apertura</label>
                                <input type="date" id="fecha_apertura" v-model="localConcurso.fecha_apertura" class="mt-1 block w-full p-2 border border-gray-300 rounded" required />
                                </div>
                                <div>
                                <label for="fecha_terminacion" class="block text-gray-700 font-bold">Fecha de Terminación</label>
                                <input type="date" id="fecha_terminacion" v-model="localConcurso.fecha_terminacion" class="mt-1 block w-full p-2 border border-gray-300 rounded" required />
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-300">
                                Actualizar Concurso
                                </button>
                            </div>
                            </form>
                            <div class="mt-4">
                            <button @click="goBack" class="text-gray-600 hover:text-gray-800">Volver a Concursos</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        
        
        
    </AuthenticatedLayout>


    
  </template>
  
  <script setup>
  import { reactive, watch } from 'vue';
  import { defineProps } from 'vue';
  import { router } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  
  const props = defineProps({
    concurso: {
      type: Object,
      default: () => ({})
    }
  });
  
  // Creamos una copia local reactiva para evitar mutar directamente la prop
  const localConcurso = reactive({
    id: props.concurso.id || null,
    nombre: props.concurso.nombre || '',
    descripcion: props.concurso.descripcion || '',
    fecha_inicio: props.concurso.fecha_inicio || '',
    fecha_apertura: props.concurso.fecha_apertura || '',
    fecha_terminacion: props.concurso.fecha_terminacion || '',
  });
  
  // Actualizamos la copia local si la prop cambia
  watch(() => props.concurso, (newVal) => {
    Object.assign(localConcurso, newVal);
  });
  
  function submitForm() {
    // Realizamos la petición PUT para actualizar el concurso
    router.put(route('concursos.update', localConcurso.id), { ...localConcurso });
  }
  
  function goBack() {
    router.get(route('concursos.index'));
  }
  </script>
  