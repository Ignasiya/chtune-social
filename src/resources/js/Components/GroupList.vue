<script setup>
import {ChevronRightIcon} from '@heroicons/vue/24/solid'
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import GroupModal from "@/Pages/Group/Partials/GroupModal.vue";
import SkyButton from "@/Components/SkyButton.vue";
import GroupItem from "@/Components/GroupItem.vue";
import {ref} from "vue";

const showNewGroupModal = ref(false);

const props = defineProps({
    groups: Array
})

function onGroupCreate(group) {
    props.groups.unshift(group)
}

</script>

<template>
    <div
        class="px-3 py-3 bg-white dark:bg-neutral-800 rounded border dark:border-neutral-900 dark:text-gray-300 h-full overflow-hidden">
        <div class="block lg:hidden">
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="w-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">Мои группы</h2>
                        <SkyButton
                            class="w-min-[120px]"
                            @click="showNewGroupModal = true">
                            Новая группа
                        </SkyButton>
                        <ChevronRightIcon
                            class="w-6 h-6 transition-all"
                            :class="open ? 'rotate-90 transform' : ''" />
                    </div>
                </DisclosureButton>
                <DisclosurePanel>
                    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
                        <div v-if="groups.length">
                            <GroupItem v-for="group of groups" :key="group.id" :group="group" />
                        </div>
                        <div v-else class="text-gray-400 text-center p-3">
                            Вы не вступили ни в одну группу
                        </div>
                    </div>
                </DisclosurePanel>
            </Disclosure>
        </div>
        <div class="hidden lg:flex h-full flex-col overflow-hidden">
            <div class="flex justify-between">
                <h2 class="text-xl font-bold">Мои группы</h2>
                <SkyButton
                    class="w-min-[120px]"
                    @click="showNewGroupModal = true">
                    Новая группа
                </SkyButton>
            </div>
            <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
                <div v-if="groups.length">
                    <GroupItem v-for="group of groups" :key="group.id" :group="group" />
                </div>
                <div v-else class="text-gray-400 text-center p-3">
                    Вы не вступили ни в одну группу
                </div>
            </div>
        </div>
        <GroupModal v-model="showNewGroupModal" @create="onGroupCreate"/>
    </div>
</template>

<style scoped>

</style>
