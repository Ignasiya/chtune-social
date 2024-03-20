<script setup>
import GroupItem from "@/Components/app/GroupItem.vue";
import TextInput from "@/Components/TextInput.vue";
import GroupModal from "@/Components/app/GroupModal.vue";
import {ref} from "vue";
import IndigoButton from "@/Components/app/IndigoButton.vue";

const searchKeyword = ref('');
const showNewGroupModal = ref(false);

const props = defineProps({
    groups: Array
})

function onGroupCreate(group) {
    props.groups.unshift(group)
}

</script>

<template>
    <div class="flex gap-2 mt-4">
        <TextInput :model-value="searchKeyword" placeholder="Введите для поиска" class="w-full"/>
        <IndigoButton
            class="w-min-[120px]"
            @click="showNewGroupModal = true">
            Новая группа
        </IndigoButton>
    </div>
    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400 text-center p-3">
            Вы не вступили ни в одну группу
        </div>
        <div v-else>
            <GroupItem v-for="group of groups" :group="group" />
        </div>
    </div>
    <GroupModal v-model="showNewGroupModal" @create="onGroupCreate"/>
</template>

<style scoped>

</style>
