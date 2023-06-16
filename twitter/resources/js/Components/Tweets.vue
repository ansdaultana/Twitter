<script setup>
import { computed } from 'vue';
import HeartOutline from 'vue-material-design-icons/HeartOutline.vue'
import MessageOutline from 'vue-material-design-icons/MessageOutline.vue'
import Sync from 'vue-material-design-icons/Sync.vue'
import { usePage,router } from '@inertiajs/vue3'
import dayjs from 'dayjs';
const page = usePage();

const Mytweets = computed(() => page.props.tweets);
const GoToUserPage =(username)=>
{
router.get(`/users/${username}`);
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
    <div v-for="tweet in Mytweets" :key="tweet.id"
        class="w-full p-4 border-b border-gray-800 hover:bg-[#181818] flex cursor-pointer transition duration-200 ease-in-out">
        <div class="flex-none mr-4">
            <div class="flex items-center" @click="GoToUserPage(tweet.user.username)">

            <img src="https://media.licdn.com/dms/image/C4D03AQHySl-ZFgyOfg/profile-displayphoto-shrink_400_400/0/1655959852960?e=1691020800&v=beta&t=YOs9sUi06NTkbFEsNz90qPTtNLRf1lZPaGVyXSXZg9A"
                class="h-12 w-12 rounded-full flex-none" />
            </div>
        </div>
        <div class="w-full">
            <div class="flex items-center w-full" >
            <div class="flex items-center" @click="GoToUserPage(tweet.user.username)">
                <p class="font-semibold text-white"> {{ tweet.user.name }} </p>
                <div class="flex">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mt-1 text-dark ml-2">
                      <path stroke-linecap="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                    </svg>
                    <p class="text-sm text-dark "> {{ tweet.user.username }} </p>
    
                  </div>
            </div>
                <div class="text-sm text-dark ml-2">&bull;</div>

                <p class="text-sm text-dark ml-1"> {{ formatCreatedAt(tweet.created_at) }} </p>
                <i class="fas fa-angle-down text-dark ml-auto"></i>
            </div>
            <p class="py-2 text-white">
                {{ tweet.text }}
            </p>
            

            <div class="flex items-center justify-between w-full">
                <div class="flex items-center text-sm text-dark">
                    <div class="flex justify-center items-center cursor-pointer">

                        <MessageOutline  class="hover:bg-blue mt-1  p-2 rounded-full" fillColor="blue" size=12 />
                <span class="text-sm font-extrabold text-[#5e5c5c]  mt-1 ml-2">{{ tweet.comments_count }}</span>

                    </div>

                </div>
                <div class="flex justify-center items-center text-sm text-dark">
                    <div class="flex cursor-pointer">
                        <Sync  class="hover:bg-green-400  p-2 mt-1 rounded-full" fillColor="green" size=12 />
                    </div>
                </div>
                <div class="flex justify-center items-center text-sm text-dark">
                    <div class="flex cursor-pointer">

                        <HeartOutline class="hover:hover:bg-red-500  p-2  rounded-full" fillColor="red" size=12 />
                        <span class="text-sm font-extrabold text-[#5e5c5c]  mt-1 ml-2">{{ tweet.likes_count }}</span>
                  
                    </div>
                </div>
                <div class="flex items-center text-sm text-dark">
                    <i class="fas fa-share-square mr-3"></i>
                </div>
            </div>
        </div>
    </div>
</template>