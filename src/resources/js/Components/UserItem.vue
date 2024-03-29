<script setup>
import {Link} from '@inertiajs/vue3';
import {XMarkIcon} from "@heroicons/vue/24/solid/index.js";
import UserHeader from "@/Components/UserHeader.vue";

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

defineEmits(['approved', 'rejected', 'roleChange', 'deleted'])

</script>

<template>
    <div
        class="transition-all bg-white dark:text-gray-300 dark:bg-neutral-800 border-2 border-transparent rounded-lg hover:border-sky-500">
        <div class="flex items-center gap-2 py-2 px-2">
            <UserHeader :user="user" />
            <div class="flex justify-between items-center flex-1">
                <Link :href="route('profile', user.username)">
                    <h3 class="font-black hover:underline">{{ user.name }}</h3>
                </Link>
                <div v-if="forApprove" class="flex gap-1">
                    <button
                        class="text-xs py-1 px-2 rounded bg-red-500 hover:bg-red-600 text-white"
                        @click.prevent="$emit('rejected', user)">
                        отклонить
                    </button>
                    <button
                        class="text-xs py-1 px-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white"
                        @click.prevent="$emit('approved', user)">
                        одобрить
                    </button>
                </div>
                <div class="flex gap-1" v-if="showRoleDropdown">
                    <select
                        :disabled="disableRoleDropdown"
                        @change="$emit('roleChange', user, $event.target.value)"
                        class="rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-sky-600 max-w-xs text-sm leading-6">
                        <option :selected="user.role === 'admin'">admin</option>
                        <option :selected="user.role === 'user'">user</option>
                    </select>
                    <button
                        @click="$emit('deleted', user)"
                        :disabled="disableRoleDropdown"
                        class="h-8 w-8 rounded-full hover:bg-black/5 transition flex items-center justify-center disabled:opacity-25">
                        <XMarkIcon class="w-4 h-4"/>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>

