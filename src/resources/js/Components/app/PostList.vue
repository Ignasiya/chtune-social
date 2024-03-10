<script setup>
import PostItem from "@/Components/app/PostItem.vue";
import {ref} from "vue";

const showEditModal = ref(false)
const editPost = ref({})

function openEditModal(post) {
    editPost.value = post;
    showEditModal.value = true;
}

defineProps({
    posts: Array
});

</script>

<template>
    <div class="overflow-auto">
        <PostItem v-for="post of posts" :key="post.id" :post="post" @editClick="openEditModal"/>
    </div>
</template>

<style scoped>

</style>
