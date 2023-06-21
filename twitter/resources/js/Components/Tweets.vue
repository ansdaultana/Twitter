<script setup>
import Tweet from "@/Components/Tweet.vue";
import { defineProps, computed} from 'vue';

const props = defineProps({
  Mytweets: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <div>
    <Tweet v-for="tweet in props.Mytweets" :key="tweet.id" :tweet="tweet" />
  </div>
</template>
