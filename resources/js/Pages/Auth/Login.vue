<script setup>
import BreezeButton from '@/Components/Button.vue';
import BreezeCheckbox from '@/Components/Checkbox.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    }).then(() => {
        Inertia.post(route('dashboard.user'), {
            userRole: 'User Role Value' 
        });
    });
};
</script>

<template>
    <BreezeGuestLayout class="items-center">
        <br>
        <Head title="Login" />

        <BreezeValidationErrors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div >
                <BreezeInput id="email" type="email" placeholder="Email" class="m-1 block w-full font-calibri text-lg" style="width: 400px; height: 40px;" v-model="form.email" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <BreezeInput id="password" type="password" placeholder="Password" class="m-1 block w-full font-calibri text-lg" style="width: 400px; height: 40px;" v-model="form.password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center text-white-600">
                    <BreezeCheckbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 font-calibri" style="color: black;">Remember me</span>
                </label>
            </div>
            <br>

            <div class="flex justify-center items-center">
                <BreezeButton class="bg-green font-calibri text-white bold-text items-center flex justify-center" style="font-size: 20px; font-weight: bold; height: 35px; width: 100px;" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Login
                </BreezeButton>
            </div>
        </form> 
    </BreezeGuestLayout>
    
</template>
