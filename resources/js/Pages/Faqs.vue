<script >
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';

import FaqList from '@/Pages/Faqqs/FaqList.vue';
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
        FaqList,
        BreezeFooter,    
    },
    methods: {
        getter(){
            axios.get('/faqlist').then(({data})=>{
                this.faqs = data;
            })
        },

        editData(id){
            this.is_edit=true;
            this.edit_id=id;
        },
        
        successEdit(){
            this.is_edit=false;
            this.edit_id=0;
            this.getter();
        }

    },

    mounted(){
        this.getter();
    }
};
</script>

<template>
    <Head title="Faqs" />
    <BreezeAuthenticatedLayout>
        <div class="flex flex-grow">
            <NavigationMenu/>
            <div class="container">
                <FaqList  @success="getter"/>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
    <BreezeFooter/>
</template>

<style>

    .container{
        background-color: rgb(239, 239, 238);
    }
</style>

