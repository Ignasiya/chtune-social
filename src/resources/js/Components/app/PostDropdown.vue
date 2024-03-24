<script setup>

import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {EllipsisVerticalIcon, PencilIcon, TrashIcon} from "@heroicons/vue/20/solid/index.js";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps({
    post: {
        type: Object,
        default: null
    },
    comment: {
        type: Object,
        default: null
    }
})

const authUser = usePage().props.auth.user || {};
const group = usePage().props.group;

const user = computed(() => props.comment?.user || props.post?.user);

defineEmits(['edit', 'delete'])

const editAllowed = computed(() => user.value.id === authUser.id);

const deleteAllowed = computed(() => {
    if (user.value.id === authUser.id) return true;
    if (props.post.user.id === authUser.id) return true;
    return !props.comment && props.post.group?.role === 'admin';
})

</script>

<template>
    <Menu as="div" class="relative inline-block text-left">
        <div>
            <MenuButton
                class="z-20 h-8 w-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
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
                class="absolute z-30 right-0 mt-2 w-40 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg focus:outline-none"
            >
                <MenuItem
                    class="px-1 py-1"
                    v-if="editAllowed"
                    v-slot="{ active }">
                    <button
                        @click="$emit('edit')"
                        :class="[
                              active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                              'group flex w-full items-center rounded-md px-1 py-1 text-sm',
                            ]"
                    >
                        <PencilIcon
                            class="mr-2 h-4 w-4"
                            aria-hidden="true"
                        />
                        Редактировать
                    </button>
                </MenuItem>
                <MenuItem
                    class="px-1 py-1"
                    v-if="deleteAllowed"
                    v-slot="{ active }">
                    <button
                        @click="$emit('delete')"
                        :class="[
                              active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                              'group flex w-full items-center rounded-md px-1 py-1 text-sm',
                            ]"
                    >
                        <TrashIcon
                            class="mr-2 h-4 w-4"
                            aria-hidden="true"
                        />
                        Удалить
                    </button>
                </MenuItem>
            </MenuItems>
        </transition>
    </Menu>
</template>

<style scoped>

</style>
