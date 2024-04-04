<template>
    <div id="paypal"></div>
</template>
<script setup>
import { loadScript } from "@paypal/paypal-js";
import { onMounted } from 'vue'
const paypal = () => {
    loadScript({ clientId: 'AQ7M1aSDdwGUXGqVm4LOm4RhDk24BqupZIX5VSPe-QP1U-v_q58VxdsbB5iG-EDjjefIbdnU9cjVOPpY' })
        .then((paypal) => {
            paypal.Buttons({
                style: {
                    shape: 'rect',
                    color: 'gold',
                    layout: 'vertical',
                    label: 'paypal'
                },
                createSubscription: function (data, actions) {
                    return actions.subscription.create({
                        /* Creates the subscription */
                        plan_id: 'P-5S108013FF819440JMYGX7TY'
                    });
                },
                onApprove: function (data, actions) {
                    console.log(data);
                    fetch(route('paypal.subscription.create'), {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        method: "POST",
                        body: JSON.stringify(data)
                    })
                        .then(function (res) {
                            console.log(res)
                        })
                        .catch(function (res) {
                            console.log(res)
                        })
                    console.log(data);
                }
            }).render('#paypal');
        })
        .catch((error) => {
            console.error("failed to load the PayPal JS SDK script", error);
        });
}
onMounted(() => {
    paypal();
})
</script>