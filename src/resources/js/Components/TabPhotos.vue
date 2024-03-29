<script setup>
import {ArrowDownTrayIcon} from '@heroicons/vue/24/solid'
import {ref} from "vue";
import AttachmentPreview from "@/Components/AttachmentPreview.vue";

const showPhotoModal = ref(false);
const previewPhotoIndex = ref(0);

defineProps({
    photos: Array
})

function openPhoto(index) {
    previewPhotoIndex.value = index
    showPhotoModal.value = true;
}

</script>
<template>
    <div class="grid gap-2 grid-cols-2 sm:grid-cols-3">
        <template v-for="(photo, ind) of photos">
            <div
                @click="openPhoto(ind)"
                class="group aspect-square bg-sky-100 dark:bg-neutral-700 flex flex-col items-center justify-center text-gray-500 relative cursor-pointer">

                <!--Download-->
                <a
                    @click.stop
                    :href="route('post.download', photo)"
                    class="opacity-0 z-20 group-hover:opacity-100 transition-all w-8 h-8 flex items-center justify-center text-gray-300 bg-gray-700 rounded absolute right-2 top-2 cursor-pointer hover:bg-gray-800">
                    <ArrowDownTrayIcon class="w-4 h-4"/>
                </a>
                <!--/ Download-->

                <img :src="photo.url"
                     class="object-contain aspect-square"
                     alt="image"/>
            </div>
        </template>
    </div>

    <AttachmentPreview
        :attachments="photos || []"
        v-model:index="previewPhotoIndex"
        v-model="showPhotoModal"/>
</template>
