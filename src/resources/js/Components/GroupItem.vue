<script setup>
import {Link} from '@inertiajs/vue3'
    defineProps({
        group: Object,
    })
</script>

<template>
    <div class="cursor-pointer hover:bg-gray-100 dark:hover:bg-stone-900 rounded">
        <Link :href="route('group.profile', group.slug)" class="flex items-start gap-1 py-2 px-2 w-full">
            <img class="w-[32px] h-[32px] object-cover rounded-full" :src="group.thumbnail_url" alt="thumbnail">
            <div class="flex-1">
                <div class="flex items-center flex-1 justify-between">
                    <h3 class="font-black">{{ group.name }}</h3>
                    <small class="text-xs">
                        {{ group.status === 'approved' ? (group.role === 'admin' ? group.role : '') : 'запрос' }}
                    </small>
                </div>
                <div class="text-xs text-gray-500 ck-content-output" v-html="group.description"></div>
            </div>
        </Link>
    </div>
</template>

<style scoped>

</style>
