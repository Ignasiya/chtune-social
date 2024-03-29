<script setup>
import {ArrowDownTrayIcon, DocumentIcon, PlayCircleIcon} from '@heroicons/vue/24/solid'
import {isImage, isVideo} from "@/helpers.js";

defineProps({
    attachments: Array
})

defineEmits(['attachmentClick'])
</script>
<template>
    <template v-for="(attachment, ind) of attachments.slice(0, 4)">
        <div
            @click="$emit('attachmentClick', ind)"
            class="group aspect-square bg-blue-100 dark:bg-neutral-700 flex flex-col items-center justify-center text-gray-500 dark:text-gray-300 relative cursor-pointer">

            <div
                v-if="ind === 3 && attachments.length > 4"
                class="absolute flex items-center justify-center z-10 left-0 top-0 right-0 bottom-0 z-10 bg-black/60 text-white">
                +{{ attachments.length - 4 }} ещё
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
                 class="object-contain"
                 alt="image"/>
            <div v-else-if="isVideo(attachment)" class="relative flex justify-center items-center">
                <PlayCircleIcon class="absolute w-20 h-20 z-10 text-gray-300 opacity-70" />
                <div class="absolute top-0 left-0 w-full h-full bg-black/50"/>
                <video :src="attachment.url"/>
            </div>
            <div
                v-else
                class="flex flex-col justify-center items-center">
                <DocumentIcon class="w-12 h-12"/>
                <small>{{ attachment.name }}</small>
            </div>
        </div>
    </template>
</template>
