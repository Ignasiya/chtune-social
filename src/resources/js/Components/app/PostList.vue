<script setup>
import PostItem from "@/Components/app/PostItem.vue";
import PostModal from "@/Components/app/PostModal.vue";
import {ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import AttachmentPreview from "@/Components/app/AttachmentPreview.vue";

const showEditModal = ref(false);
const showAttachmentModal = ref(false);
const editPost = ref({});
const previewAttachmentsPost = ref({});
const authUser = usePage().props.auth.user;

function openEditModal(post) {
    editPost.value = post;
    showEditModal.value = true;
}

function openAttachmentPreviewModal(post, index) {
    previewAttachmentsPost.value = {
        post,
        index
    };
    showAttachmentModal.value = true;
}

function onModalHide() {
    editPost.value = {
        id: null,
        body: '',
        user: authUser
    };
}

defineProps({
    posts: Array
});

</script>

<template>
    <div class="overflow-auto">

        <PostItem
            v-for="post of posts"
            :key="post.id"
            :post="post"
            @editClick="openEditModal"
            @attachmentClick="openAttachmentPreviewModal"/>

        <PostModal
            :post="editPost"
            v-model="showEditModal"
            @hide="onModalHide"/>

        <AttachmentPreview
            :attachments="previewAttachmentsPost.post?.attachments || []"
            v-model:index="previewAttachmentsPost.index"
            v-model="showAttachmentModal"/>
    </div>
</template>

<style scoped>

</style>
