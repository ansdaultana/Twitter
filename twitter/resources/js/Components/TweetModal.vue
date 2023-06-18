<script setup>
import { ref, inject, computed, watch } from 'vue';
import Close from 'vue-material-design-icons/Close.vue';
import ImageOutline from 'vue-material-design-icons/ImageOutline.vue';
import FileGifBox from 'vue-material-design-icons/FileGifBox.vue';
import Emoticon from 'vue-material-design-icons/Emoticon.vue';
import { useForm } from '@inertiajs/vue3'
const edit = ref(inject('edit'));
const tweetsidebtn = ref(inject('tweetsidebtn'));
const EditTweet = ref(inject('EditTweet'));

const closemodal = () => {
  tweetsidebtn.value = false;
  edit.value = false;
  EditTweet.value = null;
}

const isTweetSideBtnVisible = computed(() => {
  return tweetsidebtn.value === true;
});



let newTweet = useForm({
  text: ""
});
watch(EditTweet, (newValue) => {
  EditTweetForm.text = newValue ? newValue.text : '';
});
let EditTweetForm = useForm(
  {
    text: '',
  }
)


let addNewTweet = () => {
  tweetsidebtn.value = !tweetsidebtn.value;

  newTweet.post('/createtweet');
  newTweet.text = ''
}
let EditTweetfunc = (id) => {
  tweetsidebtn.value = false;
  edit.value = false;
  EditTweetForm.post(`/edittweet/${id}`);
}
</script>

<template>
  <div v-if="tweetsidebtn || edit"
    class=" fixed top-10 left-0 right-0 flex items-center justify-center z-50  hover:scale-105 ease-in duration-300">
    <div class="bg-black rounded border border-gray-800 w-96 lg:w-1/2 m-10">
      <button class="cursor-pinter p-2 " @click="closemodal">
        <Close size=20 fillColor="white" />
      </button>
      <div class="px-5 py-3 border-gray-800 border-b flex  bg-black ">
        <div class="flex-none">
          <img
            src="https://media.licdn.com/dms/image/C4D03AQHySl-ZFgyOfg/profile-displayphoto-shrink_400_400/0/1655959852960?e=1691020800&v=beta&t=YOs9sUi06NTkbFEsNz90qPTtNLRf1lZPaGVyXSXZg9A"
            class="w-12 h-12 rounded-full border border-lighter" />
        </div>
        <button>
          <Close />
        </button>

        <form v-on:submit.prevent="EditTweetfunc(EditTweet.id)" v-if="edit" class="w-full px-4 relative">

          <textarea v-model="EditTweetForm.text" v-text="EditTweet.text"
            class="mt-3 pb-3 bg-black text-white w-full focus:outline-none" rows="5" required minlength="3" autofocus />


          <div v-if="EditTweetForm.errors.text" v-text="EditTweetForm.errors.text" class="text-red-500 text-xs mt-1">
          </div>

          <div class="flex items-center gap-8  border-t mt-4 p-4 border-gray-800">
            <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">
              <label for="fileUpload" class="cursor-pointer">
                <ImageOutline fillColor="#48C9B0" :size=22 class="cursor-pointer" />
              </label>
              <input type="file" id="fileUpload" class="hidden" @change="get">
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
            class="mt-3 pb-3 bg-black text-white w-full focus:outline-none" rows="5" required minlength="3" autofocus />
          <div v-if="newTweet.errors.text" v-text="newTweet.errors.text" class="text-red-500 text-xs mt-1">
          </div>

          <div class="flex items-center gap-8  border-t mt-4 p-4 border-gray-800">
            <div class="hover:bg-gray-800 cursor-pointer p-2 rounded-full">
              <label for="fileUpload" class="cursor-pointer">
                <ImageOutline fillColor="#48C9B0" :size=22 class="cursor-pointer" />
              </label>
              <input type="file" id="fileUpload" class="hidden" @change="get">
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

