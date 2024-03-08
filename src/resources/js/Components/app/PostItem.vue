<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/vue'
import {HandThumbUpIcon, ChatBubbleLeftEllipsisIcon, ArrowDownTrayIcon, DocumentIcon} from '@heroicons/vue/24/solid'

defineProps({
    post: Object
});

function isImage(attachment) {
    const mime = attachment.mime.split('/');
    return mime[0].toLowerCase() === 'image';
}
</script>

<template>
    <div class="bg-white border rounded p-4 mb-3">
        <div class="flex items-center gap-2 mb-3">
            <a href="javascript:void(0)">
                <img :src="post.user.avatar_url" alt="avatar"
                     class="w-[40px] rounded-full border-2 transition-all hover:border-blue-500"/>
            </a>
            <div>
                <h4 class="font-bold">
                    <a href="javascript:void(0)" class="hover:underline">{{ post.user.name }}</a>
                    <template v-if="post.group">
                        >
                        <a href="javascript:void(0)" class="hover:underline">{{ post.group.name }}</a>
                    </template>
                </h4>
                <small class="text-gray-400">{{ post.created_at }}</small>
            </div>
        </div>
        <div class="mb-3">
            <Disclosure v-slot="{ open }">
                <div v-if="!open" v-html="post.body.substring(0, 200)"/>
                <template v-if="post.body.length > 200">
                    <DisclosurePanel>
                        <div v-html="post.body"/>
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
