<script setup>
import GroupItem from "@/Components/app/GroupItem.vue";
import TextInput from "@/Components/TextInput.vue";
import {ref} from "vue";

const searchKeyword = ref('');

</script>

<template>
    <div class="px-3 py-3 bg-white rounded border">
        <h2 class="text-xl font-bold mb-4">Мои группы</h2>
        <TextInput :model-value="searchKeyword" placeholder="Введите для поиска" class="w-full"/>
        <div class="py-8">
            <div v-if="false" class="text-gray-400 flex text-center">
                Вы не вступили ни в одну группу
            </div>
            <div v-else>
                <GroupItem image="https://loremflickr.com/100/100"
                           title="Разработчики Laravel"
                           description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
                <GroupItem image="https://loremflickr.com/100/100"
                           title="Разработчики Vue.js"
                           description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
