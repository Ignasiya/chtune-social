<script setup>
import UserItem from "@/Components/UserItem.vue";
import TextInput from "@/Components/TextInput.vue";
import {ref} from "vue";

defineProps({
    users: Array
})

const searchKeyword = ref('');
</script>

<template>
    <TextInput :model-value="searchKeyword" placeholder="Введите для поиска" class="w-full mt-4"/>
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
</template>

<style scoped>

</style>
