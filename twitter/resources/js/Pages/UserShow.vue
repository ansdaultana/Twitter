<script setup>
import Tweets from '@/Components/Tweets.vue';
import Bluetick from '@/Components/Bluetick.vue';
import { computed ,inject} from "vue";
import { usePage, router } from '@inertiajs/vue3';
import TwitterLayout from '@/Layouts/TwitterLayout.vue';
import TweetModal from "@/Components/TweetModal.vue";
import axios from 'axios';
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3';



const openEditProfile=ref(inject('openEditProfile'));

defineOptions({
    layout: TwitterLayout,
});
const isHovered = ref(false);
const page = usePage();
const BeingVieweduser = computed(()=>page.props.BeingVieweduser);
const tweets = computed(() => page.props.tweets);

const profile = computed(() => page.props.profile);

let followerCount = computed(() => page.props.followerCount);
let followingCount = computed(() => page.props.followingCount);
const tweetCount = computed(() => page.props.tweets.length);

const isFollowing = ref(page.props.auth_is_following_opened_user);


const back = () => {
    router.get("/home");
}

const followOrUnfollow = async (username) => {

    try {
        const response = await axios.post(`/users/${username}/follow`)
        isFollowing.value = response.data.isFollowing;

        if (isFollowing.value === true) {
            followerCount.value++;
        } else {
            followerCount.value--;

        }
    } catch (error) {
        console.log(error)
    }
}

const ShowFollowing = (username) => {
    router.get(`/${username}/following`);

}
const ShowFollower = (username) => {
    router.get(`/${username}/follower`);

}

</script>
<template>
    <Head title="User" />

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
                        <div class="flex">
                            <h1 class="text-xl text-white ml-3 font-bold">{{ BeingVieweduser.name }}</h1>
                            <Bluetick :username="BeingVieweduser.username" />

                        </div>
                        <h3 class="text-md text-gray-400 ml-3 ">{{ tweetCount }} tweets</h3>

                    </div>
                </div>
            </div>
            <div class="-mb-14 md:w-full h-80 flex justify-center">
                <img name="cover" class="z-0  max-h-full max-w-full object-contain  "
                :src='BeingVieweduser.cover'
                alt="">
            </div>
            <div class="flex justify-between">
                <img name="profile"
                    class=" w-32 h-32 ml-4 mb-4 z-10 rounded-full -bottom-1 right-0 border-2 border-white bg-gray-900"
                :src='BeingVieweduser.profile'
                   
                    alt="Profile Picture" />

                <div v-if="profile === false">
                    <div>
                        <button @click="followOrUnfollow(BeingVieweduser.username)" class="mt-16 mr-4"
                            @mouseenter="isHovered = true" @mouseleave="isHovered = false">
                            <div v-if="isFollowing"
                                class=" text-sm text-[#48C9B0] py-1 px-4 rounded-full border-2 hover:text-red-800 hover:bg-red-400 hover:border-red-800 border-[#48C9B0]">
                                {{ !isHovered ? 'Following' : 'Unfollow' }}

                            </div>
                            <div v-if="!isFollowing"
                                class=" text-sm text-[#48C9B0] py-1 px-4 rounded-full border-2 hover:text-black hover:bg-[#48C9B0] border-[#48C9B0]">
                                follow

                            </div>
                        </button>
                    </div>

                </div>
                <div v-if="profile === true">

                    <button
                    @click.stop="openEditProfile=!openEditProfile"
                        class="text-white rounded-full border-2 px-2 py-1 hover:bg-zinc hover:text-black hover:border-black mt-16 mr-4">
                        Edit Profile
                    </button>


                </div>
            </div>
            <div class="flex-col mb-3 ">
                <div class="flex">

                    <h1 class="text-xl text-white ml-6 font-bold">{{ BeingVieweduser.name }}</h1>
                    <Bluetick :username="BeingVieweduser.username" />

                </div>
                <div class="flex ml-4">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="w-4 h-4 mt-1 text-dark ml-2">
                        <path stroke-linecap="round"
                            d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                    </svg>
                    <h3 class="text-md text-gray-400  ">{{ BeingVieweduser.username }}</h3>
                </div>
            </div>
            <div class="flex border-b-2 pb-8 border-gray-800">
                <button @click="ShowFollowing(BeingVieweduser.username)"
                    class="text-gray-400 ml-4 hover:text-white transition-transform hover:scale-105 ease-in-out ">
                    {{ followingCount }} following
                </button>
                <button @click="ShowFollower(BeingVieweduser.username)"
                    class="text-gray-400 ml-4 hover:text-white transition-transform hover:scale-105 ease-in-out  ">
                    {{ followerCount }} followers
                </button>
            </div>
        </div>
        <Tweets :Mytweets="tweets" />
    </div>
</template>

