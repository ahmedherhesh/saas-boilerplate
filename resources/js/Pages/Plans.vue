<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { router } from '@inertiajs/vue3'
import { ref } from 'vue';

defineProps({
    plans: Array
})
const stripePriceId = ref(0);
const showPaymentModal = ref(false)
const openPaymentModal = (e) => {
    stripePriceId.value = e.target.dataset.stripePriceId;
    showPaymentModal.value = true
}

const openPaypalModal = () => {
    showPaypalModal.value = true
};
const closePaymentMethodsModals = () => {
    showPaypalModal.value = false
    showStripeModal.value = false
    showPaymentModal.value = false
};
const showStripeModal = ref(false)
const closeStripeModal = ref(false)
const showPaypalModal = ref(false)
const closePaypalModal = ref(false)
</script>
<template>

    <Head title="Plans" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Plans</h2>
        </template>
        <div class="mx-auto">
            <div class="plans flex justify-center gap-4 max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
                <div v-for="plan in plans" :key="plan.id"
                    class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">

                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ plan.title }}
                    </h5>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{ plan.price }} {{ plan.currency }}
                    </p>

                    <PrimaryButton @click="openPaymentModal" :data-stripe-price-id="plan.stripe_price_id">
                        Pay
                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 5h12m0 0L9 1m4 4L9 9" />
                        </svg>
                    </PrimaryButton>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
    <Modal :show="showPaymentModal" @close="closePaymentMethodsModals">
        <div class="p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 p-6">
                Choose Your Favorite Payment
            </h2>
            <div class="payment-methods flex justify-around">
                <SecondaryButton @click="openPaypalModal">
                    <img src='/imgs/paypal.png' alt="" width="200" height="150">
                </SecondaryButton>
                <form :action="route('stripe.checkout') + '?stripe_price_id=' + stripePriceId" method="POST">
                    <input type="hidden" name="_token" :value="$page.props.csrf_token">
                    <SecondaryButton type="submit">
                        <img src='/imgs/stripe.png' alt="" width="200" height="150">
                    </SecondaryButton>
                </form>
            </div>
            <div class="mt-6 flex justify-center">
                <SecondaryButton @click="closePaymentMethodsModals">Cancel</SecondaryButton>
            </div>
        </div>
    </Modal>
</template>
