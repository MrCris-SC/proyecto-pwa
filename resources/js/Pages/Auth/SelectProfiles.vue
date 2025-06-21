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
                        </div>
                    </div>
                </div>
            </nav>
            <main 
            class="w-full max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg relative"
            :class="{ 'opacity-50 pointer-events-none': mostrarModalEvaluaciones || mostrarModalInscripcion || mostrarModalCerrar }">
            
                <div class="max-w-4xl mx-auto mt-10">
                    <h1 class="text-2xl font-bold mb-4">Seleccionar Perfiles</h1>
                    <form @submit.prevent="submit">
                        <!-- Sección de Perfil de Jurado -->
                        <div class="mt-8">
                            <h3 class="text-xl font-bold mb-4">Selecciona tu Perfil</h3>

                            <!-- Mostrar lista de perfiles solo si mostrarListaPerfiles es true -->
                            <div v-if="mostrarListaPerfiles">
                                <input
                                    v-model="busquedaPerfil"
                                    type="text"
                                    placeholder="Buscar perfil..."
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#611232] mb-4"
                                />
                                <div class="space-y-2">
                                    <div v-for="(categoria, index) in categoriasPerfiles" :key="index" class="mb-4">
                                        <h4 class="font-semibold text-[#611232] mb-2">{{ categoria.nombre }}</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div v-for="perfil in categoria.perfiles" :key="perfil" class="flex items-center">
                                                <input
                                                    type="radio"
                                                    :id="perfil"
                                                    :value="perfil"
                                                    v-model="perfilSeleccionado"
                                                    class="mr-2"
                                                />
                                                <label :for="perfil">{{ perfil }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mostrar perfil seleccionado y botón para editar -->
                            <div v-if="perfilSeleccionado" class="mt-4 mb-6">
                                <h4 class="font-semibold text-[#611232] mb-2">Perfil seleccionado:</h4>
                                <ul>
                                    <li class="text-sm mb-2">{{ perfilSeleccionado }}</li>
                                </ul>
                                <button
                                    v-if="!mostrarListaPerfiles"
                                    @click="editarSeleccionPerfiles"
                                    class="mt-4 bg-[#611232] text-white px-6 py-2 rounded-md hover:bg-[#8A1C4A] transition duration-300 focus:outline-none focus:ring-2 focus:ring-[#611232] focus:ring-offset-2"
                                >
                                    Editar selección
                                </button>
                            </div>
                        </div>

                        <!-- Botón para guardar -->
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        >
                            Guardar
                        </button>
                    </form>
                </div>
            </main>
            
        </div>
    </div>
    
</template>

<script>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
    data() {
        return {
            perfilesDisponibles: [
            "Ingeniería ambiental",
            "Ingeniería civil",
            "Ingeniería agrícola",
            "Ingeniería en alimentos",
            "Ingeniería en análisis de sistemas",
            "Ingeniería en bioingeniería",
            "Ingeniería en bioquímica",
            "Ingeniería en ciencias de la computación",
            "Ingeniería en desarrollo de software",
            "Ingeniería en energías renovables",
            "Ingeniería en electrónica",
            "Ingeniería en logística",
            "Ingeniería mecánica",
            "Ingeniería en mecatrónica",
            "Ingeniería química",
            "Ingeniería en sistemas computacionales",
            "Ingeniería en tics",
            "Ingeniería industrial",
            "Ingeniería forestal",
            "Ingeniería y gestión de proyectos",
            "Enfermería",
            "Medicina",
            "Psicología",
            "Sociología",
            "Trabajo social",
            "Educación",
            "Educación especial e inclusión",
            "Profesorado",
            "Administración de empresas",
            "Administración de empresas turísticas",
            "Administración comercial",
            "Administración de gestión y personal",
            "Agronomía",
            "Análisis de sistemas",
            "Análisis de mercados",
            "Antropología",
            "Arquitectura",
            "Biología marina",
            "Biología",
            "Biotecnología",
            "Ciencias de la computación",
            "Ciencias de la educación",
            "Comunicación y medios de información",
            "Consultoría empresarial",
            "Contabilidad",
            "Contaduría",
            "Desarrollo de software",
            "Desarrollo sustentable",
            "Dietética",
            "Diseño gráfico",
            "Economía",
            "Educación infantil",
            "Educación primaria",
            "Electricidad",
            "Electrónica",
            "Emprendimiento",
            "Emprendimiento de negocios",
            "Enseñanza interactiva",
            "Especialista en el área de aplicación",
            "Farmacia",
            "Fisioterapia",
            "Gastronomía",
            "Gerencia de empresas",
            "Gestión empresarial",
            "Informática",
            "Ingeniería química en alimentos",
            "Innovación educativa",
            "Innovación tecnológica",
            "Innovación y desarrollo empresarial",
            "Licenciatura en informática",
            "Literatura",
            "Matemáticas",
            "Medicina general",
            "Mercadotecnia",
            "Nutrición",
            "Pedagogía",
            "Preparación física deportiva",
            "Profesorado universitario",
            "Psicopedagogía",
            "Psiquiatría",
            "Publicidad",
            "Recursos humanos",
            "Sistemas computacionales",
            "Traducción",
            "Turismo",
            "Medicina veterinaria",
            "Otro",
        ],

            perfilSeleccionado: '', // Cambiado a string
            busquedaPerfil: '',
            mostrarListaPerfiles: true,
        };
    },
    watch: {
        perfilSeleccionado(newVal) {
            if (newVal) {
                this.mostrarListaPerfiles = false;
            }
        }
    },
    computed: {
        categoriasPerfiles() {
            const categorias = [
                { nombre: 'Ingenierías', perfiles: [] },
                { nombre: 'Ciencias de la Salud', perfiles: [] },
                { nombre: 'Ciencias Sociales', perfiles: [] },
                { nombre: 'Educación', perfiles: [] },
                { nombre: 'Otros', perfiles: [] },
            ];

            this.perfilesDisponibles.forEach(perfil => {
                if (perfil.toLowerCase().includes(this.busquedaPerfil.toLowerCase())) {
                    if (perfil.includes('Ingeniera/o') || perfil.includes('Ingeniería')) {
                        categorias[0].perfiles.push(perfil);
                    } else if (perfil.includes('Médica/o') || perfil.includes('Enfermera/o') || perfil.includes('Psicóloga/o')) {
                        categorias[1].perfiles.push(perfil);
                    } else if (perfil.includes('Sociólogo') || perfil.includes('Trabajador/a social')) {
                        categorias[2].perfiles.push(perfil);
                    } else if (perfil.includes('Educación') || perfil.includes('Profesor/a')) {
                        categorias[3].perfiles.push(perfil);
                    } else {
                        categorias[4].perfiles.push(perfil);
                    }
                }
            });

            return categorias.filter(categoria => categoria.perfiles.length > 0);
        },
    },
    methods: {
        editarSeleccionPerfiles() {
            this.mostrarListaPerfiles = true;
        },
        submit() {
            // Enviar el perfil seleccionado como un array para cumplir con la validación del backend
            router.post(route('perfil.save'), { perfil: [this.perfilSeleccionado] }, {
                onSuccess: () => {
                    router.get(route('dashboard'));
                }
            });
        },
    },
};
</script>
