<script setup>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FollowingList from "@/Components/FollowingList.vue";
import GroupList from "@/Components/GroupList.vue";

defineProps({
    groups: Array,
    followings: Array
});
</script>

<template>
    <Head title="Социальная сеть"/>

    <AuthenticatedLayout :menu="1">
        <div class="container max-w-[768px] mx-auto grid lg:grid-cols-3 gap-3 p-4 items-start">
            <div class="lg:col-span-2">
                <GroupList :groups="groups"/>
            </div>
            <div class="lg:col-span-1">
                <FollowingList :users="followings"/>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
