<script setup>
// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    errors:Object,
    data: {
        type: Object,
        default: () => ({}),
    },
    leadFrontData: {
        type: Object,
        default: () => ({}),
    },
    leadNotesData: {
        type: Object,
        default: () => ({}),
    },
})
</script>

<template>
    <div class="form-wrapper">
        <p>edit lead front here</p>
    </div>
</template>
