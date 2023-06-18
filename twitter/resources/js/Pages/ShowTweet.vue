<script setup>
import Tweets from '@/Components/Tweets.vue';

import { computed } from "vue";
import { usePage, router } from '@inertiajs/vue3';
import TwitterLayout from '@/Layouts/TwitterLayout.vue';
import TweetModal from "@/Components/TweetModal.vue";
import axios from 'axios';
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: TwitterLayout,
});
const page = usePage();
const auth = ref(page.props.authUser);
const tweet = computed(() => page.props.tweet);

const back = () => {
    router.get("/home");
}



</script>
<template>
    <Head title="Tweet" />

    <div class="w-full md:w-1/2 h-full overflow-y-scroll scrollbar-hide">
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
                        <h1 class="text-xl text-white ml-3 font-bold">Tweet</h1>
                    </div>
                </div>
            </div>
            <div class="w-full p-4 border-b border-gray-800 hover:bg-[#181818] flex cursor-pointer transition duration-200 ease-in-out"
            :class="{ 'hover:bg-black': Menu }">
            <div class="flex-none mr-4">
                <div class="flex items-center" @click.stop="GoToUserPage(tweet.user.username)">
    
                    <img src="https://media.licdn.com/dms/image/C4D03AQHySl-ZFgyOfg/profile-displayphoto-shrink_400_400/0/1655959852960?e=1691020800&v=beta&t=YOs9sUi06NTkbFEsNz90qPTtNLRf1lZPaGVyXSXZg9A"
                        class="h-12 w-12 rounded-full flex-none" />
                </div>
            </div>
            <!-- <div class="w-full relative">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center" @click.stop="GoToUserPage(tweet.user.username)">
                        <p class="font-semibold text-white"> {{ tweet.user.name }} </p>
                        <p class="text-sm text-dark ml-2"> @{{ tweet.user.username }} </p>
                        <div class="hidden lg:block text-sm text-dark ml-2">&bull;</div>
                        <p class="  hidden lg:block text-sm text-dark ml-1"> {{ formatCreatedAt(tweet.created_at) }} </p>
                    </div>
                    <div class="flex">
                        <button @click.stop="openMenu" class="p-1 m-2 h-8 rounded-full hover:bg-zinc">
                            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-6 text-white h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <p class="py-2 text-white">
                    {{ tweet.text }}
                </p>
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center text-sm text-dark">
                        <div class="flex justify-center items-center cursor-pointer">
    
                            <MessageOutline class="hover:bg-blue mt-1  p-2 rounded-full" fillColor="blue" size=12 />
                            <span class="text-sm font-extrabold text-[#5e5c5c]  mt-1 ml-2">{{ tweet.comments_count
                            }}</span>
    
                        </div>
    
                    </div>
                    <div class="flex justify-center items-center text-sm text-dark">
                        <div class="flex cursor-pointer">
                            <Sync class="hover:bg-green-400  p-2 mt-1 rounded-full" fillColor="green" size=12 />
                        </div>
                    </div>
                    <div class="flex justify-center items-center text-sm text-dark">
                        <div class="flex  cursor-pointer">
    
                            <svg @click.stop="LikeTheTweet(tweet)" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="
                                            w-4 h-5 mt-1 hover:fill-[#FF3E20]  text-red-800 
                                            hover:scale-150
                                            transition-transform
                                            duration-300 ease-in-out
                                            " :class="{ 'fill-[#FF3E20]': tweet.isLiked }">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
    
                            <span class="text-sm font-extrabold text-[#5e5c5c]  mt-1 ml-2">{{ tweet.likes_count }}</span>
    
                        </div>
                    </div>
                    <div class="flex items-center text-sm text-dark">
                        <i class="fas fa-share-square mr-3"></i>
                    </div>
                </div>
           
            </div> -->
        </div>
        </div>
       
        <Tweets />
    </div>
</template>

