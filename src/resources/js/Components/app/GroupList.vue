<script setup>
import {ChevronRightIcon} from '@heroicons/vue/24/solid'
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import GroupItems from "@/Components/app/GroupItems.vue";

defineProps({
    groups: Array
})

</script>

<template>
    <div class="px-3 py-3 bg-white rounded border h-full overflow-hidden">
        <div class="block lg:hidden">
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="w-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">Мои группы</h2>
                        <ChevronRightIcon
                            class="w-6 h-6 transition-all"
                            :class="open ? 'rotate-90 transform' : ''" />
                    </div>
                </DisclosureButton>
                <DisclosurePanel>
                    <GroupItems :groups="groups" />
                </DisclosurePanel>
            </Disclosure>
        </div>
        <div class="hidden lg:flex h-full flex-col overflow-hidden">
            <div class="flex justify-between">
                <h2 class="text-xl font-bold">Мои группы</h2>
            </div>
            <GroupItems :groups="groups" />
        </div>
    </div>
</template>

<style scoped>

</style>
