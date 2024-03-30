<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/vue'
import {HandThumbUpIcon as HandLike, BookmarkIcon as PinBook} from '@heroicons/vue/24/solid'
import {ChatBubbleOvalLeftIcon, BookmarkIcon as UnpinBook, HandThumbUpIcon as HandUnlike} from
        "@heroicons/vue/24/outline";
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
    post: {
        type: Object,
        default: null
    }
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

const pinAllowed = computed(() => {
    return props.post.user.id === authUser.id ||
        props.post.group && props.post.group.role === 'admin'
});

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
            <PostDropdown :post="post" @edit="openEditModal" @delete="deletePost"/>
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
            <div class="flex gap-2 items-center justify-between py-2 border-t border-gray-300 dark:border-neutral-700">

                <div class="flex gap-3 items-center">

                    <button
                        @click="sendReaction"
                        class="flex items-center rounded-full p-2 bg-gray-100 hover:bg-gray-200 dark:bg-neutral-700 dark:hover:bg-neutral-600"
                        :class="[
                            post.current_user_has_reaction ? 'text-sky-600 hover:text-sky-500' :
                            'text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200'
                        ]">
                        <HandLike v-if="post.current_user_has_reaction" class="h-6 w-6" />
                        <HandUnlike v-else class="h-6 w-6" />
                        <span v-if="post.num_of_reactions > 0" class="mx-1">{{ post.num_of_reactions }}</span>
                    </button>

                    <DisclosureButton
                        class="flex items-center rounded-full p-2 bg-gray-100 hover:bg-gray-200 dark:bg-neutral-700 dark:hover:bg-neutral-600 text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200">
                        <ChatBubbleOvalLeftIcon class="w-6 h-6"/>
                        <span v-if="post.num_of_comments > 0" class="mx-1">{{ post.num_of_comments }}</span>
                    </DisclosureButton>

                </div>

                <button
                    v-if="pinAllowed"
                    @click="pinUnpinPost"
                    class="flex items-center rounded-full p-2 bg-gray-100 hover:bg-gray-200 dark:bg-neutral-700 dark:hover:bg-neutral-600"
                    :class="[
                        isPinned ? 'text-sky-600 hover:text-sky-500' : 'text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200'
                    ]">
                    <PinBook v-if="isPinned" class="h-6 w-6" />
                    <UnpinBook v-else class="h-6 w-6" />
                </button>

            </div>
            <DisclosurePanel class="mt-3 max-h-[600px] overflow-auto">
                <CommentList :post="post" :data="{comments: post.comments}"/>
            </DisclosurePanel>
        </Disclosure>
    </div>
</template>

