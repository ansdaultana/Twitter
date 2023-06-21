<script setup>
import { ref, inject, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MenuItem from '@/Components/MenuItem.vue';
import Twitter from 'vue-material-design-icons/Twitter.vue';
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue';
import Feather from 'vue-material-design-icons/Feather.vue';
import { router } from '@inertiajs/vue3'

const page = usePage();
const dropdown = ref(false);
const tweetsidebtn = ref(inject('tweetsidebtn'));
const user = computed(() => page.props.auth.user);

const handleClick = () => {
  tweetsidebtn.value = !tweetsidebtn.value;
};

const logout = () => {
  dropdown.value = false;
  router.post(route('logout'));
};

const goToHome = () => {
  router.get('/home');
}



const goToExplore = () => {
  router.get('/explore');
}
const VisitProfile = (username) => {
  if (window.location.pathname !== `/${username}`) {
  
    router.get(`/${username}`);
  }
  dropdown.value = false
}
</script>

<template>
  <div name="sidebar" class="lg:w-1/5 px-2 lg:px-4 py-2 border-r border-gray-900 flex flex-col justify-between">
    <!-- Sidebar content -->
    <div>
      <button class="mt-5 lg:ml-3 hover:bg-gray-700 rounded-full p-2">
        <div @click="goToHome">
          <Twitter fillColor="#48C9B0" :size=35 />

        </div>
      </button>
      <div name="sidemenubuttons" class="mb-3 lg:ml-3">
        <!-- Sidebar menu items -->
        <MenuItem iconString="Home"  @click="goToHome"/>
        <button 
        @click.stop="goToExplore"
        class="text-white text-lg flex p-2 px-4 lg:pr-6  hover:bg-[#181818] font-bold  w-full  rounded-full cursor-pointer transition duration-200 ease-in-out">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-zinc font-bold">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
         <div class="ml-2 hidden lg:block">
          Search
         </div> 
        </button>
        <MenuItem iconString="Notifications" />
        <MenuItem iconString="Messages" />
        <MenuItem iconString="Profile" @click="VisitProfile(user.username)" />
        <button @click="handleClick"
          class="lg:w-40 mt-8 w-30 text-white font-extrabold text-[22px] hover:bg-[#71C9B0] bg-[#48C9B0] ml-1 p-2 px-2 rounded-full cursor-pointer">
          <span class="lg:block hidden">Tweet</span>
          <span class="block m-2 lg:hidden">
            <Feather />
          </span>
        </button>
      </div>
    </div>
    <div class="md:ml-3 lg:w-full relative mb-8">
      <div v-if="dropdown === true" class="border border-t border-d border-gray-600 rounded">
        <button @click="VisitProfile(user.username)"
          class="w-full hover:bg-gray-900 text-white text-left border-t border-gray-600 p-3 test-sm focus:outline-none">
          Visit Profile
        </button>

        <button @click="logout" type="submit"
          class="w-full text-white border-gray-600 text-left hover:bg-gray-900 border-t p-3 text-sm focus:outline-none">
          Log out {{ user.name }}
        </button>
      </div>
      <div class="hover:bg-[#181818] mt-2 lg:px-3 py-1 rounded-full lg:mr-8">
        <button class="flex items-center" @click="dropdown = !dropdown">

          <img
          :src="user.profile"
          class="w-12 h-12 border border-gray-800 rounded-full" alt="" />
          <div class="hidden lg:block ml-4">
            <p class="text-sm text-white font-bold leading-tight">{{ user.name }}</p>
            <div class="flex">
              <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mt-0.5 text-gray-400">
                <path stroke-linecap="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
              </svg>
              <p class="text-sm text-gray-400 leading-tight">
                
                {{ user.username }}</p>
            
            </div>
            </div>
          <DotsHorizontal fillColor="white" class="ml-6 hidden lg:block" size=24 />
        </button>
      </div>
    </div>
  </div>
</template>
