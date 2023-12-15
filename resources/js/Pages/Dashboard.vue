<script setup>
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { Head } from '@inertiajs/inertia-vue3';
    import BreezeFooter from '@/Components/Footer.vue';
</script>

<template >
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout :showNav="showNav">
        <div class="flex">
            <div class="container">
                <div class="py-10">
                    <div class="flex justify-center">
                        <div class="flex justify-between">
                            <div class="px-20 h-50 w-50" 
                                v-if="userAccess.generic_pages === 1 || $page.props.auth.user.role == 1"
                            >
                                <button type="button" @click="pageBtn" class="flex flex-col items-center justify-center bg-green text-white max-w-xs transition duration-300 ease-in-out hover:scale-110 buttonStyle">
                                    <div class="px-30">
                                        <img src="/images/pages.svg" alt="" class="icon">
                                    </div>
                                    PAGES
                                </button>
                            </div>
                            <div class="px-20 h-50 w-50"
                                v-if="userAccess.articles === 1 || $page.props.auth.user.role == 1" 
                            >     
                                <button type="button" @click="articlesBtn" class="flex flex-col items-center justify-center bg-green text-white max-w-xs transition duration-300 ease-in-out hover:scale-110 buttonStyle">
                                    <div class="px-30">
                                        <img src="/images/articles.svg" alt="" class="icon">
                                    </div>
                                    ARTICLES
                                </button>
                            </div>
                            <div class="px-20 h-50 w-50"
                                v-if="userAccess.faq === 1 || $page.props.auth.user.role == 1"
                            >
                                <button type="button" @click="faqsBtn" class="flex flex-col items-center justify-center bg-green text-white max-w-xs transition duration-300 ease-in-out hover:scale-110 buttonStyle">
                                    <div class="px-30">
                                        <img src="/images/faqs.svg" alt="" class="icon">
                                    </div>
                                    FAQs
                                </button>
                            </div>
                        </div>
                    </div> 
                </div>

                <div class="py-2">
                    <div class="flex justify-center">
                        <div class="flex justify-between">
                            <div class="px-20 h-50 w-50 mb-6"
                                v-if="userAccess.images === 1 || $page.props.auth.user.role == 1"
                            >
                                <button type="button" @click="imageBtn" class="flex flex-col items-center justify-center bg-green text-white max-w-xs transition duration-300 ease-in-out hover:scale-110 buttonStyle">
                                    <div class="px-30">
                                        <img src="/images/image.svg" alt="" class="icon">
                                    </div>
                                    IMAGES
                                </button>
                            </div>
                            <div class="px-20 h-50 w-50"
                                v-if="userAccess.accounts === 1 || $page.props.auth.user.role == 1"
                            >     
                                <button type="button" @click="linkBtn" class="flex flex-col items-center justify-center bg-green text-white max-w-xs transition duration-300 ease-in-out hover:scale-110 buttonStyle">
                                    <div class="px-30">
                                        <img src="/images/link.svg" alt="" class="icon">
                                    </div>
                                    QUICK LINKS
                                </button>
                            </div>
                            <div class="px-20 h-50 w-50"
                                v-if="
                                    $page.props.auth.user.role == 1
                                "
                            >
                                <button type="button" @click="accountBtn" class="flex flex-col items-center justify-center bg-green text-white max-w-xs transition duration-300 ease-in-out hover:scale-110 buttonStyle">
                                    <div class="px-30">
                                        <img src="/images/account.svg" alt="" class="icon">
                                    </div>
                                    ACCOUNTS
                                </button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        
        </div>
    
    </BreezeAuthenticatedLayout>
    <BreezeFooter class="fixed-bottom"></BreezeFooter>
</template>

<script>
    import { Inertia } from '@inertiajs/inertia';
    import axios from 'axios';

    export default {
        data() {
            return {
                userAccess: {}
            }
        },

        methods: {
            pageBtn() {
                Inertia.visit(route('genericpages'));
            },

            articlesBtn() {
                Inertia.visit(route('articlepage'));
            },

            faqsBtn() {
                Inertia.visit(route('faqs'));
            },

            imageBtn() {
                Inertia.visit(route('image'));
            },

            linkBtn() {
                Inertia.visit(route('quickLink'));
            },

            accountBtn() {
                Inertia.visit(route('manageacc'));
            },

            getUserAccess(){
                axios.get('/get-user-access').then(({data}) => {
                    this.userAccess = data
                });
            }
        },

        mounted() {
            this.getUserAccess();
        },
    };
</script>

<style>
    .buttonStyle{
        width: 200px; 
        height: 150px; 
        font-size: 25px;
        font-weight: bold;
        padding: 3%;
        border-radius: 20px;
        border-color: #FFCD00;
        border-width: 3px;
    }

    .icon{
        width: 60px; 
        height: 60px;

    }

    .body{
        height: 100%;
    }

    .container{
        background-color: white;
    }
</style>
