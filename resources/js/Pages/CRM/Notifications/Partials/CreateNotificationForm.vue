<script setup>
dayjs.extend(utc);
dayjs.extend(timezone);
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { ref, onMounted, reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { cl, back, convertToIndexedValueObject, 
    setDateTimeWithOffset, setFormattedDateTimeWithOffset 
} from '@/Composables';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';
import Checkbox from '@/Components/Checkbox.vue';
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue';
import CollapsibleSection from '@/Components/CollapsibleSection.vue';
import PasswordInputField from '@/Components/PasswordInputField.vue';
import CustomTextInputField from '@/Components/CustomTextInputField.vue';
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue'
import CustomDateTimeInputField from '@/Components/CustomDateTimeInputField.vue';

// Get the errors thats passed back from controller if there are any error after backend validations
defineProps({
    errors:Object
})

const allUsers = reactive({});

const notificationTypeArray = ref({
    'PRIVATE_MESSAGE': {
        value: "Custom notification",
    },
    'NEW_ANNOUNCEMENT': {
        value: "New announcement",
    },
});

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	title: '',
	description: '',
	notification_type: '',
	attachment: '',
	is_read: false,
	edited_at: '',
	created_at: '',
	user_id: '',
});

onMounted(async () => {
  try {
    const usersResponse = await axios.get(route('notifications.getAllUsers'));
    
    // Iterate over the data and populate the allUsers array
    usersResponse.data.forEach(key => {
        allUsers[key.id] = {
            value: key.username + ' (' + key.site.name + ')'
        };
    });

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => { 
    form.edited_at = setDateTimeWithOffset(true);
	form.created_at = setDateTimeWithOffset(true);

    form.post(route('notifications.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    
    })
};


// Check and allows only the following keypressed
const isNumber = (e, withDot = true) => {
    const keysAllowed = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    if (withDot) {
        keysAllowed.push('.');
    }
    const keyPressed = e.key;
    
    if (!keysAllowed.includes(keyPressed)) {
		e.preventDefault();
    }
};

// Check if the value is not empty and is a valid number
const isValidNumber = (value) => {
    return value !== '' && !isNaN(parseFloat(value)) && isFinite(value);
};
</script>

<template>
    <div class="form-wrapper">
        <form novalidate @submit.prevent="formSubmit">
            <div class="sticky top-[72px] z-10 mb-6 border border-gray-700 rounded-md">
                <div class="dark:bg-dark-eval-1 bg-white rounded-md flex flex-row items-center pr-12 pl-14 py-2 gap-8">
                    <p class="text-xl font-semibold leading-tight">Action</p>
                    <span class="border-l border-gray-500 h-20"></span>
                    <div class="action-button-group">
                        <Button 
                            :type="'button'"
                            :variant="'info'" 
                            :size="'base'"
                            @click="back"
                            class="justify-center gap-2 form-actions"
                        >
                            <span>Back</span>   
                        </Button>
                        <Button 
                            :variant="'primary'" 
                            :size="'base'" 
                            class="justify-center gap-2 form-actions"
                            :disabled="form.processing"
                        >
                            <span>{{ form.processing ? 'Saving...' : 'Save' }}</span>
                        </Button>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 lg:gap-6">
                <div class="col-span-12 lg:col-span-12">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="flex justify-between items-center mb-2">
                            <Label 
                                :value="'Notification'" 
                                class="mt-4 !text-2xl !font-bold pl-2" 
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full" />
                        <div class="input-group-wrapper grid grid-cols-1 lg:grid-cols-12">
                            <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6 col-span-full lg:col-span-12 xl:col-span-11 2xl:col-span-7">
                                <CustomTextInputField
                                    :labelValue="'Title'"
                                    :inputId="'title'"
                                    :errorMessage="form?.errors?.title ?? '' "
                                    class="lg:col-start-1 col-span-full lg:col-span-6"
                                    v-model="form.title"
                                />
                                <CustomSelectInputField
                                    :inputArray="notificationTypeArray"
                                    :inputId="'notification_type'"
                                    :labelValue="'Notification Type'"
                                    :customValue="true"
                                    :errorMessage="form?.errors?.notification_type ?? ''"
                                    class="col-span-full lg:col-span-6"
                                    v-model="form.notification_type"
                                />
                                <CustomTextInputField
                                    :inputType="'textarea'"
                                    :inputId="'description'"
                                    :labelValue="'Description'"
                                    class="lg:col-start-1 col-span-full"
                                    :errorMessage="form?.errors?.description ?? '' "
                                    v-model="form.description"
                                />
                                <CustomSelectInputField
                                    :inputArray="allUsers"
                                    :inputId="'user_id'"
                                    :labelValue="'User'"
                                    :customValue="true"
                                    :errorMessage="form?.errors?.user_id ?? ''"
                                    class="lg:col-start-1 col-span-full lg:col-span-6"
                                    v-model="form.user_id"
                                />
                                <Checkbox
                                    v-model:checked="form.is_read"
                                    :inputId="'is_read'"
                                    :labelValue="'Notification Viewed?'"
                                    :errorMessage="form?.errors?.is_read ?? '' "
                                    class="col-span-full lg:col-span-6"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
