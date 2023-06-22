<script setup>

import { router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed, watchEffect } from 'vue';
import { onMounted, } from 'vue';
import Bluetick from '@/Components/Bluetick.vue'
import TwitterLayout from '@/Layouts/TwitterLayout.vue';

const page = usePage();
const mutualFollowing = computed(() => page.props.mutualFollowing);
const Authuser = computed(() => page.props.auth.user);

const users = computed(() => page.props.users);
let currentPath = ref(window.location.pathname);
let searchValue = ref('');
const checkWindowLocation = () => {
    //if new location is /users/username and old loc is '/' then currentpath.val will be /users/username
    const newLocation = window.location.pathname;
    if (newLocation !== currentPath.value) {
        currentPath.value = newLocation;
        searchValue.value = '';
    }
};


defineOptions({
    layout: TwitterLayout,
});
onMounted(() => {
    setInterval(checkWindowLocation, 100);
});
watch(searchValue, value => {

    router.get(currentPath.value, { search: value }, { preserveState: true });
});
const navigateToUser = (searchedUser) => {
    const username = searchedUser.username;
    router.get(`/users/${username}`);

};
let trending = [
    { top: 'Music', title: 'We Won', bottom: '135K Tweets' },
    { top: 'Pop', title: 'Blue Ivy', bottom: '40k tweets' },
    { top: 'Trending in US', title: 'Denim Day', bottom: '40k tweets' },
    { top: 'Trending', title: 'When Beyonce', bottom: '25.4k tweets' },
];



const followOrUnfollow = async (user) => {

try {
    const response = await axios.post(`/users/${user.username}/follow`)
    user.is_following = response.data.isFollowing;
    
} catch (error) {
    console.log(error)
}
}

</script>

<template>
    <div name="feed" class="  border-gray-800  py-2 px-6 relative 
    
 h-full bg-black overflow-y-scroll  scrollbar-hide
    lg:w-1/2 w-full ">

        <input v-model="searchValue" class=" pl-12 mt-5 rounded-full w-full p-2 text-white bg-[#181818] text-sm"
            placeholder="Search Twitter" />

        <div 
        v-if="searchValue!==''"
         name="search"
            class="
            ring-2 
            ring-offset-gray-400
            max-h-96 w-full rounded-lg bg-[#181818] border-l border-r border-b mb-2  overflow-y-scroll scrollbar-hide ">

            <div class=" p-3">
                <p class="text-lg font-bold text-[#48C9B0]">Results</p>
            </div>
            <button @click="navigateToUser(searchedUser)" v-for="searchedUser in users" :key="searchedUser.id"
                class="cursor-pointer transition duration-200 ease-in-out w-full flex hover:bg-[#2F2F2F]   p-3 ">
                <img 
               :src="searchedUser.profile"
                class="w-12 h-12 rounded-full border border-lighter" />
                <div class=" ml-4 ">
                    <div class="flex">
                        <p class="text-md font-bold mt-1 leading-tight text-white "> {{ searchedUser.name }} </p>
                        <Bluetick :username="searchedUser.username" />
                    </div>

                    <div class="flex ">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-4 h-4 mt-1 text-dark ml-2">
                            <path stroke-linecap="round"
                                d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                        </svg>
                        <p class="text-sm  leading-tight text-gray-400 w-12"> {{ searchedUser.username }} </p>

                    </div>
                </div>

            </button>

        </div>
        <div  name="tags" class="
        w-full rounded-lg border mt-4 border-gray-800 bg-[#181818]">
            <div class="flex items-center justify-between p-3">
                <p class="text-lg text-[#48C9B0] font-bold">Trends for You</p>
                <i class="fas fa-cog text-lg text-blue"></i>
            </div>

            <button v-for="trend in trending"
                class="w-full flex justify-between hover:bg-[#2F2F2F] p-3 cursor-pointer transition duration-200 ease-in-out">
                <div>
                    <p class="text-xs text-left leading-tight text-dark"> {{ trend.top }} </p>
                    <p class="font-semibold text-white  text-sm text-left leading-tight"> {{ trend.title }} </p>
                    <p class="text-left text-sm leading-tight text-dark"> {{ trend.bottom }} </p>
                </div>
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="w-6 text-white h-6 mr-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
            </svg>
    
            </button>
            <button class="p-3 w-full hover:bg-[#2F2F2F] text-left text-[#48C9B0]">
                Show More
            </button>
        </div>
        <div class="w-full md:hidden  rounded-lg bg-[#181818] border mb-2 border-gray-800 my-4">
            <div class=" p-3">
                <p class="text-lg font-bold text-[#48C9B0]">Who to Follow</p>
            </div>
            <div v-if="mutualFollowing.length>0">

                <div v-for="mutual in mutualFollowing">
    
                    <button v-if="mutual.id !== Authuser.id"
                    class="cursor-pointer transition duration-200 ease-in-out w-full flex justify-between hover:bg-[#2F2F2F]   p-3 ">
                    <div class="flex">

                    <img :src="mutual.profile" class="w-12 h-12 rounded-full border border-lighter" />
                    <div class=" ml-4">
                        <p class="text-sm font-bold leading-tight w-auto text-white"> {{ mutual.name }} </p>
                        <p class="text-sm leading-tight text-gray-500"> {{ mutual.username }} </p>
                    </div>
                </div>


                    <button @click="followOrUnfollow(mutual)"  @mouseenter="isHovered = true"
                    @mouseleave="isHovered = false">
                    <div v-if="mutual.is_following"
                        class=" mr-4 text-sm text-[#48C9B0] py-1 px-4 rounded-full border-2 hover:text-red-800 hover:bg-red-400 hover:border-red-800 border-[#48C9B0]">
                        {{ !isHovered ? 'Following' : 'Unfollow' }}

                    </div>
                    <div v-if="!mutual.is_following"
                        class=" mr-4 text-sm text-[#48C9B0] py-1 px-4 rounded-full border-2 hover:text-black hover:bg-[#48C9B0] border-[#48C9B0]">
                        follow
                    </div>
                </button>


                  
                </button>
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