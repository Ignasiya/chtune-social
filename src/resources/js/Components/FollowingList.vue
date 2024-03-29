<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import {ChevronRightIcon} from "@heroicons/vue/24/solid/index.js";
import UserItem from "@/Components/UserItem.vue";

defineProps({
    users: Array
})

</script>

<template>
    <div
        class="px-3 py-3 bg-white dark:bg-stone-800 rounded border dark:border-stone-900 dark:text-gray-300 h-full overflow-hidden">
        <div class="block lg:hidden">
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="w-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">Мной читаемые</h2>
                        <ChevronRightIcon
                            class="w-6 h-6 transition-all"
                            :class="open ? 'rotate-90 transform' : ''" />
                    </div>
                </DisclosureButton>
                <DisclosurePanel>
                    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
                        <div v-if="users.length">
                            <UserItem
                                class="rounded-lg"
                                v-for="user of users"
                                :key="user.id"

                                :user="user"/>
                        </div>
                        <div v-else class="text-gray-400 text-center p-3">
                            Вы ни на кого не подписаны
                        </div>
                    </div>
                </DisclosurePanel>
            </Disclosure>
        </div>
        <div class="hidden lg:flex h-full flex-col overflow-hidden">
            <h2 class="text-xl font-bold">Мной читаемые</h2>
            <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
                <div v-if="users.length">
                    <UserItem
                        class="rounded-lg"
                        v-for="user of users"
                        :key="user.id"
                        :user="user"/>
                </div>
                <div v-else class="text-gray-400 text-center p-3">
                    Вы ни на кого не подписаны
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
