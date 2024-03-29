<script setup>
import {ref} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {Link, router, usePage} from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import UserHeader from "@/Components/UserHeader.vue";
import {MagnifyingGlassIcon, MoonIcon, BellAlertIcon, HomeIcon as HomeSol, UsersIcon as UsersSol} from
        '@heroicons/vue/24/solid'
import {HomeIcon as HomeOut, UsersIcon as UsersOut} from '@heroicons/vue/24/outline'
import Modal from "@/Components/Modal.vue";
import {Tab, TabGroup, TabList} from "@headlessui/vue";
import TabItem from "@/Components/TabItem.vue";

const showingNavigationDropdown = ref(false);
const keywords = ref(usePage().props.search || '');

const authUser = usePage().props.auth.user;

defineProps({
    menu: {
        type: Number,
        default: 2
    }
})

function search() {
    router.get(route('search', encodeURIComponent(keywords.value)))
}

const showSearch = ref(false);

function openSearch() {
    showSearch.value = true;
}

function onSearchHide() {
    showSearch.value = false;
}

function toggleDarkMode() {
    const html = window.document.documentElement
    if (html.classList.contains('dark')) {
        html.classList.remove('dark')
        localStorage.setItem('darkMode', '0')
    } else {
        html.classList.add('dark')
        localStorage.setItem('darkMode', '1')
    }
}

</script>

<template>
    <div class="h-full overflow-hidden flex flex-col bg-gray-100 dark:bg-neutral-900">
        <nav class="bg-white dark:bg-neutral-900 border-b border-gray-100 dark:border-neutral-700">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-1 items-center justify-between gap-4 h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('home')">
                                <ApplicationLogo
                                    class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-300"
                                />
                            </Link>
                        </div>
                    </div>
                    <div v-if="authUser">
                        <button
                            @click="openSearch"
                            class="flex items-center rounded-full p-1.5 bg-black/5 dark:bg-neutral-700 dark:hover:bg-neutral-800 hover:bg-gray-200 text-sky-600 hover:text-sky-500">
                            <MagnifyingGlassIcon class="w-5 h-5"/>
                        </button>
                        <Modal :show="showSearch" @close="onSearchHide">
                            <TextInput
                                class="w-full"
                                v-model="keywords"
                                @keyup.enter="search"
                                placeholder="Поиск по сайту"/>
                        </Modal>
                    </div>
                    <TabGroup :defaultIndex="menu" class="flex flex-1 justify-center" v-if="authUser">
                        <TabList class="flex">
                            <Tab v-slot="{ selected }" as="template">
                                <Link :href="route('home')">
                                <TabItem :selected="selected">
                                        <HomeSol v-if="selected" class="w-8 h-8"/>
                                        <HomeOut v-else class="w-8 h-8"/>
                                </TabItem>
                                </Link>
                            </Tab>
                            <Tab v-slot="{ selected }" as="template">
                                <Link :href="route('activity')">
                                    <TabItem :selected="selected">
                                        <UsersSol v-if="selected" class="w-8 h-8"/>
                                        <UsersOut v-else class="w-8 h-8"/>
                                    </TabItem>
                                </Link>
                            </Tab>
                            <Tab v-slot="{ selected }"/>
                        </TabList>
                    </TabGroup>
                    <button @click="toggleDarkMode"
                            class="flex items-center rounded-full p-1.5 bg-gray-100 dark:bg-neutral-700 dark:hover:bg-neutral-800 hover:bg-gray-200 text-sky-600 hover:text-sky-500">
                        <MoonIcon class="w-5 h-5"/>
                    </button>
                    <div v-if="authUser" class="ms-3 relative inline-flex items-center">
                        <button
                            @click=""
                            class="flex items-center rounded-full p-1.5 bg-black/5 dark:bg-neutral-700 dark:hover:bg-neutral-800 hover:bg-gray-200 text-sky-600 hover:text-sky-500">
                            <BellAlertIcon class="w-5 h-5"/>
                        </button>
                    </div>
                    <div class="hidden sm:flex sm:items-center">
                        <!-- Settings Dropdown -->
                        <div class="ms-3 relative inline-flex items-center">
                            <Dropdown v-if="authUser" align="right" width="32">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="flex items-center">
                                        <UserHeader :user="authUser" :link="true"/>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile', {username: authUser.username})">
                                        Профиль
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Выйти
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                            <div v-else>
                                <Link class="text-gray-700 dark:text-gray-300" :href="route('login')">
                                    Войти
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div
                :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                class="sm:hidden"
            >

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                    <template v-if="authUser">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-300">
                                {{ authUser.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ authUser.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile', {username: authUser.username})"> Профиль
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Выйти
                            </ResponsiveNavLink>
                        </div>
                    </template>
                    <template>
                        Войти
                    </template>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex 1 overflow-hidden">
            <slot/>
        </main>
    </div>
</template>
