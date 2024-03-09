<script setup>

import {ref} from "vue";
import TextareaInput from "@/Components/TextareaInput.vue";
import {useForm} from "@inertiajs/vue3";

const postCreating = ref(false);
const newPostForm = useForm({
    body: ''
});

function submit() {
    newPostForm.post(route('post.create'), {
        onSuccess: () => {
            newPostForm.reset()
        }
    });
}

</script>

<template>
    <div class="p-4 bg-white rounded-lg border mb-3">

        <textarea-input
            @click="postCreating = true"
            class="mb-3 w-full"
            placeholder="Новая запись"
            rows="1"
            v-model="newPostForm.body">
        </textarea-input>

        <div v-if="postCreating" class="flex gap-2 justify-between">
            <button
                type="button"
                class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 relative">
                Вложить файлы
                <input type="file" class="absolute left-0 top-0 right-0 bottom-0 opacity-0">
            </button>
            <button
                @click="submit"
                type="submit"
                class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Опубликовать
            </button>
        </div>
    </div>
</template>

<style scoped>

</style>
