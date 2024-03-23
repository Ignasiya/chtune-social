<script setup>

import {ref} from "vue";
import PostModal from "@/Components/app/PostModal.vue";
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
    <div class="p-4 bg-white rounded-lg border mb-3">
        <div
            @click="showCreatePostModal"
            class="py-2 px-3 border-2 border-gray-200 text-gray-500 rounded-md mb-3 w-full">
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
