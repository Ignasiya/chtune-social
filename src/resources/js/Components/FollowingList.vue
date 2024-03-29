<script setup>
import UserItem from "@/Components/UserItem.vue";

defineProps({
    users: Array
})

</script>

<template>
    <div
        class="px-3 py-3 bg-white dark:bg-neutral-800 rounded-[20px] border dark:border-neutral-900 dark:text-gray-300 h-full overflow-hidden">
        <div class="lg:flex h-full flex-col overflow-hidden">
            <h2 class="text-xl font-bold">Мной читаемые</h2>
            <div class="mt-3 max-h-[300px] lg:flex-1 overflow-auto">
                <div v-if="users.length">
                    <UserItem
                        v-for="user of users"
                        :key="user.id"
                        :user="user"/>
                </div>
                <div v-else class="text-gray-400 text-center p-3">
                    Вы ни на кого не подписаны
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
