<template>
    <div class=" w-full lg:inline-block hover:bg-[#181818] p-2 px-4 lg:pr-6 rounded-full cursor-pointer transition duration-200 ease-in-out">
        <div class="flex items-center">
            <component :is="icon"  fillColor="#48C9B0" :size=30 />
            <div v-if="icon===BellOutline" class="-mt-3 text-red-600">{{Notificationscount}}</div>
            <span class="lg:block hidden text-white font-extrabold text-[20px] pl-4 mt-0.5">
                {{ iconString }}
            </span>
        </div>
    </div>
</template>

<script setup>
import { toRefs } from 'vue'
import { computed } from 'vue';

import Home from 'vue-material-design-icons/Home.vue';
import Pound from 'vue-material-design-icons/Pound.vue';
import BellOutline from 'vue-material-design-icons/BellOutline.vue';
import EmailOutline from 'vue-material-design-icons/EmailOutline.vue';
import AccountOutline from 'vue-material-design-icons/AccountOutline.vue';
import { usePage } from '@inertiajs/vue3';
const page = usePage();

const Notificationscount=computed(()=>page.props.Notificationscount);

const props = defineProps({
    iconString: String,
})

const { iconString } = toRefs(props)

let icon = null

if (iconString.value === 'Home') icon = Home
if (iconString.value === 'Explore') icon = Pound
if (iconString.value === 'Notifications') icon = BellOutline
if (iconString.value === 'Messages') icon = EmailOutline
if (iconString.value === 'Profile') icon = AccountOutline
</script>