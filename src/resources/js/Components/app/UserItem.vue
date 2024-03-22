<script setup>
import {Link} from '@inertiajs/vue3';

defineProps({
    user: Object,
    forApprove: {
        type: Boolean,
        default: false
    }
})

defineEmits(['approve', 'reject'])

</script>

<template>
    <div
        class="cursor-pointer transition-all bg-white border-2 border-transparent hover:border-indigo-500">
        <Link :href="route('profile', user.username)" class="flex items-center gap-2 py-2 px-2">
            <img class="w-[32px] h-[32px] object-cover rounded-full" :src="user.avatar_url" alt="avatar">
            <div class="flex justify-between items-center flex-1">
                <h3 class="font-black">{{ user.name }}</h3>
                <div class="flex gap-1">
                    <button
                        class="text-xs py-1 px-3 rounded bg-red-500 hover:bg-red-600 text-white"
                        v-if="forApprove"
                        @click.prevent.stop="$emit('reject', user)">
                        Отклонить
                    </button>
                    <button
                        class="text-xs py-1 px-3 rounded bg-emerald-500 hover:bg-emerald-600 text-white"
                        v-if="forApprove"
                        @click.prevent.stop="$emit('approve', user)">
                        Одобрить
                    </button>
                </div>
            </div>
        </Link>
    </div>
</template>

<style scoped>

</style>

