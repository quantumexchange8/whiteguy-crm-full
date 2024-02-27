<script setup>
import { computed } from 'vue'
import InputError from '@/Components/InputError.vue'

const emit = defineEmits(['update:checked'])

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        default: null,
    },
    labelValue: {
		type: String,
		default: ''
	},
	inputId: {
		type: String,
	},
    errorMessage: {
        type: String,
        default: ''
    }
})

const proxyChecked = computed({
    get() {
        return props.checked
    },

    set(val) {
        emit('update:checked', val)
    },
})
</script>

<template>
	<div class="input-wrapper ">
        <div class="flex items-center">
            <label 
                :for="inputId" 
                class="checkbox-wrapper"
            >
                <input
                    type="checkbox"
                    :value="value"
                    v-model="proxyChecked"
                    class="checkbox-input"
                />
                <span class="checkbox-tile">
                    <span class="checkbox-label">{{ labelValue }}</span>
                </span>
            </label>
        </div>
        <InputError
            :message="errorMessage"
            v-if="errorMessage"
        />
    </div>
</template>
