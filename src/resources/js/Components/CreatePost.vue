<script setup>

import {ref} from "vue";
import PostModal from "@/Pages/Post/Partials/PostModal.vue";
import {usePage} from "@inertiajs/vue3";

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
    <div class="p-4 bg-white dark:bg-stone-800 rounded-lg border dark:border-stone-900 mb-3">
        <div
            @click="showCreatePostModal"
            class="py-2 px-3 border-2 dark:bg-stone-900 border-gray-200 dark:border-stone-700 text-gray-500 rounded-md mb-3 w-full">
            Новая запись
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
