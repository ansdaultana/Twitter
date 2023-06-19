<script setup>

import { ref, inject, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const pic = computed(() => page.props.profilePic);
const tweetsidebtn = ref(inject('tweetsidebtn'));

</script>
