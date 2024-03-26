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
import {useForm, usePage} from "@inertiajs/vue3";
import IndigoButton from "@/Components/IndigoButton.vue";
import TextInput from "@/Components/TextInput.vue";

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
                                    Пригласить пользователя
                                    <button
                                        @click="closeModal"
                                        class="h-8 w-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                                        <XMarkIcon class="w-4 h-4"/>
                                    </button>
                                </DialogTitle>
                                <div class="p-4">
                                    <div class="mb-3">
                                        <label>
                                            Логин или email
                                        </label>
                                        <TextInput
                                            type="text"
                                            class="mt-1 block w-full"
                                            :class="page.props.errors.email ?
                                            'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            v-model="form.email"
                                            required
                                            autofocus
                                        />
                                        <div class="text-red-500">{{ page.props.errors.email }}</div>
                                    </div>
                                </div>
                                <div class="py-3 px-4 flex justify-end gap-2 ">
                                    <button
                                        @click="show = false"
                                        class="text-sm font-semibold shadow-sm text-gray-800 flex gap-1 items-center justify-center py-2 bg-gray-100 rounded-md hover:bg-gray-200 px-4"
                                    >
                                        Отмена
                                    </button>

                                    <IndigoButton @click="submit">
                                        Отправить
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
