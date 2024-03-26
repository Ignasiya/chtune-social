<script setup>
import PostItem from "@/Pages/Post/PostItem.vue";
import {ref} from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, usePage} from "@inertiajs/vue3";
import PostModal from "@/Pages/Post/PostModal.vue";
import AttachmentPreview from "@/Components/AttachmentPreview.vue";

defineProps({
    post: Object
})

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

</script>

<template>
    <Head title="Запись"/>

    <AuthenticatedLayout>
        <div class="container py-4 px-2 max-w-[768px] mx-auto h-full overflow-auto">
            <PostItem
                :post="post"
                @editClick="openEditModal"
                @attachmentClick="openAttachmentPreviewModal"/>
        </div>

        <PostModal
            :post="editPost"
            v-model="showEditModal"
            @hide="onModalHide"/>

        <AttachmentPreview
            :attachments="previewAttachmentsPost.post?.attachments || []"
            v-model:index="previewAttachmentsPost.index"
            v-model="showAttachmentModal"/>

    </AuthenticatedLayout>
</template>

<style scoped>

</style>
