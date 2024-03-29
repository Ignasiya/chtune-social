<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/vue'
import {ChatBubbleLeftEllipsisIcon, HandThumbUpIcon, BookmarkIcon as PinBook} from '@heroicons/vue/24/solid'
import {BookmarkIcon as UnpinBook} from "@heroicons/vue/24/outline";
import PostUserHeader from "@/Pages/Post/Partials/PostUserHeader.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import axiosClient from "@/axiosClient.js";
import ReadMoreReadLess from "@/Pages/Post/Partials/ReadMoreReadLess.vue";
import PostDropdown from "@/Pages/Post/Partials/PostDropdown.vue";
import PostAttachments from "@/Pages/Post/Partials/PostAttachments.vue";
import CommentList from "@/Pages/Post/Partials/CommentList.vue";
import {computed} from "vue";
import UrlPreview from "@/Pages/Post/Partials/UrlPreview.vue";

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
    <div
        class="bg-white border dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 rounded-[20px] p-4 mb-3 shadow-md">
        <div class="flex justify-between items-center mb-3">
            <PostUserHeader :post="post"/>
            <PostDropdown :post="post" @edit="openEditModal" @pin="pinUnpinPost" @delete="deletePost"/>
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
            <div class="flex gap-2 py-2 border-t-2 border-gray-300 dark:border-neutral-700">

            </div>
            <div class="flex gap-2 py-2 border-t-2 border-gray-300 dark:border-neutral-700">
                <button
                    @click="sendReaction"
                    class="text-gray-800 dark:text-gray-300 flex gap-1 items-center justify-center py-2 rounded-lg px-4 flex-1"
                    :class="[
                    post.current_user_has_reaction ?
                    'bg-sky-100 hover:bg-sky-200 dark:bg-sky-900 dark:hover:bg-sky-950' :
                    'bg-gray-100 dark:bg-neutral-900 hover:bg-gray-200 dark:hover:bg-neutral-950'
                ]"
                >
                    <HandThumbUpIcon class="w-6 h-6"/>
                    <span class="mr-2">{{ post.num_of_reactions }}</span>
                    {{ post.current_user_has_reaction ? 'Не нравится' : 'Нравится' }}
                </button>
                <DisclosureButton
                    class="text-gray-800 dark:text-gray-300 flex gap-1 items-center justify-center py-2 bg-gray-100 rounded-lg hover:bg-gray-200 px-4 flex-1 dark:bg-neutral-900 dark:hover:bg-neutral-950"
                >
                    <ChatBubbleLeftEllipsisIcon class="w-6 h-6"/>
                    <span class="mr-2">{{ post.num_of_comments }}</span>
                    Комментарии
                </DisclosureButton>
                <button
                    v-if="isPinned"
                    class="flex items-center rounded-full p-1.5 bg-gray-100 dark:bg-neutral-700 dark:hover:bg-neutral-800 hover:bg-gray-200 text-sky-600 hover:text-sky-500">
                    <PinBook class="h-5 w-5" />
                </button>
                <button
                    v-else
                    class="flex items-center rounded-full p-1.5 bg-gray-100 dark:bg-neutral-700 dark:hover:bg-neutral-800 hover:bg-gray-200 text-gray-600 hover:text-gray-500">
                    <UnpinBook class="h-5 w-5" />
                </button>
            </div>
            <DisclosurePanel class="mt-3 max-h-[400px] overflow-auto">
                <CommentList :post="post" :data="{comments: post.comments}"/>
            </DisclosurePanel>
        </Disclosure>
    </div>
</template>

