<template>
  <button 
    @click="iniciarTutorial"
    class="fixed bottom-4 right-4 bg-[#611232] text-white px-4 py-2 rounded-lg shadow-lg hover:bg-[#8A1C4A] z-50 flex items-center"
  >
    <i class="fas fa-question-circle mr-2"></i> ¿Necesitas ayuda?
  </button>
</template>

<script setup>
import { Driver } from "driver.js";
import "driver.js/dist/driver.min.css";
import { obtenerPasosTutorial } from "../tutorial.js";
import { usePage } from "@inertiajs/vue3";

const { props } = usePage();

const iniciarTutorial = () => {
  const driver = new Driver({
    animate: true,
    opacity: 0.75,
    doneBtnText: "Finalizar",
    nextBtnText: "Siguiente",
    prevBtnText: "Anterior",
    popoverClass: "driver-popover-estilo",
  });

  const pasos = obtenerPasosTutorial(props.auth.user.rol);
  driver.defineSteps(pasos);
  driver.start();
};

// Opcional: Mostrar automáticamente al cargar (tu código actual)
onMounted(() => {
  if (localStorage.getItem("tutorialMostrado") !== "true") {
    iniciarTutorial();
    localStorage.setItem("tutorialMostrado", "true");
  }
});
</script>

<style>
.driver-popover-estilo {
  background-color: #611232;
  color: white;
  border-radius: 10px;
}
.driver-popover-estilo .driver-popover-title {
  font-size: 1.2rem;
  font-weight: bold;
}
</style>