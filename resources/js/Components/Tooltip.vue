<script setup>
import { QuestionCircleFillIcon } from '@/Components/Icons/solid';

</script>

<template>
    <div class="tooltip-icon flex items-center justify-center">
        <div class="tooltip">
            <slot></slot>
        </div>
        <QuestionCircleFillIcon 
            class="w-4 h-4 mb-1"
        />
    </div>
</template>
