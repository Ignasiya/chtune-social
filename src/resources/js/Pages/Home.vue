<script setup>
import {Head} from '@inertiajs/vue3';
import PostList from "@/Components/PostList.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps({
    posts: Object
});

</script>

<template>
    <Head title="Социальная сеть"/>

    <AuthenticatedLayout :menu="0">
        <div class="container max-w-[768px] mx-auto p-4">
            <div class="flex flex-col">
                <PostList :posts="posts.data" class="flex-1"/>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>

</style>
