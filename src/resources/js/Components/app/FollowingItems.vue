<script setup>
import FollowingItem from "@/Components/app/FollowingItem.vue";
import TextInput from "@/Components/TextInput.vue";
import {ref} from "vue";

const searchKeyword = ref('');
</script>

<template>
    <TextInput :model-value="searchKeyword" placeholder="Введите для поиска" class="w-full mt-4"/>
    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400 text-center p-3">
            У Вас ещё нет друзей
        </div>
        <div v-else>
            <FollowingItem image="https://loremflickr.com/100/100" title="John Doe"/>
            <FollowingItem image="https://loremflickr.com/100/100" title="Elon Musk"/>
            <FollowingItem image="https://loremflickr.com/100/100" title="John Doe"/>
            <FollowingItem image="https://loremflickr.com/100/100" title="Elon Musk"/>
            <FollowingItem image="https://loremflickr.com/100/100" title="John Doe"/>
            <FollowingItem image="https://loremflickr.com/100/100" title="Elon Musk"/>
            <FollowingItem image="https://loremflickr.com/100/100" title="John Doe"/>
            <FollowingItem image="https://loremflickr.com/100/100" title="Elon Musk"/>
            <FollowingItem image="https://loremflickr.com/100/100" title="John Doe"/>
            <FollowingItem image="https://loremflickr.com/100/100" title="Elon Musk"/>
        </div>
    </div>
</template>

<style scoped>

</style>
