<script setup>
    import BreezeNavLink from '@/Components/NavLink.vue';
</script>

<template>
    <nav class="nav shadow-lg bg-white shadow-gray-500 min-h-screen">
        <!-- Navigation Links -->
        <div>
            
            <li class="no-bullet">
                <ul>
                    <div class="mx-3 pt-5">
                        <BreezeNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </BreezeNavLink>
                    </div>
                </ul>
                <ul>
                    <div style="margin: 10px;"
                        v-if="userAccess.generic_pages === 1 || $page.props.auth.user.role == 1"
                    >
                        <BreezeNavLink :href="route('genericpages')" :active="route().current('page')">
                            Generic Pages
                        </BreezeNavLink>
                    </div>
                </ul>
                <ul>
                    <div style="margin: 10px;"
                        v-if="userAccess.articles === 1 || $page.props.auth.user.role == 1"
                    >
                        <BreezeNavLink :href="route('articlepage')" :active="route().current('articlepage')">
                            Articles
                        </BreezeNavLink>
                    </div>
                </ul>
                <ul>
                    <div style="margin: 10px;" active-class="active-link"
                        v-if="userAccess.faq === 1 || $page.props.auth.user.role == 1"
                    >
                        <BreezeNavLink :href="route('faqs')" :active="route().current('faqs')">
                            Faqs
                        </BreezeNavLink>
                    </div>
                </ul>
                <ul>
                    <div style="margin: 10px;"
                        v-if="userAccess.accounts === 1 || $page.props.auth.user.role == 1"
                    >
                        <BreezeNavLink :href="route('quickLink')" :active="route().current('quickLink')">
                            Quick Links
                        </BreezeNavLink>
                    </div>
                </ul>
                <ul>
                    <div style="margin: 10px;"
                        v-if="userAccess.images === 1 || $page.props.auth.user.role == 1"
                    >
                        <BreezeNavLink :href="route('image')" :active="route().current('image')">
                            Images
                        </BreezeNavLink>
                    </div>
                </ul>
                <ul>
                    <div style="margin: 10px;"
                        v-if="
                            $page.props.auth.user.role == 1
                        "
                    >
                        <BreezeNavLink :href="route('manageacc')" :active="route().current('manageacc')">
                            Accounts
                        </BreezeNavLink>
                    </div>
                </ul>
            </li>
        </div>
    </nav>
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
    .content-heading {
        border-bottom: 2px solid #FFCD00; /* Adjust the color and thickness as needed */
    }

    h2{
        color: white;
        font-family: Garamond;
        font-size: 25px;  
    }

    .no-bullet{
        list-style-type: none;
    }

    .nav{
        font-family: Calibri;
        font-size: 20px;
        min-height: 700px;
        width: 200px;
        font-weight: bold;
        color: rgb(0, 0, 0);
        place-items: center;
    }

    .active-link{
        color: white;
        background-color: #0C4B05;
    }
</style>
