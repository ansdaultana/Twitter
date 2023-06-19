<script setup>
import { ref, inject, computed, defineProps } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3'

import ImageOutline from 'vue-material-design-icons/ImageOutline.vue';
import FileGifBox from 'vue-material-design-icons/FileGifBox.vue';
import Emoticon from 'vue-material-design-icons/Emoticon.vue';

const props = defineProps({
    heading: {
        type: String,
        required: true
    },
    BtnText: {
        type: String,
        required: true
    },
    id: {
        type: String,
        required: true
    }
});
const pic = computed(() => page.props.profilePic);
const displayImage = ref(false);
const displayVideo = ref(false);
const selectedImage = ref(null)
const selectedVideo = ref(null)
const invalidUpload = ref(false)
const invalidUploadText = ref('')


const page = usePage();
const tweetsidebtn = ref(inject('tweetsidebtn'));
const user = computed(() => page.props.auth.user);
let newTweet = useForm({
    text: ""
});
let ReplyForm = useForm({
    text: ""
});

let addNewTweet = () => {
    newTweet.post('/createtweet');
    newTweet.text = ''
}
const postReply = (id) => {
    console.log(id)
    ReplyForm.post(`/replytweet/${id}`);
    ReplyForm.text = ''
}
const VisitProfile = (username) => {
    if (window.location.pathname !== `/users/${username}`) {
        router.get(`/users/${username}`);

    }
}

const UploadImage = (file) => {
    displayImage.value = true;
    selectedImage.value = file
}

const UploadVideo = (file) => {
    selectedVideo.value = file;
    displayVideo.value = true;
}

const selectedImageUrl = computed(() => {
    if (selectedImage.value) {
        return URL.createObjectURL(selectedImage.value);
    }
    return null;
})

const selectedVideoUrl = computed(() => {
    if (selectedVideo.value) {
        return URL.createObjectURL(selectedVideo.value);

    }
    return null;
});
let Upload = (event) => {
    const file = event.target.files[0];
    const filetype = file.type;
    if (filetype.includes('image')) {

        UploadImage(file);

        invalidUpload.value = false;
        invalidUploadText.value = '';
    }

    else if (filetype.includes('video')) {
        UploadVideo(file);

        invalidUpload.value = false;
        invalidUploadText.value = '';
    }
    else {
        invalidUpload.value = true;
        invalidUploadText.value = 'You can only Upload Picture or Video';
    }

};
const deleteImageOrVideo = () => {

    if (selectedImage.value) {
        selectedImage.value=null;
        displayImage.value=false;
        
    }
    else if (selectedVideo.value) {
        selectedVideo.value=null;
        displayVideo.value=false;
        
    }
}
</script>


<template>
    <div name="modal" v-if="tweetsidebtn === false">
        <div class="px-5 py-3 border-gray-800 border-b flex">
            <div class="flex-none">

                <img @click="VisitProfile(user.username)"
                    src="https://media.licdn.com/dms/image/C4D03AQHySl-ZFgyOfg/profile-displayphoto-shrink_400_400/0/1655959852960?e=1691020800&v=beta&t=YOs9sUi06NTkbFEsNz90qPTtNLRf1lZPaGVyXSXZg9A"
                    class="flex-none w-12 h-12 rounded-full border border-lighter cursor-pointer" />
            </div>
            <form v-if="props.BtnText === 'Tweet'" v-on:submit.prevent="addNewTweet" class="w-full px-4 relative">
                <textarea v-model="newTweet.text" :placeholder=props.heading
                    class="mt-3  pb-3 bg-black text-white w-full focus:outline-none" required minlength="3" />

                <div v-if="newTweet.errors.text" v-text="newTweet.errors.text" class="text-red-500 text-xs mt-1">

                </div>

                <svg v-if="displayImage || displayVideo" @click.stop="deleteImageOrVideo" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor"
                    class="w-6 h-6 text-white m-1 -ml-4 hover:bg-gray-600 rounded-full">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <img v-if="displayImage" :src='selectedImageUrl' />
                <video v-if="displayVideo" :src="selectedVideoUrl" controls />
                <div v-if="invalidUpload" v-text="invalidUploadText" class="text-red-600 text-xs"></div>


                <div class="flex items-center gap-8 border-t mt-4 p-4 border-gray-800 ">
                    <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">
                        <label for="fileUpload" class="cursor-pointer">
                            <ImageOutline fillColor="#48C9B0" :size=22 class="cursor-pointer" />

                        </label>
                        <input type="file" id="fileUpload" class="hidden" @change="Upload">
                    </div>

                    <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">

                        <Emoticon fillColor="#48C9B0" :size=22 class="cursor-pointer" />
                    </div>

                </div>

                <button type="submit" :disabled="newTweet.processing"
                    class="h-10 px-4 text-white font-semibold bg-[#48C9B0] hover:bg-[#78C9B0] focus:outline-none rounded-full absolute bottom-0 right-0">
                    {{ props.BtnText }}
                </button>
            </form>
            <form v-if="props.BtnText === 'Reply'" v-on:submit.prevent="postReply(props.id)" class="w-full px-4 relative">
                <textarea v-model="ReplyForm.text" :placeholder=props.heading
                    class="mt-3  pb-3 bg-black text-white w-full focus:outline-none" required minlength="3" />

                <div v-if="ReplyForm.errors.text" v-text="ReplyForm.errors.text" class="text-red-500 text-xs mt-1">

                </div>
                <svg v-if="displayImage || displayVideo" @click.stop="deleteImageOrVideo" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor"
                class="w-6 h-6 text-white m-1 -ml-4 hover:bg-gray-600 rounded-full">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <img v-if="displayImage" :src='selectedImageUrl' />
            <video v-if="displayVideo" :src="selectedVideoUrl" controls />
            <div v-if="invalidUpload" v-text="invalidUploadText" class="text-red-600 text-xs"></div>

                <div class="flex items-center gap-8 border-t mt-4 p-4 border-gray-800 ">
                    <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">
                        <label for="fileUpload" class="cursor-pointer">
                            <ImageOutline fillColor="#48C9B0" :size=22 class="cursor-pointer" />

                        </label>
                        <input type="file" id="fileUpload" class="hidden" @change="Upload">

                    </div>
                    
                    <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">
                        <Emoticon fillColor="#48C9B0" :size=22 class="cursor-pointer" />
                    </div>
                </div>
                <button type="submit" :disabled="ReplyForm.processing"
                    class="h-10 px-4 text-white font-semibold bg-[#48C9B0] hover:bg-[#78C9B0] focus:outline-none rounded-full absolute bottom-0 right-0">
                    {{ props.BtnText }}
                </button>
            </form>
        </div>
    </div>
</template>