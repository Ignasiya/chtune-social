<script setup>
import PostItem from "@/Components/app/PostItem.vue";
import PostModal from "@/Components/app/PostModal.vue";
import {onMounted, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import AttachmentPreview from "@/Components/app/AttachmentPreview.vue";
import axiosClient from "@/axiosClient.js";

const showEditModal = ref(false);
const showAttachmentModal = ref(false);
const editPost = ref({});
const previewAttachmentsPost = ref({});
const authUser = usePage().props.auth.user;
const loadMoreIntersect = ref(null);
const page = usePage();

const props = defineProps({
    posts: Array
});

const allPosts = ref({
    data: page.props.posts.data,
    next: page.props.posts.links.next
})

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

function loadMore() {
    if (!allPosts.value.next) {
        return;
    }

    axiosClient.get(allPosts.value.next)
        .then(({data}) => {
            allPosts.value.data = [...allPosts.value.data, ...data.data]
            allPosts.value.next = data.links.next
        })
}

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => entries.forEach(entry => entry.isIntersecting && loadMore()), {
            rootMargin: '-250px 0px 0px 0px'
        })
    observer.observe(loadMoreIntersect.value)
})

</script>

<template>
    <div class="overflow-auto">

        <PostItem
            v-for="post of allPosts.data"
            :key="post.id"
            :post="post"
            @editClick="openEditModal"
            @attachmentClick="openAttachmentPreviewModal"/>

        <div ref="loadMoreIntersect"></div>

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
