<script setup>
import { ref, inject, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3'
import Close from 'vue-material-design-icons/Close.vue';
import axios from 'axios';

const Show = ref(inject('ShowAreYouSure'));
const AreYouSure = ref(inject('AreYouSure'));

const closemodal = () => {
    Show.value = false;
}
const DeleteTweet = (id) => {
    router.post(`deletetweet/${id}`);
    closemodal();
}

const unfollow = async (username) => {

    try {
        await axios.post(`/users/${username}/follow`)
        router.get('/home');
        closemodal();

    } catch (error) {
        console.log(error)
    }

}
</script>

<template>
    <div v-if="Show"
        class=" fixed top-40  left-0 right-0  flex items-center justify-center z-50  hover:scale-105 ease-in duration-300">
        <div class="bg-black border border-gray-800 rounded-lg w-96 lg:96 m-10">
            <button class="cursor-pinter p-2 " @click="closemodal">
                <Close size=20 fillColor="white" />
            </button>
            <div class="px-5 py-3 border-gray-800 border-b flex  bg-black ">
                <form class="w-full px-4 relative mb-5 ">
                    <div class="flex justify-evenly">
                        <button @click="closemodal"
                            class="text-white px-2 py-3 w-28  hover:text-blue hover:bg-gray-800 text-sm border-2 border-gray-600  border-b">
                            <div class="flex">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                                </svg>

                                <div class="ml-4">
                                    No
                                </div>
                            </div>

                        </button>

                        <button v-if="AreYouSure.method === 'deleteTweet'" @click.prevent="DeleteTweet(AreYouSure.id)"
                            class="text-white px-2 py-3 w-28 hover:text-red-900 hover:bg-red-300 text-sm border-2 border-gray-600  border-b">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>

                                <div>
                                    Delete
                                </div>
                            </div>

                        </button>

                        <button v-if="AreYouSure.method === 'unfollow'" @click.prevent="unfollow(AreYouSure.id)"
                            class="text-white px-2 py-3 w-28 hover:text-red-900 hover:bg-red-300 text-sm border-2 border-gray-600  border-b">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>

                                <div>
                                    Unfollow
                                </div>
                            </div>

                        </button>

                    </div>


                </form>
            </div>


        </div>
    </div>
</template>

