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
import Checkbox2 from '@/Components/Checkbox2.vue';
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

const actionTypeArray = ref({
    'BUY': {
        value: "Buy",
    },
    'SELL': {
        value: "Sell"
    },
});
const stockTypeArray = ref({
    'BOND': {
        value: "Bonds",
    },
    'SHARE': {
        value: "Shares",
    },
    'ISA': {
        value: "ISAs",
    },
    'FUND': {
        value: "Funds",
    },
    'DIGITAL_ASSET': {
        value: "Digital Assets",
    },
    'MUTUAL_FUND': {
        value: "Mutual Fund",
    },
    'COMMODITY': {
        value: "Commodity",
    },
    'FOREX': {
        value: "Forex",
    },
    'UNKNOWN': {
        value: "Unknown"
    },
});
const statusArray = ref(convertToIndexedValueObject(
	[ "Pending", "In progress", "Active", "Cancelled", "Cancelled (approved)", "Cancelled (non-authorized)", 
    "Pending allocation", "Pending payment", "Pending clearance", "Cleared", "Trade confirmation required" ]
));
const limbStageArray  = ref(convertToIndexedValueObject(
	[ "ALLO", "Allo + docs", "TT", "CLEARED", "Cancelled", "Cancelled - bank block", "Cancelled - HTR", 
    "Cancelled - order drop", "Cancelled refuse trade", "Kicked", "Carry over", "Free switch" ]
));

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	trade_id: '',
	date: '',
	action_type: '',
	stock_type: '',
	stock: '',
	unit_price: 0.00,
	quantity: 0.00,
	current_unit_price: 0.00,
	profit: 0.00,
	status: '',
	edited_at: '',
	created_at: '',
	user_id: '',
	is_deleted: false,
	limb_stage: '',
	confirmation_name: '',
	confirmed_at: '',
	send_notification: false,
	notification_title: '',
	notification_description: '',
});

onMounted(async () => {
  try {
    const tradeIdResponse = await axios.get(route('orders.generateTradeId'));
    form.trade_id = tradeIdResponse.data.trade_id;
    
    const usersResponse = await axios.get('/data/users-clients');
    
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
    form.unit_price = isValidNumber(form.unit_price) ? parseFloat(form.unit_price) : 0;
    form.quantity = isValidNumber(form.quantity) ? parseFloat(form.quantity) : 0;
    form.current_unit_price = isValidNumber(form.current_unit_price) ? parseFloat(form.current_unit_price) : 0;
    form.profit = isValidNumber(form.profit) ? parseFloat(form.profit) : 0;
    form.date = form.date ? setFormattedDateTimeWithOffset(form.date) : form.date;
    form.confirmed_at = form.confirmed_at ? setFormattedDateTimeWithOffset(form.confirmed_at, true) : form.confirmed_at;
    form.edited_at = setDateTimeWithOffset(true);
	form.created_at = setDateTimeWithOffset(true);

    // let melbTo08Date = dayjs(form.confirmed_at).utcOffset('+08:00')
    //                     .format('YYYY-MM-DD HH:mm:ss.SSSSSSZZ')
    //                     .replace(/(\d{2})(\d{2})$/, '$1');
    // cl(melbTo08Date);

    form.post(route('orders.store'), {
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
                <div class="col-span-12 lg:col-span-6">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="flex justify-between items-center mb-2">
                            <Label 
                                :value="'Details'" 
                                class="mt-4 !text-2xl !font-bold pl-2" 
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full" />
                        <div class="input-group-wrapper">
                            <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                                <CustomTextInputField
                                    :labelValue="'Trade ID'"
                                    :inputId="'trade_id'"
                                    :withTooltip="true"
                                    :dataValue="form.trade_id"
                                    :errorMessage="form?.errors?.trade_id ?? '' "
                                    class="col-span-full md:col-span-5 lg:col-span-6"
                                    v-model="form.trade_id"
                                >
                                    Trade ID is auto generated.
                                </CustomTextInputField>
                                <CustomDateTimeInputField
                                    :labelValue="'Date'"
                                    :inputId="'date'"
                                    :dateTimeOpt="false"
                                    :errorMessage="form?.errors?.date ?? '' "
                                    class="lg:col-start-1 col-span-full lg:col-span-6"
                                    v-model="form.date"
                                />
                                <CustomSelectInputField
                                    :inputArray="actionTypeArray"
                                    :inputId="'action_type'"
                                    :labelValue="'Action Type'"
                                    :customValue="true"
                                    :errorMessage="form?.errors?.action_type ?? ''"
                                    class="lg:col-start-1 col-span-full lg:col-span-6"
                                    v-model="form.action_type"
                                />
                                <CustomSelectInputField
                                    :inputArray="stockTypeArray"
                                    :inputId="'stock_type'"
                                    :labelValue="'Stock Type'"
                                    :customValue="true"
                                    :errorMessage="form?.errors?.stock_type ?? ''"
                                    class="col-span-full lg:col-span-6"
                                    v-model="form.stock_type"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-6">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="flex justify-between items-center mb-2">
                            <Label 
                                :value="'Client & Status'" 
                                class="mt-4 !text-2xl !font-bold pl-2" 
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full" />
                        <div class="input-group-wrapper">
                            <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                                <CustomSelectInputField
                                    :inputArray="allUsers"
                                    :inputId="'user_id'"
                                    :labelValue="'Client'"
                                    :customValue="true"
                                    :errorMessage="form?.errors?.user_id ?? ''"
                                    class="col-span-full lg:col-span-6"
                                    v-model="form.user_id"
                                />
                                <CustomTextInputField
                                    :labelValue="'Client Full Name'"
                                    :inputId="'confirmation_name'"
                                    :errorMessage="form?.errors?.confirmation_name ?? '' "
                                    :withTooltip="true"
                                    class="col-span-full lg:col-span-6"
                                    v-model="form.confirmation_name"
                                >
                                    For security purpose, please enter your full name.
                                </CustomTextInputField>
                                <CustomSelectInputField
                                    :inputArray="statusArray"
                                    :inputId="'status'"
                                    :labelValue="'Status'"
                                    :customValue="true"
                                    :errorMessage="form?.errors?.status ?? ''"
                                    class="lg:col-start-1 col-span-full lg:col-span-6"
                                    v-model="form.status"
                                />
                                <CustomSelectInputField
                                    :inputArray="limbStageArray"
                                    :inputId="'limb_stage'"
                                    :labelValue="'Limb Stage'"
                                    :customValue="true"
                                    :errorMessage="form?.errors?.limb_stage ?? ''"
                                    class="col-span-full lg:col-span-6"
                                    v-model="form.limb_stage"
                                />
                                <CustomDateTimeInputField
                                    :labelValue="'Confirmated At'"
                                    :inputId="'confirmed_at'"
                                    :dateTimeOpt="true"
                                    :errorMessage="form?.errors?.confirmed_at ?? '' "
                                    class="col-span-full lg:col-span-6"
                                    v-model="form.confirmed_at"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 lg:gap-6">
                <div class="col-span-12 lg:col-span-6">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="flex justify-between items-center mb-2">
                            <Label 
                                :value="'Stock & Price'" 
                                class="mt-4 !text-2xl !font-bold pl-2" 
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full" />
                        <div class="input-group-wrapper">
                            <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                                <CustomTextInputField
                                    :labelValue="'Stock'"
                                    :inputId="'stock'"
                                    :errorMessage="form?.errors?.stock ?? '' "
                                    class="col-span-full lg:col-span-6"
                                    v-model="form.stock"
                                />
                                <CustomTextInputField
                                    :inputType="'number'"
                                    :inputId="'unit_price'"
                                    :labelValue="'Unit Price'"
                                    :dataValue="parseFloat(form.unit_price).toFixed(2)"
                                    :decimalOption="true"
                                    :step="0.01"
                                    :errorMessage="form?.errors?.unit_price ?? '' "
                                    @keypress="isNumber($event)"
                                    class="lg:col-start-1 col-span-full lg:col-span-6"
                                    v-model="form.unit_price"
                                />
                                <CustomTextInputField
                                    :inputType="'number'"
                                    :inputId="'quantity'"
                                    :labelValue="'Quantity'"
                                    :dataValue="parseFloat(form.quantity).toFixed(2)"
                                    :decimalOption="true"
                                    :step="0.01"
                                    :errorMessage="form?.errors?.quantity ?? '' "
                                    @keypress="isNumber($event)"
                                    class="col-span-full lg:col-span-6"
                                    v-model="form.quantity"
                                />
                                <CustomLabelGroup
                                    :inputId="'total_price'"
                                    :labelValue="'Total Price'"
                                    :dataValue="'Total price will be calculated when saved.'"
                                    class="col-span-full lg:col-span-6"
                                />
                                <CustomTextInputField
                                    :inputType="'number'"
                                    :inputId="'current_unit_price'"
                                    :labelValue="'Current Price'"
                                    :dataValue="parseFloat(form.current_unit_price).toFixed(2)"
                                    :decimalOption="true"
                                    :step="0.01"
                                    :errorMessage="form?.errors?.current_unit_price ?? '' "
                                    @keypress="isNumber($event)"
                                    class="lg:col-start-1 col-span-full lg:col-span-6"
                                    v-model="form.current_unit_price"
                                />
                                <CustomTextInputField
                                    :inputType="'number'"
                                    :inputId="'profit'"
                                    :labelValue="'Profit'"
                                    :dataValue="parseFloat(form.profit).toFixed(2)"
                                    :decimalOption="true"
                                    :step="0.01"
                                    :errorMessage="form?.errors?.profit ?? '' "
                                    @keypress="isNumber($event)"
                                    class="col-span-full lg:col-span-6"
                                    v-model="form.profit"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-6">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="flex justify-between items-center mb-2">
                            <Label 
                                :value="'Notification'" 
                                class="mt-4 !text-2xl !font-bold pl-2" 
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full" />
                        <div class="input-group-wrapper">
                            <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                                <Checkbox2
                                    v-model:checked="form.send_notification"
                                    :inputId="'send_notification'"
                                    :labelValue="'Send notification'"
                                    :errorMessage="form?.errors?.send_notification ?? '' "
                                    class="col-span-full lg:col-span-6"
                                />
                                <CustomTextInputField
                                    :labelValue="'Notification Title'"
                                    :inputId="'notification_title'"
                                    :errorMessage="form?.errors?.notification_title ?? '' "
                                    class="lg:col-start-1 col-span-full lg:col-span-6"
                                    v-model="form.notification_title"
                                />
                                <CustomTextInputField
                                    :inputType="'textarea'"
                                    :inputId="'notification_description'"
                                    :labelValue="'Notification Description'"
                                    :dataValue="form.notification_description"
                                    class="col-span-full"
                                    :errorMessage="form?.errors?.notification_description ?? '' "
                                    v-model="form.notification_description"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
