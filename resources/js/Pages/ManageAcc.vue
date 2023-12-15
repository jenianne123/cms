<script >
  import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
  import { Head } from '@inertiajs/inertia-vue3';

  import UserList from '@/Pages/Accounts/UserList.vue';
  import NavigationMenu from '@/Layouts/NavigationMenu.vue';

  import axios from 'axios';
  import BreezeFooter from '@/Components/Footer.vue';
  
  export default {
      data(){
          return{
              users:[],
              is_edit:false,
              edit_id:0,
          }
      },
    
      components: {
          BreezeAuthenticatedLayout,
          NavigationMenu,
          UserList,    
          BreezeFooter,    
      },
  };
</script>

<template>
    <Head title="Accounts" />
    <BreezeAuthenticatedLayout>
        <div class="flex">
            <NavigationMenu/>
            <div class="container">
                <UserList @success="getter"/>
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

