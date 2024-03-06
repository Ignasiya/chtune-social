<script setup>
import GroupItem from "@/Components/app/GroupItem.vue";
import TextInput from "@/Components/TextInput.vue";

</script>

<template>
    <TextInput :model-value="searchKeyword" placeholder="Введите для поиска" class="w-full mt-4"/>
    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400 text-center p-3">
            Вы не вступили ни в одну группу
        </div>
        <div v-else>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Laravel"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Vue.js"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Laravel"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Vue.js"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Laravel"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Vue.js"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Laravel"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Vue.js"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Laravel"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Vue.js"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Laravel"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Vue.js"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Laravel"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
            <GroupItem image="https://loremflickr.com/100/100"
                       title="Разработчики Vue.js"
                       description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."/>
        </div>
    </div>
</template>

<style scoped>

</style>
