<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';

import CurrentAwareness from '@/Pages/QuickLinks/CurrentAwareness.vue';
import NavigationMenu from '@/Layouts/NavigationMenu.vue';
import BreezeFooter from '@/Components/Footer.vue';


export default {
     

     components: {
        NavigationMenu,
         BreezeAuthenticatedLayout,
         Head,
         CurrentAwareness,
         BreezeFooter
      
 
         
     }
 };

</script>

<template>
    <Head title="QuickLink" />

    <BreezeAuthenticatedLayout>
        <div class="flex">
            <NavigationMenu/>
            <div class="container">
                <CurrentAwareness />
            </div>
        </div>
    </BreezeAuthenticatedLayout>
    <BreezeFooter />
</template>

<style>
 .container{
        background-color: rgb(239, 239, 238);
    }


</style>