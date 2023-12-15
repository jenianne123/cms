<script >
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';

import ArticleList from '@/Pages/ArticleComponent/ArticleList.vue';
import NavigationMenu from '@/Layouts/NavigationMenu.vue';
import BreezeFooter from '@/Components/Footer.vue';


export default {
     
    components: {
        BreezeAuthenticatedLayout,
        NavigationMenu,
        ArticleList,   
        BreezeFooter,
    }
};
</script>

<template>
    <Head title="Article" />

    <BreezeAuthenticatedLayout>
        <div class="flex flex-grow"> 
            <NavigationMenu/>
            <div class="container">
                <ArticleList />
            </div>
        </div>
        <BreezeFooter/>
    </BreezeAuthenticatedLayout>
</template>

<style>
    .container{
        background-color: rgb(239, 239, 238);
    }
</style>

