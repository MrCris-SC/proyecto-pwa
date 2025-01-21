<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'inline-flex items-center px-1 pt-1 border-b-4 border-primary text-white font-medium leading-5 focus:outline-none focus:border-primary transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-4 border-transparent text-white font-medium leading-5 hover:text-guinda hover:border-white focus:outline-none focus:text-primary focus:border-primary transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
