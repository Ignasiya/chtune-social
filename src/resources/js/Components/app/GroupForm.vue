<script setup>
import TextareaInput from "@/Components/TextareaInput.vue";
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";

defineProps({
    form: Object
})

</script>

<template>
    <div class="mb-3">
        <label>
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
        <label>
            <Checkbox
                name="approval"
                v-model:checked="form.auto_approval"
            />
            Включить автоодобрение
        </label>
    </div>
    <div class="mb-3">
        <label>
            Описание группы
        </label>
        <TextareaInput
            class="w-full"
            v-model="form.about"
        />
    </div>
</template>

<style scoped>

</style>
