
<script >
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { Head } from '@inertiajs/inertia-vue3';

    import AddImage from '@/Pages/AddImage/AddImage.vue';
    import NavigationMenu from '@/Layouts/NavigationMenu.vue';
    import BreezeFooter from '@/Components/Footer.vue';

    import axios from 'axios';
    export default {
        data(){
            return{
                faqs:[],
                is_edit:false,
                edit_id:0,
            }
        },
    
        components: {
            BreezeAuthenticatedLayout,
            NavigationMenu,
            AddImage,   
            BreezeFooter,
        },
    };
</script>

<template>
    <Head title="Images" />

    <BreezeAuthenticatedLayout>
        <div class="flex flex-grow">
            <NavigationMenu/>
            <div class="container">
                <AddImage />
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