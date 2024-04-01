<script setup>
import { computed } from 'vue'
import Label from '@/Components/Label.vue'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import InputError from '@/Components/InputError.vue'
import { DashboardIcon } from '@/Components/Icons/outline'

const emit = defineEmits(['update:checked'])

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    defaultChecked: {
        type: Boolean,
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
        return props.defaultChecked ? true : props.checked;
    },

    set(val) {
        emit('update:checked', val)
    },
})
</script>

<template>
	<div class="input-wrapper">
        <Label
            :value="labelValue"
            :for="inputId"
            class="mb-2"
        >
        </Label>
        <input
            type="checkbox"
            :value="value"
            v-model="proxyChecked"
            class="ml-2 text-purple-500 border-gray-300 rounded focus:border-purple-300 focus:ring focus:ring-purple-500 
                    dark:border-gray-600 dark:bg-dark-eval-1 dark:focus:ring-offset-dark-eval-1 w-6 h-6"
        />
        <InputError
            :message="errorMessage"
            v-if="errorMessage"
        />
    </div>
</template>
