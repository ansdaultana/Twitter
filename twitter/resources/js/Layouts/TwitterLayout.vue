<script setup>
import { ref, provide } from 'vue';
import Sidebarlayout from "@/Layouts/Sidebarlayout.vue";
import Rightbarlayout from '@/Layouts/Rightbarlayout.vue';
import TweetModal from "@/Components/TweetModal.vue";

const tweetsidebtn = ref(false);
const blur = ref(false);
const edit=ref(false);
const EditTweet=ref(null);

const textarea = ref(null);

provide('tweetsidebtn', tweetsidebtn);
provide('blur', blur);
provide('edit', edit);
provide('EditTweet', EditTweet);


const blurOn = () => {
    if (tweetsidebtn) {
        return true;
    }
    if (edit) {
        return true;
    }
    return false;
}

</script>

<template>
    <TweetModal />

    <div class="h-screen flex w-full relative " :class="{'blur-xs':tweetsidebtn || edit}">
        <Sidebarlayout />

        <slot />

        <Rightbarlayout />
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
