<script setup>
import {ChatBubbleLeftRightIcon, HandThumbUpIcon} from '@heroicons/vue/24/solid';
import ReadMoreReadLess from "@/Components/app/ReadMoreReadLess.vue";
import IndigoButton from "@/Components/app/IndigoButton.vue";
import TextareaInput from "@/Components/TextareaInput.vue";
import Dropdown from "@/Components/app/PostDropdown.vue";
import {usePage, Link} from "@inertiajs/vue3";
import {ref} from "vue";
import axiosClient from "@/axiosClient.js";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";

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
            props.data.comments.unshift(data);
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
    <div class="flex gap-2 mb-3">
        <Link :href="route('profile', authUser.username)">
            <img
                :src="authUser.avatar_url"
                alt="avatar"
                class="w-[40px] rounded-full border-2 transition-all hover:border-blue-500"/>
        </Link>
        <div class="flex flex-1">
            <TextareaInput
                v-model="newCommentText"
                placeholder="Введите ваш комментарий"
                class="w-full max-h-[160px] rounded-r-none resize-none"
                rows="1"/>
            <IndigoButton @click="createComment"
                          class="w-[100px] rounded-l-none">Отправить
            </IndigoButton>
        </div>
    </div>
    <div>
        <div
            class="mb-4"
            v-for="comment of data.comments"
            :key="comment.id">
            <div class="flex justify-between gap-2">
                <div class="flex gap-2">
                    <Link :href="route('profile', comment.user.username)">
                        <img
                            :src="comment.user.avatar_url"
                            alt="avatar"
                            class="w-[40px] rounded-full border-2 transition-all hover:border-blue-500"/>
                    </Link>
                    <div>
                        <h4 class="font-bold">
                            <Link :href="route('profile', comment.user.username)" class="hover:underline">
                                {{ comment.user.name }}
                            </Link>
                        </h4>
                        <small class="text-xs text-gray-400">{{ comment.updated_at }}</small>
                    </div>
                </div>
                <Dropdown
                    :comment="comment"
                    :post="post"
                    @edit="editComment(comment)"
                    @delete="deleteComment(comment)"/>
            </div>
            <div class="pl-12">
                <div v-if="editingComment && editingComment.id === comment.id">
                    <TextareaInput
                        v-model="editingComment.comment"
                        class="w-full max-h-[160px] resize-none"
                        rows="1"/>
                    <div class="flex gap-2 justify-end">
                        <button
                            @click="editingComment = null"
                            class="rounded-r-none text-indigo-500">
                            отмена
                        </button>
                        <IndigoButton
                            @click="updateComment"
                            class="w-[100px]">
                            изменить
                        </IndigoButton>
                    </div>
                </div>
                <ReadMoreReadLess
                    v-else
                    :content="comment.comment"
                    content-class="text-sm flex flex-1"/>
                <Disclosure>
                    <div class="mt-1 flex gap-2">
                        <button
                            @click="sendCommentReaction(comment)"
                            :class="[
                                comment.current_user_has_reaction ?
                                'bg-indigo-50 hover:bg-indigo-100' :
                                'hover:bg-indigo-50'
                            ]"
                            class="flex items-center text-xs text-indigo-500 py-0.5 px-1 rounded-lg">
                            <HandThumbUpIcon class="w-3 h-3 mr-1"/>
                            <span class="mr-2">{{ comment.num_of_reactions }}</span>
                            {{ comment.current_user_has_reaction ? 'не нравится' : 'нравится' }}
                        </button>
                        <DisclosureButton
                            class="flex items-center text-xs text-indigo-500 py-0.5 px-1 hover:bg-indigo-100 rounded-lg">
                            <ChatBubbleLeftRightIcon class="w-3 h-3 mr-1"/>
                            <span class="mr-2">{{ comment.num_of_comments }}</span>
                            комментарии
                        </DisclosureButton>
                    </div>
                    <DisclosurePanel class="mt-3">
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
    </div>
</template>

<style scoped>

</style>
