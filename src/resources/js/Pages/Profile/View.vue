<script setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {TabGroup, TabList, Tab, TabPanels, TabPanel} from '@headlessui/vue'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {computed, ref} from "vue";
import {XMarkIcon, CameraIcon, CheckIcon} from '@heroicons/vue/24/solid'

const imagesForm = useForm({
    avatar: null,
    cover: null,
});

const showNotification = ref(true);
const coverImageSrc = ref('');
const authUser = usePage().props.auth.user;

const isMyProfile = computed(() => authUser && authUser.id === props.user.id);

const props = defineProps({
    errors: Object,
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    user: {
        type: Object,
    }
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

function cancelCoverImage() {
    imagesForm.cover = null;
    coverImageSrc.value = null;
}

function submitCoverImage() {
    imagesForm.post(route('profile.updateImage'), {
        onSuccess: () => {
            cancelCoverImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        }
    });
}



</script>

<template>
    <Head title="Профиль"/>

    <AuthenticatedLayout>
        <div class="max-w-[768px] bg-white mx-auto h-full overflow-auto">
            <div
                v-show="showNotification && status === 'cover-update'"
                class="my-2 py-2 px-3 font-medium text-sm bg-emerald-500 text-white"
            >
                Ваша обложка обновлена.
            </div>
            <div
                v-if="errors.cover"
                class="my-2 py-2 px-3 font-medium text-sm bg-red-400 text-white"
            >
                {{ errors.cover }}
            </div>
            <div class="group relative bg-white">
                <img class="h-[200px] object-cover w-full"
                     :src="coverImageSrc || user.cover_url || '/image/cover_default.jpg'"
                     alt="cover">
                <div class="absolute top-2 right-2">
                    <button
                        v-if="!coverImageSrc"
                        class="bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs flex items-center opacity-0 group-hover:opacity-100">
                        <CameraIcon class="h-3 w-3 mr-2"/>
                        Обновить обложку
                        <input type="file" class="absolute left-0 right-0 bottom-0 top-0 opacity-0"
                               @change="onCoverChange"/>
                    </button>
                    <div v-else class="flex gap-2 bg-white p-2 opacity-0 group-hover:opacity-100">
                        <button
                            @click="cancelCoverImage"
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
                    <img class="ml-[48px] w-[128px] h-[128px] -mt-[64px]"
                         src="https://pol-24.ru/wp-content/uploads/2021/12/1_x7X2oAehk5M9IvGwO_K0Pg.png"
                         alt="avatar">
                    <div class="flex flex-1 justify-between items-center p-4">
                        <h2 class="font-bold text-lg">{{ user.name }}</h2>
                        <PrimaryButton v-if="isMyProfile">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24"
                                 fill="currentColor"
                                 class="w-4 h-4 mr-2">
                                <path
                                    d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32L19.513 8.2Z"/>
                            </svg>

                            Редактирование
                        </PrimaryButton>
                    </div>
                </div>
            </div>
            <div class="border-t">
                <TabGroup>
                    <TabList class="flex bg-white">
                        <Tab v-if="isMyProfile" v-slot="{ selected }" as="template">
                            <TabItem text="Обо мне" :selected="selected"></TabItem>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Записи" :selected="selected"></TabItem>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Подписчики" :selected="selected"></TabItem>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Последователи" :selected="selected"></TabItem>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Фото" :selected="selected"></TabItem>
                        </Tab>
                    </TabList>

                    <TabPanels class="mt-2">
                        <TabPanel v-if="isMyProfile">
                            <Edit :must-verify-email="mustVerifyEmail" :status="status"/>
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            Мои записи
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            Подписчики
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            Последователи
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            Фото
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
