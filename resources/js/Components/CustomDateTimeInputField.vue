<script setup>
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { onMounted, computed } from 'vue'
import '@vuepic/vue-datepicker/dist/main.css'
import VueDatePicker from '@vuepic/vue-datepicker'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import InputError from '@/Components/InputError.vue'
import { usePage } from '@inertiajs/vue3'

dayjs.extend(utc);
dayjs.extend(timezone);

const props = defineProps({
	inputId: {
		type: String,
		default: '',
	},
    labelValue: {
		type: String,
		default: '',
	},
	modelValue: {
        type: String,
        default: null,
	},
    dateTimeOpt: {
        type: Boolean,
        default: true,
    },
    errorMessage: {
        type: String,
        default: ''
    }
})

const page = usePage();

const user = computed(() => page.props.auth.user)

const emit = defineEmits(['update:modelValue']);

const formatDate = () => {
    if (props.dateTimeOpt) {
        if (page.props.auth.user.timezone) {
            return dayjs(props.modelValue).tz(page.props.auth.user.timezone).format('DD-MM-YYYY HH:mm:ss');
        }
        return dayjs(props.modelValue).tz(dayjs.tz.guess()).format('DD-MM-YYYY HH:mm:ss');
    } else {
        if (page.props.auth.user.timezone) {
            return dayjs(props.modelValue).tz(page.props.auth.user.timezone).format('DD-MM-YYYY');
        }
        return dayjs(props.modelValue).tz(dayjs.tz.guess()).format('DD-MM-YYYY');
    }
}

// Update parent component's value
const updateDateValue = (date) => {
    if (props.dateTimeOpt) {
        const formattedDate = dayjs(date).format('YYYY-MM-DD HH:mm:ss.SSSSSS');
        emit('update:modelValue', formattedDate);
    } else {
        const formattedDate = dayjs(date).format('YYYY-MM-DD');
        emit('update:modelValue', formattedDate);
    }
}

// Update parent component's value
const clearDateValue = () => {
    emit('update:modelValue', '');
}

</script>

<template>
    <div class="input-wrapper">
        <Label
            :value="labelValue"
            :for="inputId"
            class="mb-2"
        >
        </Label>
        <VueDatePicker 
            :id="inputId"
            :model-value="props.modelValue"
            :enable-time-picker="dateTimeOpt"
            @update:model-value="updateDateValue"
            @cleared="clearDateValue"
            time-picker-inline 
            v-if="dateTimeOpt === true"
            :format="formatDate"
            enable-seconds
            auto-apply
            dark
            placeholder="Select Date"
            input-class-name="py-2 focus:ring focus:ring-purple-500 focus:ring-offset-2 
                focus:ring-offset-white dark:focus:ring-offset-dark-eval-1" 
        >
        <template #hours="{ text, value }">
            {{ props.modelValue ? dayjs(props.modelValue).tz(user.timezone).hour() : value }}
        </template>
        <template #minutes="{ text, value }">
            {{ props.modelValue ? dayjs(props.modelValue).tz(user.timezone).minute() : value }}
        </template>
        <template #action-extra="{ selectCurrentDate }">
            <Button 
                :type="'button'"
                :size="'sm'"
                @click="selectCurrentDate()"
                title="Select now"
                class="justify-center gap-2 form-actions my-3"
            >
                <span>Select now</span>
            </Button>
        </template>
        </VueDatePicker>
        <VueDatePicker 
            :id="inputId"
            :model-value="props.modelValue"
            :enable-time-picker="dateTimeOpt"
            @update:model-value="updateDateValue"
            @cleared="clearDateValue"
            time-picker-inline 
            v-else
            :format="formatDate"
            auto-apply
            dark
            placeholder="Select Date"
            input-class-name="py-2 focus:ring focus:ring-purple-500 focus:ring-offset-2 
                focus:ring-offset-white dark:focus:ring-offset-dark-eval-1" 
        >
        <template #action-extra="{ selectCurrentDate }">
            <Button 
                :type="'button'"
                :size="'sm'"
                @click="selectCurrentDate()"
                title="Select now"
                class="justify-center gap-2 form-actions mb-3"
            >
                <span>Select now</span>
            </Button>
        </template>
        </VueDatePicker>
        <InputError
            :message="errorMessage"
            v-if="errorMessage"
        />
    </div>
</template>
