<script setup>
import GroupModal from "@/Pages/Group/Partials/GroupModal.vue";
import GroupItem from "@/Components/GroupItem.vue";
import {ref} from "vue";
import {UserGroupIcon} from '@heroicons/vue/24/solid'

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
        class="px-3 py-3 bg-white dark:bg-neutral-800 rounded-[20px] border dark:border-neutral-900 dark:text-gray-300 h-full overflow-hidden">
        <div class="lg:flex h-full flex-col overflow-hidden">
            <div class="flex justify-between">
                <h2 class="text-xl font-bold">Мои группы</h2>
                <button
                    class="flex items-center rounded-full p-2 bg-sky-700 hover:bg-sky-600 text-white hover:text-gray-100"
                    @click="showNewGroupModal = true">
                    <UserGroupIcon class="w-6 h-6"/>
                    +
                </button>
            </div>
            <div class="mt-3 max-h-[400px] lg:flex-1 overflow-auto">
                <div v-if="groups.length">
                    <GroupItem v-for="group of groups" :key="group.id" :group="group"/>
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
