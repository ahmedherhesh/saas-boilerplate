<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Tabs from './Tabs/Tabs.vue';
import ProfileTab from './Tabs/ProfileTab.vue';
import SubscriptionTab from './Tabs/SubscriptionTab.vue';


</script>

<template>

    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Profile</h2>
        </template>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
            <Tabs />
        </div>
        <div class="tabs max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <ProfileTab />
            <SubscriptionTab />
        </div>

    </AuthenticatedLayout>
</template>
