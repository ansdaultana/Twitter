<script setup>
import { ref, inject,computed } from 'vue';
import { useForm,usePage,router } from '@inertiajs/vue3'

import ImageOutline from 'vue-material-design-icons/ImageOutline.vue';
import FileGifBox from 'vue-material-design-icons/FileGifBox.vue';
import Emoticon from 'vue-material-design-icons/Emoticon.vue';

const page=usePage();
const tweetsidebtn = ref(inject('tweetsidebtn'));
const user=computed(()=>page.props.auth.user);
let newTweet = useForm({
    text: ""
});

let addNewTweet = () => {
    newTweet.post('/createtweet');
    newTweet.text=''
}
const VisitProfile = (username) => {
  if (window.location.pathname !== `/users/${username}`) {
    router.get(`/users/${username}`);

  }}
</script>


<template>
    <div name="modal" v-if="tweetsidebtn === false">
        <div class="px-5 py-3 border-gray-800 border-b flex">
            <div class="flex-none">
               
            <img @click="VisitProfile(user.username)" src="https://media.licdn.com/dms/image/C4D03AQHySl-ZFgyOfg/profile-displayphoto-shrink_400_400/0/1655959852960?e=1691020800&v=beta&t=YOs9sUi06NTkbFEsNz90qPTtNLRf1lZPaGVyXSXZg9A"
                class="flex-none w-12 h-12 rounded-full border border-lighter cursor-pointer" />
        </div>
            <form v-on:submit.prevent="addNewTweet" class="w-full px-4 relative">
                <textarea  v-model="newTweet.text" placeholder="What's up?"
                    class="mt-3  pb-3 bg-black text-white w-full focus:outline-none" required minlength="3" />

                <div v-if="newTweet.errors.text" v-text="newTweet.errors.text" class="text-red-500 text-xs mt-1">

                </div>

                <div class="flex items-center gap-8 border-t mt-4 p-4 border-gray-800 ">
                    <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">
                        <label for="fileUpload" class="cursor-pointer">
                            <ImageOutline fillColor="#48C9B0" :size=22 class="cursor-pointer" />

                        </label>
                        <input type="file" id="fileUpload" class="hidden" @change="get
                        ">
                    </div>
                  
                    <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">

                        <Emoticon fillColor="#48C9B0" :size=22 class="cursor-pointer" />
                    </div>

                </div>

                <button type="submit"
                :disabled="newTweet.processing"
                    class="h-10 px-4 text-white font-semibold bg-[#48C9B0] hover:bg-[#78C9B0] focus:outline-none rounded-full absolute bottom-0 right-0">
                    Tweet
                </button>
            </form>
        </div>
    </div>
</template>