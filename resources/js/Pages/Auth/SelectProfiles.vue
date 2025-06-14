<template>
    <div class="max-w-4xl mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-4">Seleccionar Perfiles</h1>
        <form @submit.prevent="submit">
            <!-- Sección de Perfil de Jurado -->
            <div class="mt-8">
                <h3 class="text-xl font-bold mb-4">Selecciona tus Perfiles (Máximo 3)</h3>

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
                                        type="checkbox"
                                        :id="perfil"
                                        :value="perfil"
                                        v-model="perfilSeleccionado"
                                        :disabled="perfilSeleccionado.length >= 3 && !perfilSeleccionado.includes(perfil)"
                                        class="mr-2"
                                    />
                                    <label :for="perfil">{{ perfil }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-if="perfilSeleccionado.length > 3" class="text-red-500 text-sm mt-2">Solo puedes seleccionar hasta 3 perfiles.</p>
                </div>

                <!-- Mostrar perfiles seleccionados y botón para editar -->
                <div v-if="perfilSeleccionado.length > 0" class="mt-4 mb-6">
                    <h4 class="font-semibold text-[#611232] mb-2">Perfiles seleccionados:</h4>
                    <ul>
                        <li v-for="(perfil, index) in perfilSeleccionado" :key="index" class="text-sm mb-2">{{ perfil }}</li>
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
</template>

<script>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
    data() {
        return {
            perfilesDisponibles: [
                "Ingeniera/o ambiental",
                "Ingeniera/o civil",
                "Ingeniera/o en agrícola",
                "Ingeniera/o en alimentos",
                "Ingeniera/o en análisis de sistemas",
                "Ingeniera/o en bioingeniería",
                "Ingeniera/o en bioquímico",
                "Ingeniera/o en ciencias de la computación",
                "Ingeniera/o en desarrollo de software",
                "Ingeniera/o en energías renovables",
                "Ingeniera/o en electrónica",
                "Ingeniera/o en logística",
                "Ingeniera/o en mecánico",
                "Ingeniera/o en mecatrónica",
                "Ingeniera/o en química",
                "Ingeniera/o en sistemas computacionales",
                "Ingeniera/o en tics",
                "Ingeniera/o industrial",
                "Ingeniería forestal",
                "Ingeniería y gestión de proyectos",
                "Enfermera/o",
                "Médica/o",
                "Psicóloga/o",
                "Sociólogo",
                "Trabajador/a social",
                "Educación",
                "Educación especial e inclusión",
                "Profesor/a",
                "Administrador de empresas",
                "Administrador de empresas turísticas",
                "Administrativa/o",
                "Administrativa/o comercial",
                "Administrativa/o de gestión y personal",
                "Agrónoma/o",
                "Análisis de sistemas",
                "Analista de mercados",
                "Antropóloga/o",
                "Arquitecta/o",
                "Biología marina",
                "Biólogo",
                "Biotecnología",
                "Ciencias de la computación",
                "Ciencias de la educación",
                "Comunicación y medios de información",
                "Consultor/a empresarial",
                "Contable",
                "Contaduría",
                "Desarrollo de software",
                "Desarrollo sustentable",
                "Dietista",
                "Diseñador/a gráfico",
                "Economista",
                "Educador/a infantil",
                "Educador/a primaria",
                "Electricista",
                "Eléctrico",
                "Emprendedor",
                "Emprendedor de negocios",
                "Enseñanza interactiva",
                "Especialista en el área de aplicación",
                "Farmacéutica",
                "Fisioterapeuta",
                "Gastronomía",
                "Gerente de empresas",
                "Gestión empresarial",
                "Informática/o",
                "Ingeniero químico en alimentos",
                "Innovación educativa",
                "Innovación tecnológica",
                "Innovación y desarrollo empresarial",
                "Licenciatura en informática",
                "Literatura",
                "Matemática/o",
                "Medicina general",
                "Mercadólogo",
                "Nutricionista",
                "Pedagoga/o",
                "Preparador/a físico/a deportivo/a",
                "Profesor de universidad",
                "Psicopedagogo",
                "Psiquiatra",
                "Publicitaria/o",
                "Recursos humanos",
                "Sistemas computacionales",
                "Traductor/a",
                "Turismo",
                "Veterinaria/o",
                "Otro",
            ],
            perfilSeleccionado: [],
            busquedaPerfil: '',
            mostrarListaPerfiles: true,
        };
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
            router.post(route('perfil.save'), { perfil: this.perfilSeleccionado }, {
                onSuccess: () => {
                    router.get(route('dashboard'));
                }
            });
        },
    },
};
</script>
