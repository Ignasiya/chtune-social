<script setup>
defineProps({
    selected: Boolean,
    text: String
});
</script>

<template>
    <button
        :class="[
          'px-3 py-2.5 outline-none text-sm',
          selected
            ? 'text-sky-600 border-b-2 border-sky-600'
            : 'text-gray-700 dark:text-gray-300',
        ]"
    >
        {{ text }}
    </button>
</template>

<style scoped>

</style>
