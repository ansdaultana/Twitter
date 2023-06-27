<script setup>

import { router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed, watchEffect } from 'vue';
import { onMounted, } from 'vue';
import Bluetick from '@/Components/Bluetick.vue'
import TwitterLayout from '@/Layouts/TwitterLayout.vue';
import { Head } from '@inertiajs/vue3';
const page = usePage();
const Authuser = computed(() => page.props.auth.user);
const Notifications=computed(()=>page.props.notification);

const GoToTweet = (username, id) => {
    if (id) {
        router.get(`/${username}/tweets/${id}`);
    }
}
defineOptions({
    layout: TwitterLayout,
});

</script>

<template>
<Head title="Notification" />

    <div class="w-full md:w-1/2 h-full overflow-y-scroll overflow-x-hidden scrollbar-hide">
        <div class="flex-col">
            <div class="px-5 py-2 border-gray-900 border-b flex justify-between">
                <div class="flex">
                    <button @click="back" class=" px-2  hover:bg-gray-800 rounded-full">
                        <svg class="h-6 w-8 text-white text-lg  " fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                        </svg>

                    </button>
                    <div>
                        <h1 class="text-xl text-white ml-3 mt-2 mb-4 font-bold">Notifications</h1>

                    </div>
                </div>
            </div>
        </div>
    
        <div v-for="notification in Notifications" class="w-full p-4 border-b border-gray-800 hover:bg-[#181818] flex cursor-pointer transition duration-200 ease-in-out"
        @click="GoToTweet(Authuser.username,notification.tweet_url)">
        
        <div class="lg:flex-none w-16 hidden md:block mr-4">
            <div class="flex items-center" >
                <img :src='notification.profile' class="h-12 w-12 rounded-full flex-none" />
            </div>
        </div>
        <div class="w-full relative">
            <div class="flex items-center md:justify-between w-full">
                <div class="md:hidden flex items-center md:mr-2 w-16" >
                    <img :src='notification.profile' class="h-12 w-12 rounded-full flex-none" />
                </div>
                <div class="flex items-center" >
                    <p class="font-semibold text-white "> {{ notification.name }} </p>
                    <p class="m-2 text-gray-400     "> {{ notification.message }} </p>
                </div>
                <div class="hidden lg:block text-xl p-4 text-zinc ml-2">&bull;</div>

               

      
        </div>
    </div>
    </div>
</div>

</template>
<style>
body {
    background-color: black;
}

/* Hide the scroll bar */
.scrollbar-hide::-webkit-scrollbar {
    width: 0.4em;
    background-color: transparent;
}

/* Optional: Add a custom color for the thumb */
.scrollbar-hide::-webkit-scrollbar-thumb {
    background-color: #888;
}

/* Optional: Add hover styles for the thumb */
.scrollbar-hide::-webkit-scrollbar-thumb:hover {
    background-color: #555;
}
</style>