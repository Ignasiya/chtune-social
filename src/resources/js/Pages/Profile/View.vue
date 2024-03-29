<script setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {TabGroup, TabList, Tab, TabPanels, TabPanel} from '@headlessui/vue'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Components/TabItem.vue";
import Edit from "@/Pages/Profile/Partials/Edit.vue";
import {computed, ref} from "vue";
import {XMarkIcon, CameraIcon, CheckIcon} from '@heroicons/vue/24/solid'
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import PostList from "@/Components/PostList.vue";
import CreatePost from "@/Components/CreatePost.vue";
import UserItem from "@/Components/UserItem.vue";
import TextInput from "@/Components/TextInput.vue";
import TabPhotos from "@/Components/TabPhotos.vue";
import {wordEndingsParser} from "@/helpers.js";

const imagesForm = useForm({
    avatar: null,
    cover: null,
});

const showNotification = ref(true);
const coverImageSrc = ref('');
const avatarImageSrc = ref('');
const authUser = usePage().props.auth.user;
const searchFollowersKeyword = ref('');
const searchFollowingsKeyword = ref('');

const isMyProfile = computed(() => authUser && authUser.id === props.user.id);

const props = defineProps({
    errors: Object,
    mustVerifyEmail: Boolean,
    status: String,
    success: String,
    isUserFollower: Boolean,
    followerCount: Number,
    user: Object,
    followers: Array,
    followings: Array,
    posts: Object,
    photos: Array
});

function onCoverChange(event) {
    imagesForm.cover = event.target.files[0];
    if (imagesForm.cover) {
        const reader = new FileReader();
        reader.onload = () => {
            coverImageSrc.value = reader.result;
        }
        reader.readAsDataURL(imagesForm.cover);
    }
}

function resetCoverImage() {
    imagesForm.cover = null;
    coverImageSrc.value = null;
}

function submitCoverImage() {
    imagesForm.post(route('profile.updateImage'), {
        preserveScroll: true,
        onSuccess: () => {
            showNotification.value = true;
            resetCoverImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        }
    });
}

function onAvatarChange(event) {
    imagesForm.avatar = event.target.files[0];
    if (imagesForm.avatar) {
        const reader = new FileReader();
        reader.onload = () => {
            avatarImageSrc.value = reader.result;
        }
        reader.readAsDataURL(imagesForm.avatar);
    }
}

function resetAvatarImage() {
    imagesForm.avatar = null;
    avatarImageSrc.value = null;
}

function submitAvatarImage() {
    imagesForm.post(route('profile.updateImage'), {
        preserveScroll: true,
        onSuccess: () => {
            showNotification.value = true;
            resetAvatarImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        }
    });
}

function followUser() {
    const form = useForm({
        follow: !props.isUserFollower
    })

    form.post(route('user.follow', props.user.id), {
        preserveScroll: true,
    });
}

</script>

<template>
    <Head title="Профиль"/>

    <AuthenticatedLayout>
        <div class="container max-w-[768px] mx-auto h-full overflow-auto">
            <div class="px-4">
                <div
                    v-show="showNotification && success"
                    class="my-2 py-2 px-3 font-medium text-sm bg-emerald-500 text-white"
                >
                    {{ success }}
                </div>
                <div
                    v-if="errors.cover"
                    class="my-2 py-2 px-3 font-medium text-sm bg-red-400 text-white"
                >
                    {{ errors.cover }}
                </div>
                <div
                    class="group relative bg-white border-b border-gray-300 dark:border-neutral-300 dark:bg-neutral-800">
                    <img class="h-[200px] object-cover w-full"
                         :src="coverImageSrc || user.cover_url"
                         alt="cover">
                    <div v-if="isMyProfile" class="absolute top-2 right-2">
                        <button
                            v-if="!coverImageSrc"
                            class="bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs flex items-center opacity-0 group-hover:opacity-100">
                            <CameraIcon class="h-3 w-3 mr-2"/>
                            Обновить обложку
                            <input type="file" class="absolute left-0 right-0 bottom-0 top-0 opacity-0"
                                   @change="onCoverChange"/>
                        </button>
                        <div v-else class="flex gap-2 p-2 opacity-0 group-hover:opacity-100">
                            <button
                                @click="resetCoverImage"
                                class="flex bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs items-center">
                                <XMarkIcon class="h-3 w-3 mr-2"/>
                                Отмена
                            </button>
                            <button
                                @click="submitCoverImage"
                                class="flex bg-gray-800 hover:bg-gray-900 text-gray-100 py-1 px-2 text-xs items-center">
                                <CheckIcon class="h-3 w-3 mr-2"/>
                                Сохранить
                            </button>
                        </div>
                    </div>
                    <div class="flex">
                        <div
                            class="flex items-center justify-center relative group/avatar ml-[48px] -mt-[64px] w-[128px] h-[128px] rounded-full">
                            <img class="w-full h-full object-cover rounded-full"
                                 :src="avatarImageSrc || user.avatar_url"
                                 alt="avatar">
                            <div v-if="isMyProfile">
                                <button
                                    v-if="!avatarImageSrc"
                                    class="absolute left-0 right-0 bottom-0 top-0 bg-black/50 text-gray-200 rounded-full opacity-0 flex items-center justify-center group-hover/avatar:opacity-100">
                                    <CameraIcon class="h-8 w-8"/>
                                    <input type="file" class="absolute left-0 right-0 bottom-0 top-0 opacity-0"
                                           @change="onAvatarChange"/>
                                </button>
                                <div v-else class="absolute top-1 right-0 flex flex-col gap-2">
                                    <button
                                        @click="resetAvatarImage"
                                        class="h-7 w-7 flex items-center justify-center bg-red-500/80 text-white rounded-full">
                                        <XMarkIcon class="h-5 w-5"/>
                                    </button>
                                    <button
                                        @click="submitAvatarImage"
                                        class="h-7 w-7 flex items-center justify-center bg-emerald-500 text-white rounded-full">
                                        <CheckIcon class="h-5 w-5"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-1 justify-between items-center p-4">
                            <div>
                                <h2 class="font-bold text-gray-700 dark:text-gray-300 text-lg">{{ user.name }}</h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{
                                        wordEndingsParser(followerCount, 'подписчи', 'к',
                                            'ка',
                                            'ков')
                                    }}</p>
                            </div>
                            <div v-if="!isMyProfile">
                                <PrimaryButton v-if="!isUserFollower" @click="followUser">
                                    Подписаться
                                </PrimaryButton>
                                <DangerButton v-else @click="followUser">
                                    Отписаться
                                </DangerButton>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="p-4 pt-0">
                <TabGroup>
                    <TabList class="flex bg-white dark:bg-neutral-800">
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Записи" :selected="selected"></TabItem>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Подписчики" :selected="selected"></TabItem>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Подписан" :selected="selected"></TabItem>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Фото" :selected="selected"></TabItem>
                        </Tab>
                        <Tab v-if="isMyProfile" v-slot="{ selected }" as="template">
                            <TabItem text="Редактирование" :selected="selected"></TabItem>
                        </Tab>
                    </TabList>

                    <TabPanels class="mt-2">
                        <TabPanel>
                            <template v-if="posts">
                                <div v-if="posts.data.length">
                                    <CreatePost v-if="isMyProfile"/>
                                    <PostList :posts="posts.data" class="flex-1"/>
                                </div>
                                <div v-else class="py-8 text-gray-600 dark:text-gray-300 text-center">
                                    Пользователь пока не опубликовал записи
                                </div>
                            </template>
                            <div v-else class="py-8 text-gray-600 dark:text-gray-300 text-center">
                                Чтобы видеть записи войдите на сайт
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <div v-if="followers.length">
                                <div class="mb-3">
                                    <TextInput
                                        :model-value="searchFollowersKeyword"
                                        placeholder="Введите для поиска"
                                        class="w-full"/>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <UserItem
                                        class="shadow"
                                        v-for="follower of followers"
                                        :user="follower"
                                        :key="follower.id"/>
                                </div>
                            </div>
                            <div v-else class="py-8 text-gray-600 dark:text-gray-300 text-center">
                                У Вас нет подписчиков
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <div v-if="followings.length">
                                <div class="mb-3">
                                    <TextInput
                                        :model-value="searchFollowingsKeyword"
                                        placeholder="Введите для поиска"
                                        class="w-full"/>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <UserItem
                                        class="shadow"
                                        v-for="following of followings"
                                        :user="following"
                                        :key="following.id"/>
                                </div>
                            </div>
                            <div v-else class="py-8 text-gray-600 dark:text-gray-300 text-center">
                                Вы ни на кого не подписаны
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <TabPhotos v-if="photos.length" :photos="photos"/>
                            <div v-else class="py-8 text-gray-600 dark:text-gray-300 text-center">
                                Пользователь пока не опубликовал фотографии
                            </div>
                        </TabPanel>
                        <TabPanel v-if="isMyProfile">
                            <Edit :must-verify-email="mustVerifyEmail" :status="status"/>
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
