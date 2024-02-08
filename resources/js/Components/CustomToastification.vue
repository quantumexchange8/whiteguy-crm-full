<script setup>
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import { CheckCircleIcon, ErrorCircleIcon, InfoCircleIcon } from '@/Components/Icons/outline';

const props = defineProps({
    errors: {
        type: Object
    },
    type: {
        type: String,
        default: 'default'
    },
})

</script>

<template>
    <div class="flex flex-col items-center">
        <CheckCircleIcon class="flex-shrink-0 w-36 h-36" v-if="props.type === 'success'"/>
        <ErrorCircleIcon class="flex-shrink-0 w-36 h-36" v-else-if="props.type === 'error'"/>
        <InfoCircleIcon class="flex-shrink-0 w-36 h-36" v-else/>
        <p class="dark:text-gray-300">heluo</p>
        <pre class="dark:text-gray-300 overflow-scroll">{{ props.errors }}</pre>
    </div>
</template>
