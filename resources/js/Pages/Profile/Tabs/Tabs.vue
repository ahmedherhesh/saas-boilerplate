<template>
    <div class="w-full bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <div class="sm:hidden">
            <label for="tabs" class="sr-only">Select tab</label>
            <select id="tabs"
                class="bg-gray-50 border-0 border-b border-gray-200 text-gray-900 text-sm rounded-t-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option>Statistics</option>
                <option>Services</option>
                <option>FAQ</option>
            </select>
        </div>
        <ul class="text-sm font-medium text-center text-gray-500 divide-x divide-gray-200 rounded-lg sm:flex dark:divide-gray-600 dark:text-gray-400 rtl:divide-x-reverse"
            id="fullWidthTab" data-tabs-toggle="#fullWidthTabContent" role="tablist">
            <li class="w-full">
                <button id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile"
                    aria-selected="true"
                    class="inline-block w-full p-4 rounded-ss-lg bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:hover:bg-gray-600">Profile</button>
            </li>
            <li class="w-full">
                <button id="subscription-tab" data-tabs-target="#subscription" type="button" role="tab"
                    aria-controls="subscription" aria-selected="false"
                    class="inline-block w-full p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:hover:bg-gray-600">Subscription</button>
            </li>
        </ul>

    </div>
</template>
<script setup>
import { onMounted } from 'vue';

onMounted(() => {
    for (let el of fullWidthTab.children) {
        el.addEventListener('click', function (e) {
            let targetTabId = e.currentTarget.firstChild.dataset.tabsTarget
            let targetTab = document.querySelector(targetTabId);
            for (let child of targetTab.parentElement.children) {
                child.classList.add('hidden')
            }
            targetTab.classList.remove('hidden')
        });
    }
});
</script>