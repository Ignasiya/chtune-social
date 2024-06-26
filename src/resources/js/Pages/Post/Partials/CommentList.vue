<script setup>
import {ChatBubbleLeftRightIcon, HandThumbUpIcon} from '@heroicons/vue/24/solid';
import {PaperAirplaneIcon, XMarkIcon} from '@heroicons/vue/24/solid';
import ReadMoreReadLess from "@/Pages/Post/Partials/ReadMoreReadLess.vue";
import TextareaInput from "@/Components/TextareaInput.vue";
import PostDropdown from "@/Pages/Post/Partials/PostDropdown.vue";
import {usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import axiosClient from "@/axiosClient.js";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import UserHeader from "@/Components/UserHeader.vue";
import PostUserHeader from "@/Pages/Post/Partials/PostUserHeader.vue";

const authUser = usePage().props.auth.user;

const newCommentText = ref('');
const editingComment = ref({});

const props = defineProps({
    post: Object,
    data: Object,
    parentComment: {
        type: [Object, null],
        default: null
    },
});

const emit = defineEmits(['commentCreate', 'CommentDelete'])

function editComment(comment) {
    editingComment.value = {
        id: comment.id,
        comment: comment.comment.replace(/<br\s*\/?>/gi, '\n')
    };
}

function createComment() {
    axiosClient.post(route('post.comment.create', props.post), {
        comment: newCommentText.value,
        parent_id: props.parentComment?.id || null
    })
        .then(({data}) => {
            newCommentText.value = '';
            props.data.comments.push(data);
            if (props.parentComment) {
                props.parentComment.num_of_comments++;
            }
            props.post.num_of_comments++;
            emit('commentCreate', data)
        })
}

function deleteComment(comment) {
    if (window.confirm('Вы уверены что хотите удалить этот комментарий?')) {
        axiosClient.delete(route('comment.delete', comment.id))
            .then(() => {
                const commentIndex = props.data.comments.findIndex(c => c.id === comment.id);
                props.data.comments.splice(commentIndex, 1)
                if (props.parentComment) {
                    props.parentComment.num_of_comments--;
                }
                props.post.num_of_comments--;
                emit('commentDelete', comment)
            })
    }
}

function updateComment() {
    axiosClient.put(route('comment.update', editingComment.value.id), editingComment.value)
        .then(({data}) => {
            editingComment.value = null;
            props.data.comments = props.data.comments.map((c) => {
                if (c.id === data.id) {
                    return data
                }
                return c;
            })
        })
}

function sendCommentReaction(comment) {
    axiosClient.post(route('comment.reaction', comment.id), {
        reaction: 'like',
    })
        .then(({data}) => {
            comment.current_user_has_reaction = data.current_user_has_reaction;
            comment.num_of_reactions = data.num_of_reactions;
        })
}

function onCommentCreate(comment) {
    if (props.parentComment) {
        props.parentComment.num_of_comments++
    }
    emit('commentCreate', comment)
}

function onCommentDelete(comment) {
    if (props.parentComment) {
        props.parentComment.num_of_comments--
    }
    emit('commentDelete', comment)
}

</script>

<template>
    <div class="flex flex-col gap-3">
        <div v-for="comment of data.comments" class="flex gap-2" :key="comment.id">

            <UserHeader class="my-2" :user="comment.user"/>

            <div class="flex flex-col gap-1 flex-1">
                <div class="flex flex-col gap-2 bg-sky-100 dark:bg-neutral-700 rounded-[20px] p-3">
                    <div class="flex justify-between">
                        <PostUserHeader :avatar="true" :post="comment"/>

                        <PostDropdown
                            :comment="comment"
                            :post="post"
                            @edit="editComment(comment)"
                            @delete="deleteComment(comment)"/>
                    </div>

                    <div class="flex" v-if="editingComment && editingComment.id === comment.id">
                        <TextareaInput
                            v-model="editingComment.comment"
                            class="w-full max-h-[100px] rounded-r-none resize-none"
                            rows="1"/>
                        <button
                            @click="editingComment = null"
                            class="flex items-center p-2 bg-gray-300 hover:bg-gray-200 dark:bg-neutral-600 dark:hover:bg-neutral-500 text-sky-600 hover:text-sky-700">
                            <XMarkIcon class="w-6 h-6"/>
                        </button>
                        <button
                            class="flex items-center rounded-full p-2 bg-sky-700 hover:bg-sky-600 text-white hover:text-gray-100 rounded-l-none"
                            @click="updateComment">
                            <PaperAirplaneIcon class="w-6 h-6"/>
                        </button>
                    </div>

                    <ReadMoreReadLess
                        v-else
                        :content="comment.comment"
                        content-class="text-sm flex flex-1"/>
                </div>

                <Disclosure>
                    <div class="mt-1 flex gap-2">
                        <button
                            @click="sendCommentReaction(comment)"
                            :class="[
                                comment.current_user_has_reaction ?
                                'bg-sky-50 hover:bg-sky-100 dark:bg-neutral-600 dark:hover:bg-neutral-700' :
                                'hover:bg-sky-100 dark:hover:bg-neutral-700'
                            ]"
                            class="flex items-center text-xs text-sky-500 py-0.5 p-2 rounded-full">
                            <HandThumbUpIcon class="w-4 h-4"/>
                            <span v-if="comment.num_of_reactions > 0" class="mx-1">{{
                                    comment.num_of_reactions
                                }}</span>
                        </button>
                        <DisclosureButton
                            class="flex items-center text-xs text-sky-500 py-0.5 p-2 hover:bg-sky-100 dark:hover:bg-neutral-700 rounded-full">
                            <ChatBubbleLeftRightIcon class="w-4 h-4"/>
                            <span v-if="comment.num_of_comments > 0" class="mx-1">{{
                                    comment.num_of_comments
                                }}</span>
                        </DisclosureButton>
                    </div>
                    <DisclosurePanel class="ml-[-30px] mt-1 text-sm">
                        <CommentList
                            :post="post"
                            :data="{comments: comment.comments}"
                            :parent-comment="comment"
                            @comment-create="onCommentCreate"
                            @comment-delete="onCommentDelete"/>
                    </DisclosurePanel>
                </Disclosure>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <UserHeader :user="authUser" :link="true"/>
            <div class="flex flex-1">
                <TextareaInput
                    v-model="newCommentText"
                    placeholder="Введите ваш комментарий"
                    class="w-full max-h-[150px] rounded-r-none resize-none"
                    rows="1"/>
                <button
                    class="flex items-center rounded-full p-2 bg-sky-700 hover:bg-sky-600 text-white hover:text-gray-100 rounded-l-none"
                    @click="createComment">
                    <PaperAirplaneIcon class="w-6 h-6"/>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
