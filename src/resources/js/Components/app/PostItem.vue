<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/vue'
import {HandThumbUpIcon, ChatBubbleLeftEllipsisIcon, ArrowDownTrayIcon, DocumentIcon} from '@heroicons/vue/24/solid'
import {Menu, MenuButton, MenuItems, MenuItem} from '@headlessui/vue'
import {EllipsisVerticalIcon, PencilIcon, TrashIcon} from '@heroicons/vue/20/solid'
import PostUserHeader from "@/Components/app/PostUserHeader.vue";
import {router} from "@inertiajs/vue3";
import {isImage} from '@/helpers.js'

const props = defineProps({
    post: Object
});

const emit = defineEmits(['editClick'])

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

</script>

<template>
    <div class="bg-white border rounded p-4 mb-3">
        <div class="flex justify-between items-center mb-3">
            <PostUserHeader :post="post"/>
            <Menu as="div" class="relative inline-block text-left">
                <div>
                    <MenuButton
                        class="h-8 w-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                        <EllipsisVerticalIcon
                            class="h-5 w-5"
                            aria-hidden="true"/>
                    </MenuButton>
                </div>

                <transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <MenuItems
                        class="absolute right-0 mt-2 w-40 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
                    >
                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="openEditModal"
                                    :class="[
                                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                        ]"
                                >
                                    <PencilIcon
                                        class="mr-2 h-5 w-5"
                                        aria-hidden="true"
                                    />
                                    Редактировать
                                </button>
                            </MenuItem>
                        </div>
                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="deletePost"
                                    :class="[
                                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                        ]"
                                >
                                    <TrashIcon
                                        class="mr-2 h-5 w-5"
                                        aria-hidden="true"
                                    />
                                    Удалить
                                </button>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
        </div>
        <div class="mb-3">
            <Disclosure v-slot="{ open }">
                <div class="ck-content-output" v-if="!open" v-html="post.body.substring(0, 200)"/>
                <template v-if="post.body.length > 200">
                    <DisclosurePanel>
                        <div class="ck-content-output" v-html="post.body"/>
                    </DisclosurePanel>
                    <div class="flex justify-end">
                        <DisclosureButton class="text-blue-500 hover:underline">
                            {{ open ? 'Скрыть' : 'Показать ещё' }}
                        </DisclosureButton>
                    </div>
                </template>
            </Disclosure>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-3">
            <template v-for="attachment of post.attachments">
                <div
                    class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative">
                    <!--Download-->
                    <button
                        class="opacity-0 group-hover:opacity-100 transition-all w-8 h-8 flex items-center justify-center text-gray-100 bg-gray-700 rounded absolute right-2 top-2 cursor-pointer hover:bg-gray-800">
                        <ArrowDownTrayIcon class="w-4 h-4"/>
                    </button>
                    <!--/ Download-->

                    <img v-if="isImage(attachment)"
                         :src="attachment.url"
                         class="object-cover aspect-square"
                         alt="image"/>
                    <template v-else>
                        <DocumentIcon class="w-12 h-12"/>
                        <small>{{ attachment.name }}</small>
                    </template>
                </div>
            </template>
        </div>
        <div class="flex gap-2">
            <button
                class="text-gray-800 flex gap-1 items-center justify-center py-2 bg-gray-100 rounded-lg hover:bg-gray-200 px-4 flex-1">
                <HandThumbUpIcon class="w-6 h-6"/>
                Нравится
            </button>
            <button
                class="text-gray-800 flex gap-1 items-center justify-center py-2 bg-gray-100 rounded-lg hover:bg-gray-200 px-4 flex-1">
                <ChatBubbleLeftEllipsisIcon class="w-6 h-6"/>
                Комментарий
            </button>
        </div>
    </div>
</template>

<style scoped>

</style>
