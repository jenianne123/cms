<!-- NavLink.vue -->
<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import { computed } from 'vue';

const props = defineProps(['href', 'active']);

const classes = computed(() => ({
  'px-1 pt-1 py-2 leading-5 text-gray-500 hover:isolate hover:border-transparent hover:bg-white focus:bg-white-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green active:shadow-lg': !props.active,
  'px-1 pt-1 py-2 leading-5 text-gray-900 hover:isolate hover:border-transparent hover:bg-white focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green active:shadow-lg': props.active,
}));
</script>

<template>
  <Link :href="href" :class="classes">
    <slot />
  </Link>
</template>
