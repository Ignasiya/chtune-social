<script setup>
import {computed, ref,} from 'vue'
import {useForm} from "@inertiajs/vue3";
import SkyButton from "@/Components/SkyButton.vue";
import axiosClient from "@/axiosClient.js";
import GroupForm from "@/Pages/Group/Partials/GroupForm.vue";
import BaseModal from "@/Components/BaseModal.vue";

const props = defineProps({
    modelValue: Boolean
});

const formErrors = ref({});
const form = useForm({
    name: '',
    auto_approval: true,
    about: '',
});

const emit = defineEmits(['update:modelValue', 'hide', 'create'])

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

function closeModal() {
    show.value = false;
    emit('hide');
    resetModal();
}

function resetModal() {
    form.reset();
    formErrors.value = {};
}

function submit() {
    axiosClient.post(route('group.create'), form)
        .then(({data}) => {
            closeModal();
            emit('create', data)
        });
}

</script>

<template>
    <BaseModal v-model="show" @hide="closeModal" title="Создать группу">
        <template #body>
            <GroupForm :form="form"/>
        </template>
        <template #button>
            <button
                @click="show = false"
                class="text-gray-800 flex gap-1 items-center justify-center py-2 bg-gray-100 rounded-md hover:bg-gray-200 px-4"
            >
                Отмена
            </button>

            <SkyButton @click="submit">
                Создать
            </SkyButton>
        </template>
    </BaseModal>
</template>

<style scoped>

</style>
