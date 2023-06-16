<template>
    <div name="feed" class="w-full md:w-1/2 h-full overflow-y-scroll scrollbar-hide">
        <div name="header" class="px-5  py-3 border-gray-800 border-b flex items-center justify-between ">
            <h1 class=" text-xl text-white font-bold">Home</h1>
        </div>
        <TweetModal />
        <TweetCreate />
        <Tweets />
    </div>
</template>

<script setup>
import TweetCreate from '@/Components/TweetCreate.vue';
import TweetModal from "@/Components/TweetModal.vue";
import Tweets from '@/Components/Tweets.vue';
import TwitterLayout from '@/Layouts/TwitterLayout.vue';
defineOptions({
    layout: TwitterLayout,
});
</script>