<script setup>

import {ref} from "vue";
import PostModal from "@/Pages/Post/Partials/PostModal.vue";
import {usePage} from "@inertiajs/vue3";
import UserHeader from "@/Components/UserHeader.vue";

const authUser = usePage().props.auth.user;

const newPost = ref({
    id: null,
    body: '',
    user: authUser
});

defineProps({
    group: {
        type: Object,
        default: null
    }
})

const showModal = ref(false);

function showCreatePostModal() {
    showModal.value = true;
}

</script>

<template>
    <div
        class="p-4 flex items-center gap-3 bg-white dark:bg-neutral-800 rounded-[20px] border dark:border-neutral-900 mb-3">
        <UserHeader :user="authUser" />
        <div
            @click="showCreatePostModal"
            class="py-2 px-3 border-2 dark:bg-neutral-900 border-gray-200 dark:border-neutral-700 text-gray-500 rounded-[40px] w-full">
            <p class="ml-3">Что нового?</p>
        </div>

        <PostModal
            :group="group"
            :post="newPost"
            v-model="showModal"
        />

    </div>
</template>

<style scoped>

</style>
