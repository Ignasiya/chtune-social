<script setup>
import {Link} from '@inertiajs/vue3';
import {ChevronRightIcon} from '@heroicons/vue/24/solid/index.js'
import UserHeader from "@/Components/UserHeader.vue";
import {computed} from "vue";

const props = defineProps({
    post: Object,
    showTime: {
        type: Boolean,
        default: true
    }
})

const dateFormatted = computed(() => {
    const date = new Date(props.post.updated_at.replace(/-/g, "/"));
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const hours = date.getHours();
    const minutes = date.getMinutes();
    const daysOfWeek = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];
    return `${daysOfWeek[date.getDay()]}, ${date.toLocaleDateString(undefined, options)}
    ${hours}:${minutes.toString().padStart(2, '0')}`;
})

</script>

<template>
    <div class="flex items-center gap-2 dark:text-gray-300">
        <UserHeader :user="post.user"/>
        <div>
            <h4 class="flex items-center font-bold">
                <Link :href="route('profile', post.user.username)" class="hover:underline">
                    {{ post.user.name }}
                </Link>
                <template v-if="post.group">
                    <ChevronRightIcon class="w-4 h-4"/>
                    <Link :href="route('group.profile', post.group.slug)" class="hover:underline">
                        {{ post.group.name }}
                    </Link>
                </template>
            </h4>
            <small v-if="showTime" class="text-gray-400">{{ dateFormatted }}</small>
        </div>
    </div>
</template>

<style scoped>

</style>
