<script >
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';

import GenericList from '@/Pages/Generic/GenericList.vue';
import NavigationMenu from '@/Layouts/NavigationMenu.vue';
import BreezeFooter from '@/Components/Footer.vue';

export default {
    
    data() {
        return {
            generic_list: [],
            is_edit: false,
            edit_id: 0,
        }
    },

    components: {
        BreezeAuthenticatedLayout,
        NavigationMenu,
        GenericList,
        BreezeFooter,
    },

    methods: {
        getter(){
            axios.get('/genericlist').then(({data})=>{
                this.generic_list = data;
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
    <Head title="Generic Pages" />

    <BreezeAuthenticatedLayout>
        <div class="flex">
            <NavigationMenu/>
            <div class="container">
                <GenericList  @success="getter"/>
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