<script setup>
import {computed, ref,} from 'vue'
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue'
import {XMarkIcon,} from '@heroicons/vue/24/solid'
import {useForm} from "@inertiajs/vue3";
import IndigoButton from "@/Components/app/IndigoButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import TextareaInput from "@/Components/TextareaInput.vue";
import axiosClient from "@/axiosClient.js";

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
    <teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/25"/>
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div
                        class="flex min-h-full items-center justify-center p-4 text-center"
                    >
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded bg-white text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="h3"
                                    class="flex items-center justify-between py-3 px-4 font-medium bg-gray-100 text-gray-900"
                                >
                                    Создать новую группу
                                    <button
                                        @click="closeModal"
                                        class="h-8 w-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                                        <XMarkIcon class="w-4 h-4"/>
                                    </button>
                                </DialogTitle>
                                <div class="p-4">
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
                                </div>
                                <div class="py-3 px-4 flex justify-end gap-2 ">
                                    <button
                                        @click="show = false"
                                        class="text-gray-800 flex gap-1 items-center justify-center py-2 bg-gray-100 rounded-md hover:bg-gray-200 px-4"
                                    >
                                        Отмена
                                    </button>

                                    <IndigoButton @click="submit">
                                        Создать
                                    </IndigoButton>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>

<style scoped>

</style>
