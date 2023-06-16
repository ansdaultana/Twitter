<script setup>
import TwitterLayout from '@/Layouts/TwitterLayout.vue';

import { computed, ref } from "vue";
import { usePage, router } from '@inertiajs/vue3';
defineOptions({
    layout: TwitterLayout,
});

const page = usePage();
const BeingVieweduser = ref(page.props.BeingVieweduser);
const authUser = computed(() => page.props.auth.user);

const followers = computed(() => page.props.followers);
const back = () => {
    router.get("/");
}
const ShowFollowing = (username) => {
    router.get(`/${username}/following`);

}
const navigateToUser = (username) => {
    if (username === authUser.value.username) {
        router.get(`/${username}`);
    }
    else {
        const username = follower.username;
        router.get(`/users/${username}`);

    }

};
</script>


<template>
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
                        <h1 class="text-xl text-white ml-3 font-bold">{{ BeingVieweduser.name }}</h1>
                        <h3 class="text-md text-gray-400 ml-3 ">1.1k tweets</h3>

                    </div>
                </div>
            </div>
        </div>
        <div class="w-full flex justify-between h-14">
            <button class="text-white hover:bg-gray-800 w-full  ">
                <span class="border-b-4 border-[#48C9B0] py-4">
                    Followers
                </span>
            </button>
            <button @click="ShowFollowing(BeingVieweduser.username)" class="text-gray-400 hover:bg-gray-800 w-full">
                Following
            </button>
        </div>
        <div @click="navigateToUser(follower.username)" v-for="follower in followers" :key="follower.id"
            class="cursor-pointer transition duration-200 ease-in-out w-full flex hover:bg-gray-800  ml-4  p-3 ">
            <img src="https://media.licdn.com/dms/image/C4D03AQHySl-ZFgyOfg/profile-displayphoto-shrink_400_400/0/1655959852960?e=1691020800&v=beta&t=YOs9sUi06NTkbFEsNz90qPTtNLRf1lZPaGVyXSXZg9A"
                class="w-12 h-12 rounded-full border border-lighter" />
            <div class="hidden lg:block ml-4 ">
                <p class="text-md font-bold leading-tight text-white "> {{ follower.name }} </p>

                <div class="flex ">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="w-4 h-4 mt-1 text-dark ml-2">
                        <path stroke-linecap="round"
                            d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                    </svg>
                    <p class="text-sm  leading-tight text-gray-400 w-12"> {{ follower.username }} </p>

                </div>
            </div>

        </div>
    </div>

    <TweetModal />
</template>
