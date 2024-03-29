<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/vue'
import {ChatBubbleLeftEllipsisIcon, HandThumbUpIcon} from '@heroicons/vue/24/solid'
import PostUserHeader from "@/Pages/Post/Partials/PostUserHeader.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import axiosClient from "@/axiosClient.js";
import ReadMoreReadLess from "@/Pages/Post/Partials/ReadMoreReadLess.vue";
import PostDropdown from "@/Pages/Post/Partials/PostDropdown.vue";
import PostAttachments from "@/Pages/Post/Partials/PostAttachments.vue";
import CommentList from "@/Pages/Post/Partials/CommentList.vue";
import {computed} from "vue";
import UrlPreview from "@/Pages/Post/Partials/UrlPreview.vue";
import {BookmarkIcon} from "@heroicons/vue/20/solid/index.js";

const props = defineProps({
    post: Object
});

const group = usePage().props.group;
const authUser = usePage().props.auth.user;

const emit = defineEmits(['editClick', 'attachmentClick'])

const postBody = computed(() => {
    return props.post.body.replace(
        /(?:(\s+)|<p>)((#\w+)(?![^<]*<\/a>))/g,
        (match, space, word) => {
            const encode = encodeURIComponent(word);
            return `${space || ''}<a class="hashtag" href="/search/${encode}">${word}</a>`;
        });
});

const isPinned = computed(() => {
    if (group?.id) {
        return group?.pinned_post_id === props.post.id;
    }
    return authUser?.pinned_post_id === props.post.id;
})

function openEditModal() {
    emit('editClick', props.post);
}

function deletePost() {
    if (window.confirm('Вы уверены что хотите удалить этот пост?')) {
        router.delete(route('post.destroy', props.post), {
            preserveScroll: true
        });
    }
}

function openAttachment(index) {
    emit('attachmentClick', props.post, index)
}

function sendReaction() {
    axiosClient.post(route('post.reaction', props.post), {
        reaction: 'like',
    })
        .then(({data}) => {
            props.post.current_user_has_reaction = data.current_user_has_reaction;
            props.post.num_of_reactions = data.num_of_reactions;
        })
}

function pinUnpinPost() {
    const form = useForm({
        forGroup: group?.id
    });
    let isPinned = false;
    if (group?.id) {
        isPinned = group?.pinned_post_id === props.post.id
    } else {
        isPinned = authUser?.pinned_post_id === props.post.id
    }

    form.post(route('post.pinUnpin', props.post.id), {
        onSuccess: () => {
            if (group?.id) {
                group.pinned_post_id = isPinned ? null : props.post.id
            } else {
                authUser.pinned_post_id = isPinned ? null : props.post.id
            }
        }
    })
}

</script>

<template>
    <div class="bg-white border dark:bg-stone-800 dark:border-stone-900 dark:text-gray-300 rounded p-4 mb-3">
        <div class="flex justify-between items-center mb-3">
            <PostUserHeader :post="post"/>
            <div class="flex items-center gap-2">
                <div v-if="isPinned" class="flex items-center text-xs">
                    <BookmarkIcon
                        class="mr-1 h-3 w-3"
                        aria-hidden="true" />
                    закреплена
                </div>
                <PostDropdown :post="post" @edit="openEditModal" @pin="pinUnpinPost" @delete="deletePost"/>
            </div>
        </div>
        <div class="mb-3">
            <ReadMoreReadLess :content="postBody"/>
            <UrlPreview :preview="post.preview" :url="post.preview_url"/>
        </div>
        <div class="grid gap-3 mb-3" :class="[
            post.attachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2'
        ]">
            <PostAttachments :attachments="post.attachments" @attachmentClick="openAttachment"/>
        </div>
        <Disclosure v-slot="{ open }">
            <!-- Секция Лайков и Комментов -->
            <div class="flex gap-2">
                <button
                    @click="sendReaction"
                    class="text-gray-800 dark:text-gray-300 flex gap-1 items-center justify-center py-2 rounded-lg px-4 flex-1"
                    :class="[
                    post.current_user_has_reaction ?
                    'bg-sky-100 hover:bg-sky-200 dark:bg-sky-900 dark:hover:bg-sky-950' :
                    'bg-gray-100 dark:bg-stone-900 hover:bg-gray-200 dark:hover:bg-stone-950'
                ]"
                >
                    <HandThumbUpIcon class="w-6 h-6"/>
                    <span class="mr-2">{{ post.num_of_reactions }}</span>
                    {{ post.current_user_has_reaction ? 'Не нравится' : 'Нравится' }}
                </button>
                <DisclosureButton
                    class="text-gray-800 dark:text-gray-300 flex gap-1 items-center justify-center py-2 bg-gray-100 rounded-lg hover:bg-gray-200 px-4 flex-1 dark:bg-stone-900 dark:hover:bg-stone-950"
                >
                    <ChatBubbleLeftEllipsisIcon class="w-6 h-6"/>
                    <span class="mr-2">{{ post.num_of_comments }}</span>
                    Комментарии
                </DisclosureButton>
            </div>
            <DisclosurePanel class="mt-3 max-h-[400px] overflow-auto">
                <CommentList :post="post" :data="{comments: post.comments}"/>
            </DisclosurePanel>
        </Disclosure>
    </div>
</template>

