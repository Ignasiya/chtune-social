<script setup>
import {Popover, PopoverButton, PopoverPanel} from "@headlessui/vue";
import {BellAlertIcon} from "@heroicons/vue/24/solid/index.js";
import axiosClient from "@/axiosClient.js";
import {usePage} from "@inertiajs/vue3";

const page = usePage();

const props = defineProps({
    notifications: Array,
});

function loadNotifications() {
    axiosClient.get(route('notification.show'))
        .then(({data}) => {
            page.props.notifications = data.notifications;
        })
}

function updateNotifications(id) {
    axiosClient.patch(route('notification.update',  id))
}

</script>

<template>
    <Popover v-slot="{ open }" class="relative">
        <PopoverButton
            @click="loadNotifications"
            :class="open ?
            'text-white bg-sky-500' :
            'bg-black/5 dark:bg-neutral-700 dark:hover:bg-neutral-800 hover:bg-gray-200 text-sky-600 hover:text-sky-500'"
            class="group flex items-center rounded-full p-1.5"
        >
            <BellAlertIcon class="w-5 h-5"/>
        </PopoverButton>

        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-1 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-1 opacity-0"
        >
            <PopoverPanel
                class="absolute right-0 z-40 mt-3 w-screen sm:max-w-[350px] px-4 sm:px-0"
            >
                <div
                    class="max-h-[200px] overflow-auto p-2 rounded-lg ml-2 bg-sky-50 border-2 border-sky-500 dark:bg-sky-950 shadow-lg ring-1 ring-black/5">
                    <a
                        v-for="notification of page.props.notifications"
                        :key="notification.id"
                        :href="notification.data.post_url"
                        @click="updateNotifications(notification.id)"
                        class="p-1 flex flex-col rounded-lg transition duration-150 ease-in-out hover:bg-sky-200 dark:hover:bg-sky-800 focus:outline-none focus-visible:ring focus-visible:ring-orange-500/50"
                    >
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ notification.data.message }}
                        </p>
                        <small class="text-sl text-gray-400">
                            {{ notification.updated_at }}
                        </small>
                    </a>
                </div>
            </PopoverPanel>
        </transition>
    </Popover>
</template>

<style scoped>

</style>
