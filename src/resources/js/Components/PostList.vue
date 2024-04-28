<script setup>
import PostItem from "@/Pages/Post/Partials/PostItem.vue";
import PostModal from "@/Pages/Post/Partials/PostModal.vue";
import {onMounted, ref, watch} from "vue";
import {usePage} from "@inertiajs/vue3";
import AttachmentPreview from "@/Components/AttachmentPreview.vue";
import axiosClient from "@/axiosClient.js";

const showEditModal = ref(false);
const showAttachmentModal = ref(false);
const editPost = ref({});
const previewAttachmentsPost = ref({});
const authUser = usePage().props.auth.user;
const loadMoreIntersect = ref(null);
const page = usePage();

const allPosts = ref({
    data: [],
    next: null
})

const props = defineProps({
    posts: Array
});

watch(() => page.props.posts, () => {
    if (page.props.posts) {
        allPosts.value = {
            data: page.props.posts.data,
            next: page.props.posts.links.next
        }
    }
}, {deep: true, immediate: true});

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => entries.forEach(entry => entry.isIntersecting && loadMore()), {
            rootMargin: '-250px 0px 0px 0px'
        })
    observer.observe(loadMoreIntersect.value)
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

</script>

<template>
    <div class="overflow-auto">

        <PostItem
            v-for="post of allPosts.data"
            :key="post.id"
            :post="post"
            @editClick="openEditModal"
            @attachmentClick="openAttachmentPreviewModal"/>

        <div v-if="!posts.length" class="py-8 text-gray-600 dark:text-gray-300 text-center">
            Стена пустая, подпищитесь на кого-нибудь
        </div>

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
