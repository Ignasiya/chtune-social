<script setup>
import {computed, ref,} from 'vue'
import {useForm, usePage} from "@inertiajs/vue3";
import SkyButton from "@/Components/SkyButton.vue";
import TextInput from "@/Components/TextInput.vue";
import BaseModal from "@/Components/BaseModal.vue";

const props = defineProps({
    modelValue: Boolean
});

const page = usePage();
const formErrors = ref({});
const form = useForm({
    email: '',
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
    form.post(route('group.inviteUsers', page.props.group.slug), {
        onSuccess(res) {
            closeModal();
            console.log(res);
        },
        onError(res) {
            console.log(res);
        }
    })
}

</script>

<template>
    <BaseModal v-model="show" @hide="closeModal" title="Пригласить пользователя">
        <template #body>
            <div class="mb-3">
                <label class="text-gray-700 dark:text-gray-300">
                    Логин или email
                </label>
                <TextInput
                    type="text"
                    class="mt-1 block w-full"
                    :class="page.props.errors.email ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                    v-model="form.email"
                    required
                    autofocus
                />
                <div class="text-red-500">{{ page.props.errors.email }}</div>
            </div>
        </template>

        <template #button>
            <button
                @click="show = false"
                class="text-sm font-semibold shadow-sm text-gray-800 flex gap-1 items-center justify-center py-2 bg-gray-100 rounded-md hover:bg-gray-200 px-4"
            >
                Отмена
            </button>

            <SkyButton @click="submit">
                Отправить
            </SkyButton>
        </template>
    </BaseModal>
</template>

<style scoped>

</style>
