<script setup>

import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {EllipsisHorizontalIcon, PencilIcon, TrashIcon, EyeIcon, ClipboardIcon} from
        "@heroicons/vue/20/solid/index.js";
import {usePage, Link} from "@inertiajs/vue3";
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

function copyToClipboard() {
    const textToCopy = route('post.view', props.post.id);

    const tempInput = document.createElement('input');
    tempInput.value = textToCopy;
    document.body.appendChild(tempInput);

    tempInput.select();
    document.execCommand('copy');

    document.body.removeChild(tempInput);
}

</script>

<template>
    <Menu as="div" class="relative inline-block text-left">
        <div>
            <MenuButton
                class="z-20 h-8 w-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                <EllipsisHorizontalIcon
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
                class="absolute z-30 right-10 top-0 w-40 origin-top-right divide-y divide-gray-100 dark:divide-neutral-900 rounded-md bg-white dark:bg-neutral-900 shadow-lg focus:outline-none"
            >
                <MenuItem
                    v-if="!comment"
                    class="px-1 py-1"
                    v-slot="{ active }">
                    <Link
                        :href="route('post.view', post.id)"
                        :class="[
                              active ? 'bg-sky-600 text-white' : 'text-gray-600 dark:text-gray-300',
                              'group flex w-full items-center rounded-md px-1 py-1 text-sm',
                            ]"
                    >
                        <EyeIcon
                            class="mr-2 h-4 w-4"
                            aria-hidden="true"
                        />
                        Открыть запись
                    </Link>
                </MenuItem>
                <MenuItem
                    v-if="!comment"
                    class="px-1 py-1"
                    v-slot="{ active }">
                    <button
                        @click="copyToClipboard"
                        :class="[
                              active ? 'bg-sky-600 text-white' : 'text-gray-600 dark:text-gray-300',
                              'group flex w-full items-center rounded-md px-1 py-1 text-sm',
                            ]"
                    >
                        <ClipboardIcon
                            class="mr-2 h-4 w-4"
                            aria-hidden="true"
                        />
                        Скопировать URL
                    </button>
                </MenuItem>
                <MenuItem
                    class="px-1 py-1"
                    v-if="editAllowed"
                    v-slot="{ active }">
                    <button
                        @click="$emit('edit')"
                        :class="[
                              active ? 'bg-sky-600 text-white' : 'text-gray-600 dark:text-gray-300',
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
                              active ? 'bg-sky-600 text-white' : 'text-gray-600 dark:text-gray-300',
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
