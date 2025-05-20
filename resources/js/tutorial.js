// Configuración de pasos del tutorial (en español)
const pasosComunes = [
  {
    elemento: "#enlace-inicio",
    popover: {
      titulo: "¡Bienvenido/a!",
      descripcion: "Esta es tu página principal. Aquí verás un resumen de actividades importantes.",
      lado: "derecha",
    },
  },
];

const pasosPorRol = {
  admin: [
    {
      elemento: "#enlace-usuarios",
      popover: {
        titulo: "Gestión de Usuarios",
        descripcion: "Desde aquí puedes registrar y administrar a todos los usuarios del sistema.",
        lado: "derecha",
      },
    },
    {
      elemento: "#enlace-concursos",
      popover: {
        titulo: "Administrar Concursos",
        descripcion: "Crea y gestiona concursos activos para los participantes.",
      },
    },
  ],
  lider: [
    {
      elemento: "#enlace-proyectos",
      popover: {
        titulo: "Tus Proyectos",
        descripcion: "Revisa y gestiona los proyectos asignados a tu equipo.",
        lado: "arriba",
      },
    },
  ],
};

export function obtenerPasosTutorial(rol) {
  return [...pasosComunes, ...(pasosPorRol[rol] || [])];
}