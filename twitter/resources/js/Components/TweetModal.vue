<script setup>
import { ref, inject, computed, watch, } from 'vue';
import Close from 'vue-material-design-icons/Close.vue';
import ImageOutline from 'vue-material-design-icons/ImageOutline.vue';
import Emoticon from 'vue-material-design-icons/Emoticon.vue';
import { useForm, usePage } from '@inertiajs/vue3'
const edit = ref(inject('edit'));
const tweetsidebtn = ref(inject('tweetsidebtn'));
const EditTweet = ref(inject('EditTweet'));
const displayImage = ref(false);
const displayVideo = ref(false);
const selectedImage = ref(null)
const selectedVideo = ref(null)
const invalidUpload = ref(false)
const invalidUploadText = ref('')
const page = usePage();
const user = computed(() => page.props.auth.user);

watch(edit, (newValue) => {
  if (newValue) {
    if (EditTweet.value.image) {
      displayImage.value = true;
      selectedImage.value = EditTweet.value.image;

      displayVideo.value = false;
      selectedVideo.value = null;
    }
    else if (EditTweet.value.video) {
      displayVideo.value = true;
      selectedVideo.value = EditTweet.value.video;
      displayImage.value = false;
      selectedImage.value = null;

    }
  }
})

const closemodal = () => {
  tweetsidebtn.value = false;
  edit.value = false;
  displayImage.value = false;
  selectedImage.value = null;
  displayVideo.value = false;
  selectedVideo.value = null;
  EditTweet.value = null;
}

const isTweetSideBtnVisible = computed(() => {
  return tweetsidebtn.value === true;
});

let newTweet = useForm({
  text: "",
  image: null,
  video: null,
  hashtags:null,

});
watch(EditTweet, (newValue) => {
  EditTweetForm.text = newValue ? newValue.text : '';
});
let EditTweetForm = useForm(
  {
    text: '',
    image: null,
    video: null,
  hashtags:null,

  }
)
const UploadImageForLocalViewing = (file) => {
  displayImage.value = true;
  selectedImage.value = URL.createObjectURL(file)
}

const UploadVideoForLocalViewing = (file) => {
  displayVideo.value = true;
  selectedVideo.value = URL.createObjectURL(file);
}

const scanHashtags = (text) => {
  const hashtagRegex = /#[^\s#]+/g;
  const hashtags = text.match(hashtagRegex);
  return hashtags ? hashtags : [];
}
let EditTweetfunc = (id) => {
  tweetsidebtn.value = false;
  edit.value = false;
  EditTweetForm.hashtags= scanHashtags(EditTweetForm.text);
  EditTweetForm.post(`/edittweet/${id}`);
  displayImage.value = false;
  displayVideo.value = false;
  selectedVideo.value = null;
  selectedImage.value = null;
}
const UploadNewPhotoOrVideo = (event) => {
  const file = event.target.files[0];
  const filetype = file.type;
  if (filetype.includes('image')) {
    UploadImageForLocalViewing(file);
    EditTweetForm.image = file;
    EditTweetForm.video = null;

    invalidUpload.value = false;
    invalidUploadText.value = '';
  }

  else if (filetype.includes('video')) {
    UploadVideoForLocalViewing(file);
    EditTweetForm.video = file;
    EditTweetForm.image = null;

    invalidUpload.value = false;
    invalidUploadText.value = '';
  }
  else {
    invalidUpload.value = true;
    invalidUploadText.value = 'You can only Upload Picture or Video';
  }
}
let addNewTweet = () => {
  newTweet.hashtags= scanHashtags(newTweet.text);
  newTweet.post('/createtweet');
  displayImage.value = false;
  displayVideo.value = false;
  selectedVideo.value = null;
  selectedImage.value = null;
  tweetsidebtn.value = false;
  newTweet.text = ''
}
let UploadTweet = (event) => {
  const file = event.target.files[0];
  const filetype = file.type;
  if (filetype.includes('image')) {
    UploadImageForLocalViewing(file);
    newTweet.image = file;
    newTweet.video = null;
    invalidUpload.value = false;
    invalidUploadText.value = '';
  }

  else if (filetype.includes('video')) {
    UploadVideoForLocalViewing(file);
    newTweet.video = file;
    newTweet.image = null;
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
    selectedImage.value = null;
    displayImage.value = false;

  }
  else if (selectedVideo.value) {
    console.log(selectedVideo.value);
    selectedVideo.value = null;
    displayVideo.value = false;

  }
}
</script>
<template>
  <div v-if="tweetsidebtn || edit"
    class=" fixed top-10 left-0 right-0 flex items-center justify-center z-50  hover:scale-105 ease-in duration-300">
    <div class="bg-black rounded border border-gray-800 w-96 lg:w-1/2 lg:m-10">
      <button class="cursor-pinter p-2" @click="closemodal">
        <Close size=20 fillColor="white" />
      </button>
      <div class="px-5 py-3 border-gray-800 border-b flex  bg-black ">
        <div class="lg:flex-none hidden lg:block md:mr-4">
          <img :src="user.profile" class="w-12 h-12 rounded-full border border-lighter" />
        </div>
        <form v-on:submit.prevent="EditTweetfunc(EditTweet.id)" v-if="edit" class="w-full px-4 relative">
          <textarea v-model="EditTweetForm.text" v-text="EditTweet.text"
            class="mt-3 pb-2 bg-black text-white w-full focus:outline-none" :rows="edit ? 2 : 5" required minlength="3"
            autofocus />
          <div v-if="EditTweetForm.errors.text" v-text="EditTweetForm.errors.text" class="text-red-500 text-xs mt-1">
          </div>
        <div v-if="invalidUpload" v-text="invalidUploadText" class="text-red-600 text-xs"></div>

          <svg v-if="displayImage || displayVideo" @click.stop="deleteImageOrVideo" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white  -ml-4 hover:bg-gray-600 rounded-full">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
          <div v-if="displayImage" class="flex justify-center">
            <div class="h-52 w-52 md:h-80 md:w-80">
              <img :src="selectedImage" class="rounded-xl max-h-full max-w-full object-contain" />
            </div>
          </div>

          <div v-if="displayVideo" class="flex justify-center">
            <div class="h-52 w-52 md:h-80 md:w-80">

              <video :src="selectedVideo" controls class="rounded-xl max-h-full max-w-full object-contain" />
            </div>
          </div>
          <div class="flex items-center gap-8  border-t mt-4 p-4 border-gray-800">
            <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">
              <label for="fileUpload" class="cursor-pointer">
                <ImageOutline fillColor="#48C9B0" :size=22 class="cursor-pointer" />
              </label>
              <input type="file" id="fileUpload" class="hidden" @change="UploadNewPhotoOrVideo">
            </div>
            <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">
              <Emoticon fillColor="#48C9B0" :size=22 class="cursor-pointer" />
            </div>
          </div>

          <button :disabled="EditTweetForm.processing" type="submit"
            class="h-10 px-4 text-white font-semibold bg-[#48C9B0] hover:bg-[#78C9B0] focus:outline-none rounded-full absolute bottom-0 right-0">
            Edit Tweet
          </button>
        </form>
        <form v-if="tweetsidebtn" v-on:submit.prevent="addNewTweet" class="w-full px-4 relative">
          <textarea v-model="newTweet.text" placeholder="What's up?"
            class="mt-3 pb-3 bg-black text-white w-full focus:outline-none" :rows="tweetsidebtn ? 2 : 5" required
            minlength="3" autofocus />
          <div v-if="newTweet.errors.text" v-text="newTweet.errors.text" class="text-red-500 text-xs mt-1">
          </div>
          <svg v-if="displayImage || displayVideo" @click.stop="deleteImageOrVideo" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white  -ml-4 hover:bg-gray-600 rounded-full">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
          <div v-if="displayImage" class="flex justify-center">
            <div class="h-52 w-52 md:h-80 md:w-80">
              <img :src="selectedImage" class="rounded-xl max-h-full max-w-full object-contain" />
            </div>
          </div>

          <div v-if="displayVideo" class="flex justify-center">
            <div class="h-52 w-52 md:h-80 md:w-80">

              <video :src="selectedVideo" controls class="rounded-xl max-h-full max-w-full object-contain" />
            </div>
          </div>
          <div class="flex items-center gap-8  border-t mt-4 p-4 border-gray-800">
            <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">
              <label for="fileUpload" class="cursor-pointer">
                <ImageOutline fillColor="#48C9B0" :size=22 class="cursor-pointer" />
              </label>
              <input type="file" id="fileUpload" class="hidden" @change="UploadTweet">
            </div>
            <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">
              <Emoticon fillColor="#48C9B0" :size=22 class="cursor-pointer" />
            </div>
          </div>

          <button :disabled="newTweet.processing" type="submit"
            class="h-10 px-4 text-white font-semibold bg-[#48C9B0] hover:bg-[#78C9B0] focus:outline-none rounded-full absolute bottom-0 right-0">
            Tweet
          </button>
        </form>
      </div>


    </div>
  </div>
</template>

