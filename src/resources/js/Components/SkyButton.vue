<script setup>

defineProps({
    type: {
        type:String,
        default: 'button'
    }
})

</script>

<template>
    <button
        :type="type"
        class="relative flex items-center justify-center rounded-md bg-sky-700 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-700"
    >
        <slot></slot>
    </button>
</template>

<style scoped>

</style>
