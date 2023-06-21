<script setup>
import { ref, inject, computed, watch, } from 'vue';
import Close from 'vue-material-design-icons/Close.vue';
import { useForm, usePage } from '@inertiajs/vue3'
const edit = ref(inject('edit'));
const tweetsidebtn = ref(inject('tweetsidebtn'));
const EditTweet = ref(inject('EditTweet'));

const page = usePage();
const user = computed(() => page.props.auth.user);

const selectedProfile = ref(null)
const selectedCover = ref(null)
const selectedName = ref('')

const invalidProfile = ref(false)
const invalidProfileText = ref('')

const invalidCover = ref(false)
const invalidCoverText = ref('')

const openEditProfile = ref(inject('openEditProfile'));

watch(openEditProfile, (newValue) => {
  selectedProfile.value = user.value.profile;
  selectedCover.value = user.value.cover;
  selectedName.value = user.value.name
})

const closemodal = () => {
  openEditProfile.value = false
  tweetsidebtn.value = false;
  edit.value = false;
  selectedProfile.value = null;
  selectedCover.value = null;
  EditTweet.value = null;
  selectedName.value = '';
}

let newData = useForm({
  name: '',
  profile: null,
  cover: null,

});
const PostNewData = () => {
  
  newData.name = selectedName.value;
  newData.post(`/updateprofile/${user.value.id}`);
  closemodal()
}

const UploadNewProfile = (event) => {
  const file = event.target.files[0];
  const filetype = file.type;
  if (filetype.includes('image')) {
    selectedProfile.value = URL.createObjectURL(file);
    newData.profile =file;
    invalidProfile.value = false;
    invalidProfileText.value = '';
  }
  else {
    invalidProfile.value = true;
    invalidProfileText.value = 'You can only Upload Pictures';
  }

}
const uploadNewCover = (event) => {
  const file = event.target.files[0];
  const filetype = file.type;
  if (filetype.includes('image')) {

    selectedCover.value = URL.createObjectURL(file)
    newData.cover =file;

    invalidCover.value = false;
    invalidCoverText.value = '';
  }
  else {
    invalidCover.value = true;
    invalidCoverText.value = 'You can only Upload Pictures';
  }
}

</script>
<template>
  <div v-if="openEditProfile"
    class=" fixed top-10 left-0 right-0 flex items-center justify-center z-50  hover:scale-105 ease-in duration-300">

    <div class="bg-black rounded border border-gray-800 w-auto  lg:m-10">
      <form v-on:submit.prevent="PostNewData">

        <div class="flex justify-between">
          <div class="flex">
            <button class="cursor-pinter p-3" @click="closemodal">
              <Close size=20 fillColor="white" />
            </button>
            <h2 class="text-white font-bold mt-2 text-lg">
              Edit Profile
            </h2>

          </div>
          <button type="submit" class="text-black bg-white rounded-full border-2 font-bold mt-3 mb-2 mr-2 px-2 text-lg">
            Save
          </button>
        </div>
        <div v-if="invalidCover" v-text="invalidCoverText" class="text-red-600 text-xs"></div>
        <div v-if="newData.errors.cover" v-text="newData.errors.cover" class="text-red-500 text-xs mt-1"></div>

        <div class="relative">
          <img name="cover" class="z-0 -mb-10 w-full h-56 " :src="selectedCover" alt="">
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="hover:bg-gray-400 bg-gray-200 cursor-pointer p-2 rounded-full">
              <label for="fileUpload" class="cursor-pointer">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
              </label>
              <input type="file" id="fileUpload" class="hidden" @change="uploadNewCover">
            </div>
          </div>
        </div>
        <div class="px-5 py-3 border-gray-800 border-b flex z-10 bg-black">
          <div class="relative">
            <img name="profile"
              class="w-32 h-32 mb-4 z-10 rounded-full -bottom-1 right-0 border-2 border-white bg-gray-900"
              :src="selectedProfile" alt="Profile Picture" />
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="hover:bg-gray-400 bg-gray-200 cursor-pointer p-2 rounded-full">
                <label for="fileUploadProfile" class="cursor-pointer">
                  <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                  </svg>
                </label>
                <input type="file" id="fileUploadProfile" class="hidden" @change="UploadNewProfile">
              </div>
            </div>
          </div>
        </div>
        <div v-if="invalidProfile" v-text="invalidProfileText" class="text-red-600 text-xs"></div>
        <div v-if="newData.errors.profile" v-text="newData.errors.profile" class="text-red-500 text-xs mt-1"></div>

        <div class="p-2 m-2 border-2 border-gray-600">
          <label for="name" class="text-gray-400 text-sm">Name:</label>
          <input type="text" v-model="selectedName"
            class="w-full bg-black text-white text-lg placeholder:text-white ring ring-transparent">
        </div>
        <div v-if="newData.errors.name" v-text="newData.errors.name" class="text-red-500 text-xs mt-1">
        </div>

      </form>

    </div>
  </div>
</template>

