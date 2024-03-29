<script setup>
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

const editor = ClassicEditor;
const editorConfig = {
    toolbar: ['bold', 'italic', '|', 'bulletedList', 'numberedList', '|', 'heading', '|', 'link', '|', 'blockQuote',]
};

defineProps({
    form: Object
})

</script>

<template>
    <div class="mb-3">
        <label class="text-gray-700 dark:text-gray-300">
            Название группы
        </label>
        <TextInput
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            required
            autofocus
        />
    </div>
    <div class="mb-3">
        <label class="text-gray-700 dark:text-gray-300">
            <Checkbox
                name="approval"
                v-model:checked="form.auto_approval"
            />
            Включить автоодобрение
        </label>
    </div>
    <div class="mb-3">
        <label class="text-gray-700 dark:text-gray-300">
            Описание группы
        </label>
        <ckeditor
            :editor="editor"
            v-model="form.about"
            :config="editorConfig">
        </ckeditor>
    </div>
</template>

<style scoped>

</style>
