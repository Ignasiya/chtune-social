<script setup>
import TextInput from "@/Components/TextInput.vue";
import {ref} from "vue";
import FollowingItem from "@/Components/app/FollowingItem.vue";

const searchKeyword = ref('');

</script>

<template>
    <div class="px-3 py-3 bg-white rounded border">
        <h2 class="text-xl font-bold mb-4">Мои подписчики</h2>
        <TextInput :model-value="searchKeyword" placeholder="Введите для поиска" class="w-full"/>
        <div class="py-8">
            <div v-if="false" class="text-gray-400 flex text-center">
                У Вас ещё нет друзей
            </div>
            <div v-else>
                <FollowingItem image="https://loremflickr.com/100/100" title="John Doe"/>
                <FollowingItem image="https://loremflickr.com/100/100" title="Elon Musk"/>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
