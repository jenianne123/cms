<script setup>
import { ref } from 'vue';
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import BreezeDropdown from '@/Components/Dropdown.vue';
import BreezeDropdownLink from '@/Components/DropdownLink.vue';
import BreezeNavLink from '@/Components/NavLink.vue';
import BreezeResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import BreezeFooter from '@/Components/Footer.vue';
import { Link } from '@inertiajs/inertia-vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="flex flex-col bg-gray-100">
            <nav class="w-full bg-green " >
                <!-- Primary Navigation Menu -->
                <div class="px-4 mx-auto max-w-9xl sm:px-6 lg:px-8" style="height: 100px;">
                    <div class="flex justify-between h-16">
                        <div class="flex" style="height: 100px; display: flex; align-items: center;">
                            <!-- Logo -->
                            <div class="flex items-center shrink-0">
                                <Link :href="route('dashboard')">
                                    <BreezeApplicationLogo class="block h-20 px-2" />
                                </Link>
                            </div>

                            <!--Header-->
                            <div>
                                <h2 class="leading-tight">UNIVERSITY LIBRARY SYSTEM</h2>
                                <h2 class="leading-snug">MARIANO MARCOS STATE UNIVERSITY</h2>
                            </div>
                        </div>

                        <div class="hidden ml-auto sm:flex sm:items-center sm:ml-6" style="height: 100px; display: flex; align-items: center;">
                            <!-- Settings Dropdown -->
                            <div class="relative" style="font-size: 25px;">
                                <BreezeDropdown align="right" width="48" >
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 text-lg leading-4 text-white text-gray-500 transition duration-150 ease-in-out font-calibri focus:outline-none">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <BreezeDropdownLink :href="route('dashboard')">
                                            Profile
                                        </BreezeDropdownLink>
                                        <BreezeDropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </BreezeDropdownLink>
                                    </template>
                                </BreezeDropdown>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <BreezeResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </BreezeResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">{{ $page.props.auth.user.name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <BreezeResponsiveNavLink :href="route('dashboard')">
                              Profile
                            </BreezeResponsiveNavLink>
                            <BreezeResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </BreezeResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->

            <div class="flex flex-row">
                    <!-- Page Content -->
                <main class="flex-grow size">
                    <slot />

                </main>
            </div>

            <!-- <div class="bg-gray-100">
                <BreezeFooter></BreezeFooter>
            </div> -->
        </div>

    </div>
</template>

<style>
.content-heading {
    border-bottom: 2px solid #FFCD00;
}

h2{
    color: white;
    font-family: Garamond;
    font-size: 20px;
    padding-left: 15px;
  }

.h-20{
    height: 4rem;
}

.size{
    width: 800px;
}

</style>
