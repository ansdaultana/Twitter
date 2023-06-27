<script setup>
import TwitterLayout from '@/Layouts/TwitterLayout.vue';
import Bluetick from '@/Components/Bluetick.vue'
import { Head } from '@inertiajs/vue3';

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
    router.get("/home");
}
const ShowFollowing = (username) => {
    router.get(`/${username}/following`);

}
const navigateToUser = (follower) => {
    if (follower.username === authUser.value.username) {
        router.get(`/${follower.username}`);
    }
    else {
        const username = follower.username;
        router.get(`/users/${username}`);

    }

};
const followOrUnfollow = async (follower) => {

    try {
        const response = await axios.post(`/users/${follower.username}/follow`)
        follower.isFollowing = response.data.isFollowing;
    } catch (error) {
        console.log(error)
    }
}


</script>


<template>
<Head title="Followers" />

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
                        <h1 class="text-xl text-white ml-3 mb-4 font-bold">{{ BeingVieweduser.name }}</h1>

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
        <div v-for="follower in followers" :key="follower.id" @click="navigateToUser(follower)"
            class="cursor-pointer transition duration-200 ease-in-out w-full flex justify-between hover:bg-gray-800  ml-4  p-3 ">
            <div class="flex ">
                <img :src="follower.profile" class="w-12 h-12 rounded-full border border-lighter" />
                <div class=" lg:block ml-4 ">
                    <div class="flex">
                        <p class="text-md mt-1 font-bold leading-tight text-white "> {{ follower.name }} </p>
                        <Bluetick :username="follower.username" />

                    </div>

                    <div class="flex ">

                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-4 h-4 mt-1 text-dark ">
                            <path stroke-linecap="round"
                                d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                        </svg>
                        <p class="text-sm  leading-tight text-gray-400 w-12"> {{ follower.username }} </p>


                    </div>

                </div>
            </div>

            <div v-if="follower.isprofile === false">
                <button @click="followOrUnfollow(follower); $event.stopPropagation()" class=" mr-4"
                    @mouseenter="follower.isHovered = true" @mouseleave="follower.isHovered = false">
                    <div v-if="follower.isFollowing"
                        class=" text-sm text-[#48C9B0] py-1 px-4 rounded-full border-2 hover:text-red-800 hover:bg-red-400 hover:border-red-800 border-[#48C9B0]">
                        {{ !follower.isHovered ? 'Following' : 'Unfollow' }}

                    </div>
                    <div v-if="!follower.isFollowing"
                        class=" text-sm text-[#48C9B0] py-1 px-4 rounded-full border-2 hover:text-black hover:bg-[#48C9B0] border-[#48C9B0]">
                        follow

                    </div>
                </button>

            </div>
        </div>

    </div>

    <TweetModal />
</template>
