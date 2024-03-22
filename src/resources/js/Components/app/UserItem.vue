<script setup>
import {Link} from '@inertiajs/vue3';

defineProps({
    user: Object,
    forApprove: {
        type: Boolean,
        default: false
    },
    showRoleDropdown: {
        type: Boolean,
        default: false
    },
    disableRoleDropdown: {
        type: Boolean,
        default: false
    }
})

defineEmits(['approved', 'rejected', 'roleChange'])

</script>

<template>
    <div
        class="transition-all bg-white border-2 border-transparent hover:border-indigo-500">
        <div class="flex items-center gap-2 py-2 px-2">
            <Link :href="route('profile', user.username)">
                <img class="w-[32px] h-[32px] object-cover rounded-full" :src="user.avatar_url" alt="avatar">
            </Link>
            <div class="flex justify-between items-center flex-1">
                <Link :href="route('profile', user.username)">
                    <h3 class="font-black hover:underline">{{ user.name }}</h3>
                </Link>
                <div v-if="forApprove" class="flex gap-1">
                    <button
                        class="text-xs py-1 px-3 rounded bg-red-500 hover:bg-red-600 text-white"
                        @click.prevent="$emit('rejected', user)">
                        Отклонить
                    </button>
                    <button
                        class="text-xs py-1 px-3 rounded bg-emerald-500 hover:bg-emerald-600 text-white"
                        @click.prevent="$emit('approved', user)">
                        Одобрить
                    </button>
                </div>
                <div v-if="showRoleDropdown">
                    <select
                        :disabled="disableRoleDropdown"
                        @change="$emit('roleChange', user, $event.target.value)"
                        class="rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 max-w-xs text-sm leading-6">
                        <option :selected="user.role === 'admin'">admin</option>
                        <option :selected="user.role === 'user'">user</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>

