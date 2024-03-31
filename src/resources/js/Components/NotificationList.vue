<script setup>
import {Popover, PopoverButton, PopoverPanel} from "@headlessui/vue";
import {BellAlertIcon} from "@heroicons/vue/24/solid/index.js";
import {onMounted, ref, watch} from "vue";
import axiosClient from "@/axiosClient.js";
import {usePage} from "@inertiajs/vue3";

const page = usePage();

const notifications = defineModel({
    type: Array,
    required: true,
});

const loadMoreIntersect = ref(null);
const allNotifications = ref({
    data: [],
    next: null
})

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => entries.forEach(entry => entry.isIntersecting && loadMore()), {
            rootMargin: '-250px 0px 0px 0px'
        })
    observer.observe(loadMoreIntersect.value)
})

watch(() => page.notifications, () => {
    if (page.notifications) {
        allNotifications.value = {
            data: page.notifications.data,
            next: page.notifications.links.next
        }
    }
}, {deep: true, immediate: true});

function loadMore() {
    if (!allNotifications.value.next) {
        return;
    }
    axiosClient.get(allNotifications.value.next)
        .then(({data}) => {
            allNotifications.value.data = [...allNotifications.value.data, ...data.data]
            allNotifications.value.next = data.links.next
        })
}

</script>

<template>
    <Popover v-slot="{ open }" class="relative">
        <PopoverButton
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
                class="absolute right-0 z-40 mt-3 w-screen max-w-sm px-4 sm:px-0"
            >
                <div
                    class="overflow-hidden rounded-[20px] ml-2 bg-sky-50 border-2 border-sky-500 dark:bg-sky-950 shadow-lg ring-1 ring-black/5">
                    <div
                        class="grid gap-8 p-7 lg:grid-cols-2">
                        <a
                            v-for="notification of allNotifications.data"
                            :key="notification.id"
                            :href="notification.href"
                            class="-m-3 flex items-center rounded-lg p-2 transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus-visible:ring focus-visible:ring-orange-500/50"
                        >
                            <p class="text-sm text-gray-500">
                                {{ notification.message }}
                            </p>
                            <p>
                                {{ notification.created_at }}
                            </p>
                            <p>
                                {{ notification.is_read }}
                            </p>
                        </a>
                    </div>
                </div>
            </PopoverPanel>
        </transition>
    </Popover>
</template>

<style scoped>

</style>
