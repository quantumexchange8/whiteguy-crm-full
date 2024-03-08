<script setup>
import { ref, onMounted } from 'vue'
import { DashboardIcon } from '@/Components/Icons/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
// import { EyeIcon } from '@/Components/Icons/outline'
import InputError from '@/Components/InputError.vue'
import Tooltip from '@/Components/Tooltip.vue'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import { EyeIcon, EyeOffIcon, LockClosedIcon } from '@heroicons/vue/outline'

const props = defineProps({
	inputId: {
		type: String,
	},
	dataValue: {
		type: [String, Number],
		default: ''
	},
    labelValue: {
		type: String,
		default: ''
	},
    errorMessage: {
        type: String,
        default: ''
    },
    withTooltip: {
        type: Boolean,
        default: false
    },
    customTooltipContent: {
        type: Boolean,
        default: false
    },
})

const showPassword = ref(false);
const passwordInput = ref();

const emit = defineEmits(['update:modelValue'])

onMounted(() => {
    passwordInput.value = props.dataValue;
});

const toggleShow = () => {
    showPassword.value = !showPassword.value;
}
const setPasswordAndEmits = (e) => {
    passwordInput.value = e;
    emit('update:modelValue', e);
}

</script>

<template>
	<div class="input-wrapper">
        <span class="flex flex-row gap-2">
            <Label
                :value="labelValue"
                :for="inputId"
                class="mb-2"
            >
            </Label>
            <Tooltip v-if="withTooltip">
                <span v-if="customTooltipContent">
                    <slot></slot>
                </span>
                <span v-else>
                    <p class="font-bold">The password must contain:</p>
                    <ul class="pl-6 list-disc font-semibold">
                        <li>at least 8 characters.</li>
                        <li>at least one uppercase letter.</li>
                        <li>at least one lowercase letter.</li>
                        <li>at least one symbol.</li>
                        <li>at least one number.</li>
                    </ul>
                </span>
            </Tooltip>
        </span>
        <InputIconWrapper>
            <template #icon>
                <LockClosedIcon 
                    class="flex-shrink-0 w-6 h-6" 
                    aria-hidden="true" 
                />
            </template>
            <Input 
                :id="inputId"
                :name="inputId"
                :inputType="'password'"
                :withIcon="true"
                :class="'w-full'"
                :placeholder="labelValue"
                :value="passwordInput"
                @input="setPasswordAndEmits($event.target.value)"
                v-if="!showPassword"
            />
            <Input 
                :id="inputId"
                :name="inputId"
                :inputType="'text'"
                :withIcon="true"
                :class="'w-full'"
                :placeholder="labelValue"
                :value="passwordInput"
                @input="setPasswordAndEmits($event.target.value)"
                v-else
            />
            <span class="absolute inset-y-0 right-0 flex items-center px-4">
                <Button 
                    :type="'button'"
                    :variant="'transparent'"
                    :size="'xs'"
                    class="justify-center max-h-max" 
                    @click="toggleShow"
                >
                    <component 
                        :is="showPassword ? EyeOffIcon : EyeIcon" 
                        class="flex-shrink-0 w-5 h-5 cursor-pointer text-cyan-500" 
                        aria-hidden="true"
                    />
                    <!-- <EyeIcon class="flex-shrink-0 w-5 h-5 cursor-pointer text-cyan-500" aria-hidden="true" />
                    <EyeOffIcon class="flex-shrink-0 w-5 h-5 cursor-pointer text-cyan-500" aria-hidden="true" /> -->
                </Button>
            </span>
        </InputIconWrapper>
        <InputError
            :message="errorMessage"
            v-if="errorMessage"
        />
    </div>
</template>
