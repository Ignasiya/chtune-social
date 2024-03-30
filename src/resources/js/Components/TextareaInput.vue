<script setup>
import {onMounted, ref, watch} from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        required: false,
    },
    placeholder: String,
    autoResize: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['update:modelValue']);

const input = ref(null);

function adjustHeight() {
    if (props.autoResize) {
        input.value.style.height = 'auto';
        input.value.style.height = (input.value.scrollHeight + 2) + 'px';
    }
}

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

onMounted(() => {
    adjustHeight()
})

watch(() => props.modelValue, () => {
    setTimeout(() => {
        adjustHeight()
    }, 10)
})

defineExpose({focus: () => input.value.focus()});

function onInputChange(event) {
    emit('update:modelValue', event.target.value);
}

</script>

<template>
    <textarea
        class="border-gray-300 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-300 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-[20px] shadow-sm"
        :value="modelValue"
        @input="onInputChange"
        ref="input"
        :placeholder="placeholder">
    </textarea>
</template>
