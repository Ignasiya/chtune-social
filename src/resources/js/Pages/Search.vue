<script setup>
import UserItem from "@/Components/UserItem.vue";
import GroupItem from "@/Components/GroupItem.vue";
import PostList from "@/Components/PostList.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";

const props = defineProps({
    search: String,
    users: Array,
    groups: Array,
    posts: Object
})

</script>

<template>
    <Head title="Поиск"/>

    <AuthenticatedLayout>
        <div class="container py-4 px-2 max-w-[768px] mx-auto h-full overflow-auto">
            <div v-if="!search.startsWith('#')" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div class="shadow bg-white dark:bg-neutral-800 p-3 rounded max-h-[300px] overflow-auto">
                    <h2 class="text-lg font-bold dark:text-gray-300">
                        Пользователи
                    </h2>
                    <UserItem v-if="users.length" v-for="user of users" :key="user.id" :user="user"/>
                    <div v-else class="py-4 text-center text-gray-500 dark:text-gray-300">
                        Не найдены пользователи
                    </div>
                </div>
                <div class="shadow bg-white dark:bg-neutral-800 p-3 rounded max-h-[300px] overflow-auto">
                    <h2 class="text-lg font-bold dark:text-gray-300">
                        Группы
                    </h2>
                    <GroupItem v-if="groups.length" v-for="group of groups" :key="group.id" :group="group"/>
                    <div v-else class="py-4 text-center text-gray-500 dark:text-gray-300">
                        Не найдены группы
                    </div>
                </div>
            </div>
            <div>
                <h2 class="text-lg font-bold dark:text-gray-300">
                    Записи
                </h2>
                <PostList v-if="posts.data.length" :posts="posts.data" class="flex-1"/>
                <div v-else class="py-8 text-center text-gray-500 dark:text-gray-300">
                    Не найдены записи
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
