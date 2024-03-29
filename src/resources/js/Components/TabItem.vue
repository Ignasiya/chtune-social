<script setup>
defineProps({
    selected: Boolean,
    text: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <button
        :class="[
          'px-5 py-4 outline-none text-sm',
          selected
            ? 'text-sky-600 border-b-2 border-sky-600'
            : 'text-gray-700 dark:text-gray-300',
        ]"
    >
        {{ text }}
        <slot/>
    </button>
</template>

<style scoped>

</style>
