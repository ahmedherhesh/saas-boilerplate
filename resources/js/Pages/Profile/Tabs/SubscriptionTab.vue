<template>
    <div id="subscription" class="hidden">

        <div
            class="mx-auto block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
            <div v-if="$page.props.auth.subscribed">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white text-center mb-4">
                    You Are On The {{ $page.props.subscription.title }} Plan
                </h5>
                <p class="font-normal text-gray-700 dark:text-gray-400 mb-3">Ends At : {{ $page.props.subscription.ends_at }}
                </p>
                <DangerButton>Unsubscribe</DangerButton>
            </div>

            <div v-if="!$page.props.auth.subscribed">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white text-center mb-4">
                    You Don't Have Any Plan
                </h5>
                <div class="text-center">
                    <PrimaryButton>Click To Subscribe</PrimaryButton>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

</script>