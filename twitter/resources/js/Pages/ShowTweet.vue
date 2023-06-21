<script setup>
import Tweets from '@/Components/Tweets.vue';
import TweetCreate from '@/Components/TweetCreate.vue';

import MessageOutline from 'vue-material-design-icons/MessageOutline.vue'
import Sync from 'vue-material-design-icons/Sync.vue'
import { computed, inject } from "vue";
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
const user = computed(() => page.props.user);
const tweet = computed(() => page.props.tweet);
const replies = computed(() => page.props.replies);
let isLiked = ref(page.props.isLiked);
let likes_count = ref(page.props.likes_count);
const edit = ref(inject('edit'));
const EditTweet = ref(inject('EditTweet'));
const AreYouSure = ref(inject('AreYouSure'));
const ShowAreYouSure = ref(inject('ShowAreYouSure'));
const Menu = ref(false)
const back = () => {
    router.get("/home");
}
const AuthUserOptions = (username) => {
    if (username === auth.value.username) {
        return true;
    }
    return false;

}
const ForAnyView = (username) => {
    if (username !== auth.value.username) {
        return true;
    }
    return false;

}
const GoToUserPage = (username) => {
    if (username === auth.value.username) {
        router.get(`/${username}`);
    }
    else {
        router.get(`/users/${username}`);

    }

}
const openMenu = () => {

    //blur.value=true;
    Menu.value = !Menu.value;
}
const LikeTheTweet = async (tweet) => {
    try {
        const response = await axios.post(`/tweets/${tweet.id}/like`);
        isLiked.value = response.data.isLiked;
        if (isLiked.value === true) {
            likes_count.value++;
        } else {
            likes_count.value--;
        }
    } catch (error) {
        console.log(error);
    }

}
const OpenEditModal = (tweet) => {
    EditTweet.value = tweet;
    edit.value = true;
    openMenu();
}

const IsUserSure = (id, method) => {
    ShowAreYouSure.value = true;
    openMenu();
    if (AreYouSure.value) {
        AreYouSure.value.id = id;
        AreYouSure.value.method = method;

    }
}

const formatCreatedAt = (date) => {
    const createdDate = new Date(date);
    const currentDate = new Date();
    const diffInMilliseconds = Math.abs(currentDate - createdDate);

    const seconds = Math.floor(diffInMilliseconds / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    if (days > 0) {
        return `${days} day${days > 1 ? 's' : ''} ago`;
    } else if (hours > 0) {
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else if (minutes > 0) {
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else {
        return `${seconds} second${seconds !== 1 ? 's' : ''} ago`;
    }
}
</script>
<template>
    <Head title='Twitter' />
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
                <div class="lg:flex-none hidden md:block mr-4">

                    <div class="flex items-center" @click.stop="GoToUserPage(user.username)">

                        <img 
                :src='user.profile'
                        
                      
                class="h-12 w-12 rounded-full flex-none" />
                    </div>
                </div>
                <div class="w-full relative">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center" @click.stop="GoToUserPage(user.username)">
                            <div class="md:hidden flex items-center mr-2">

                                <img src="https://media.licdn.com/dms/image/C4D03AQHySl-ZFgyOfg/profile-displayphoto-shrink_400_400/0/1655959852960?e=1691020800&v=beta&t=YOs9sUi06NTkbFEsNz90qPTtNLRf1lZPaGVyXSXZg9A"
                                    class="h-12 w-12 rounded-full flex-none" />
                            </div>
                       
                            <p class="font-semibold text-white"> {{ user.name }} </p>
                            <p class="text-sm text-dark ml-2"> @{{ user.username }} </p>
                            <div class="hidden lg:block text-sm text-dark ml-2">&bull;</div>
                            <p class="  hidden lg:block text-sm text-dark ml-1"> {{ formatCreatedAt(tweet.created_at) }}
                            </p>
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
                  
                    <div v-if="tweet.image" class="flex justify-center">
                        <img :src='tweet.image'  class=" max-h-700 max-w-700 rounded-xl w-96">
                    </div>
                    <div v-if="tweet.video" class="flex justify-center">
                        <video  :src="tweet.video" controls class=" max-h-700 max-w-500 rounded-xl "/>
        
                    </div>
               
                    <div class="flex items-center justify-between w-full border-t border-gray-700 mt-2 py-1">
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
                                                    " :class="{ 'fill-[#FF3E20]': isLiked }">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>

                                <span class="text-sm font-extrabold text-[#5e5c5c]  mt-1 ml-2">{{ likes_count }}</span>

                            </div>
                        </div>
                        <div class="flex items-center text-sm text-dark">
                            <i class="fas fa-share-square mr-3"></i>
                        </div>
                        <div v-if="Menu === true" class="flex justify-end">
                            <div class="absolute top-12 right-6     mt-[-20px] mr-4 z-10">
                                <div
                                    class="w-60 border-2 bg-black rounded-lg border-gray-400 flex flex-col hover:scale-105 transition-transform ease-in-out">

                                    <button v-if="ForAnyView(user.username)"
                                        @click.stop="IsUserSure(user.username, 'unfollow')"
                                        class="text-white px-2 py-3 hover:bg-gray-800 hover:text-blue text-sm border-2 border-gray-600  border-b">
                                        <div class="flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2    ">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                                            </svg>
                                            <div>
                                                Unfollow @{{ user.username }}

                                            </div>
                                        </div>

                                    </button>
                                    <button v-if="ForAnyView(user.username)" @click.stop=""
                                        class="text-white px-2 py-3 hover:bg-gray-800 text-sm border-2 border-gray-600  border-b">
                                        <div class="flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" />
                                            </svg>

                                            <div>
                                                Mark as Spam

                                            </div>
                                        </div>

                                    </button>

                                    <button @click.stop="" v-if="ForAnyView(user.username)"
                                        class="text-white px-2 py-3 hover:text-red-900 hover:bg-red-300  text-sm border-2 border-gray-600  border-b">
                                        <div class="flex">
                                            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>


                                            <div>
                                                Block

                                            </div>
                                        </div>

                                    </button>
                                    <button @click.stop="OpenEditModal(tweet)" v-if="AuthUserOptions(user.username)"
                                        class="text-white px-2 py-3 hover:text-blue hover:bg-gray-800 text-sm border-2 border-gray-600  border-b">
                                        <div class="flex">

                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>

                                            <div>
                                                Edit

                                            </div>
                                        </div>

                                    </button>

                                    <button v-if="AuthUserOptions(user.username)"
                                        @click.stop="IsUserSure(tweet.id, 'deleteTweet')"
                                        class="text-white px-2 py-3 hover:text-red-900 hover:bg-red-300 text-sm border-2 border-gray-600  border-b">
                                        <div class="flex">


                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>

                                            <div>
                                                Delete
                                            </div>
                                        </div>

                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex mt-7 ml-24">
            <div class="text-gray-400 ">Replying to</div>
            <div class="text-blue ml-3 cursor-pointer" 
            @click.stop="GoToUserPage(user.username)"
            > @{{ user.username }}</div>
        </div>
        <TweetCreate heading="Tweet Your Reply" BtnText="Reply" :id="tweet.id" />

        <div class="px-5 py-4 border-gray-900 border-b flex justify-between">
            <div class="flex">
         
                <div>
                    <h1 class="text-xl text-white ml-3 font-bold">Replies:</h1>
                </div>
            </div>
        </div>

        <Tweets :Mytweets="replies"/>


    </div>
</template>

<style>
.max-h-700 {
  max-height: 500px;
}

.max-w-700 {
  max-width: 500px;
}
</style>