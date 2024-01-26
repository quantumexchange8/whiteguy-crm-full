<script setup>
defineProps({
    message: String
})
</script>

<template>
    <div v-show="message">
        <span class="text-xs text-red-600">
            {{ message }}
        </span>
    </div>
</template>
