<script setup>
import {computed} from 'vue'
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue'
import {XMarkIcon,} from '@heroicons/vue/24/solid'

const props = defineProps({
    title: String,
    modelValue: Boolean
});

const emit = defineEmits(['update:modelValue', 'hide'])

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

function closeModal() {
    show.value = false;
    emit('hide');
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
                    <div class="fixed inset-0 bg-black/50"/>
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
                                class="w-full max-w-md sm:max-w-lg transform overflow-hidden rounded-lg bg-white dark:bg-neutral-900 text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="h3"
                                    class="flex items-center justify-between py-3 px-4 font-medium bg-gray-100 dark:bg-neutral-800 text-gray-900 dark:text-gray-400 border-b border-gray-300 dark:border-neutral-700"
                                >
                                    {{ title }}
                                    <button
                                        @click="closeModal"
                                        class="h-8 w-8 rounded-full hover:bg-black/5 dark:hover:bg-white/5 transition flex items-center justify-center">
                                        <XMarkIcon class="w-4 h-4"/>
                                    </button>
                                </DialogTitle>
                                <div class="p-4">
                                    <slot name="body"/>
                                </div>

                                <div class="py-3 px-4 flex justify-end gap-2 ">
                                    <slot name="button"/>
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
