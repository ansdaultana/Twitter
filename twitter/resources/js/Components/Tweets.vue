<script setup>
import Tweet from "@/Components/Tweet.vue";
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3'

const page = usePage();

const Mytweets = computed(() => page.props.tweets);


</script>

<template>
    <div>
        <Tweet v-for="tweet in Mytweets" :key="tweet.id" :tweet="tweet" />
      </div>
    </template>