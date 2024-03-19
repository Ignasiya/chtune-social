<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/vue'
import {HandThumbUpIcon, ChatBubbleLeftEllipsisIcon, ArrowDownTrayIcon, DocumentIcon, ChatBubbleLeftRightIcon} from
        '@heroicons/vue/24/solid'
import PostUserHeader from "@/Components/app/PostUserHeader.vue";
import {router, usePage} from "@inertiajs/vue3";
import {isImage} from '@/helpers.js'
import axiosClient from "@/axiosClient.js";
import TextareaInput from "@/Components/TextareaInput.vue";
import IndigoButton from "@/Components/app/IndigoButton.vue";
import {ref} from "vue";
import ReadMoreReadLess from "@/Components/app/ReadMoreReadLess.vue";
import Dropdown from "@/Components/app/Dropdown.vue";

const authUser = usePage().props.auth.user;
const editingComment = ref(null);
const props = defineProps({
    post: Object
});

const newCommentText = ref('');

const emit = defineEmits(['editClick', 'attachmentClick'])

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

function createComment() {
    axiosClient.post(route('post.comment.create', props.post), {
        comment: newCommentText.value,
    })
        .then(({data}) => {
            newCommentText.value = '';
            props.post.comments.unshift(data);
            props.post.num_of_comments++;
        })
}

function editComment(comment) {
    editingComment.value = {
        id: comment.id,
        comment: comment.comment.replace(/<br\s*\/?>/gi, '\n')
    };
}

function deleteComment(comment) {
    if (window.confirm('Вы уверены что хотите удалить этот комментарий?')) {
        axiosClient.delete(route('comment.delete', comment.id))
            .then(() => {
                props.post.comments = props.post.comments.filter(c => c.id !== comment.id);
                props.post.num_of_comments--;
            })
    }
}

function updateComment() {
    axiosClient.put(route('comment.update', editingComment.value.id), editingComment.value)
        .then(({data}) => {
            editingComment.value = null;
            props.post.comments = props.post.comments.map((c) => {
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

</script>

<template>
    <div class="bg-white border rounded p-4 mb-3">
        <div class="flex justify-between items-center mb-3">
            <PostUserHeader :post="post"/>
            <Dropdown :user="post.user" @edit="openEditModal" @delete="deletePost"/>
        </div>
        <div class="mb-3">
            <ReadMoreReadLess :content="post.body"/>
        </div>
        <div class="grid gap-3 mb-3" :class="[
            post.attachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2'
        ]">
            <template v-for="(attachment, ind) of post.attachments.slice(0, 4)">
                <div
                    @click="openAttachment(ind)"
                    class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative cursor-pointer">

                    <div
                        v-if="ind === 3 && post.attachments.length > 4"
                        class="absolute flex items-center justify-center z-10 left-0 top-0 right-0 bottom-0 z-10 bg-black/60 text-white">
                        +{{ post.attachments.length - 4 }} ещё
                    </div>

                    <!--Download-->
                    <a
                        @click.stop
                        :href="route('post.download', attachment)"
                        class="opacity-0 z-20 group-hover:opacity-100 transition-all w-8 h-8 flex items-center justify-center text-gray-100 bg-gray-700 rounded absolute right-2 top-2 cursor-pointer hover:bg-gray-800">
                        <ArrowDownTrayIcon class="w-4 h-4"/>
                    </a>
                    <!--/ Download-->

                    <img v-if="isImage(attachment)"
                         :src="attachment.url"
                         class="object-contain aspect-square"
                         alt="image"/>
                    <div
                        v-else
                        class="flex flex-col justify-center items-center">
                        <DocumentIcon class="w-12 h-12"/>
                        <small>{{ attachment.name }}</small>
                    </div>
                </div>
            </template>
        </div>
        <Disclosure v-slot="{ open }">
            <div class="flex gap-2">
                <button
                    @click="sendReaction"
                    class="text-gray-800 flex gap-1 items-center justify-center py-2 rounded-lg px-4 flex-1"
                    :class="[
                    post.current_user_has_reaction ?
                    'bg-sky-100 hover:bg-sky-200' :
                    'bg-gray-100 hover:bg-gray-200'
                ]"
                >
                    <HandThumbUpIcon class="w-6 h-6"/>
                    <span class="mr-2">{{ post.num_of_reactions }}</span>
                    {{ post.current_user_has_reaction ? 'Не нравится' : 'Нравится' }}
                </button>
                <DisclosureButton
                    class="text-gray-800 flex gap-1 items-center justify-center py-2 bg-gray-100 rounded-lg hover:bg-gray-200 px-4 flex-1"
                >
                    <ChatBubbleLeftEllipsisIcon class="w-6 h-6"/>
                    <span class="mr-2">{{ post.num_of_comments }}</span>
                    Комментарии
                </DisclosureButton>
            </div>
            <DisclosurePanel class="mt-3">
                <div class="flex gap-2 mb-3">
                    <a href="javascript:void(0)">
                        <img
                            :src="authUser.avatar_url || '/image/no-avatar.png'"
                            alt="avatar"
                            class="w-[40px] rounded-full border-2 transition-all hover:border-blue-500"/>
                    </a>
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
                        v-for="comment of post.comments"
                        :key="comment.id">
                        <div class="flex justify-between gap-2">
                            <div class="flex gap-2">
                                <a href="javascript:void(0)">
                                    <img
                                        :src="comment.user.avatar_url || '/image/no-avatar.png'"
                                        alt="avatar"
                                        class="w-[40px] rounded-full border-2 transition-all hover:border-blue-500"/>
                                </a>
                                <div>
                                    <h4 class="font-bold">
                                        <a href="javascript:void(0)" class="hover:underline">{{ comment.user.name }}</a>
                                    </h4>
                                    <small class="text-xs text-gray-400">{{ comment.updated_at }}</small>
                                </div>
                            </div>
                            <Dropdown
                                :user="comment.user"
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
                                <button
                                    class="flex items-center text-xs text-indigo-500 py-0.5 px-1 hover:bg-indigo-100 rounded-lg">
                                    <ChatBubbleLeftRightIcon class="w-3 h-3 mr-1"/>
                                    ответить
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </DisclosurePanel>
        </Disclosure>
    </div>
</template>

<style scoped>

</style>
