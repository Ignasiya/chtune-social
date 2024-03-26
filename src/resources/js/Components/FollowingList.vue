<script setup>
import FollowingItems from "@/Components/FollowingItems.vue";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import {ChevronRightIcon} from "@heroicons/vue/24/solid/index.js";

defineProps({
    users: Array
})

</script>

<template>
    <div class="px-3 py-3 bg-white rounded border h-full overflow-hidden">
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
                    <FollowingItems :users="users" />
                </DisclosurePanel>
            </Disclosure>
        </div>
        <div class="hidden lg:flex h-full flex-col overflow-hidden">
            <h2 class="text-xl font-bold">Мной читаемые</h2>
            <FollowingItems :users="users" />
        </div>
    </div>
</template>

<style scoped>

</style>
