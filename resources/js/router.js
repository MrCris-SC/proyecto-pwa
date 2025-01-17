import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: () => import('./Pages/Home.vue'), // Cambia según tu estructura
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import('./Pages/Auth/Login.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;

