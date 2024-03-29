<script setup>
import {Link} from "@inertiajs/vue3";

defineProps({
    user: Object,
    link: {
        type: Boolean,
        default: false
    }
});

</script>

<template>
    <Link v-if="!link" :href="route('profile', user.username)">
        <img :src="user.avatar_url" alt="avatar"
             class="w-[40px] rounded-full border-2 transition-all hover:border-sky-500"/>
    </Link>
    <div v-else>
        <img :src="user.avatar_url" alt="avatar"
             class="w-[36px] rounded-full border-2 transition-all hover:border-sky-500"/>
    </div>
</template>

<style scoped>

</style>
