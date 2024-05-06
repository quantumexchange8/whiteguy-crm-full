<script setup>
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { ref, onMounted, reactive, computed, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { 
    cl, back, populateArrayFromResponse, convertToIndexedValueObject, 
    setDateTimeWithOffset, setFormattedDateTimeWithOffset, formatToUserTimezone
} from '@/Composables'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Checkbox2 from '@/Components/Checkbox2.vue'
import { TimesCircleIcon } from '@/Components/Icons/solid';
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import CustomLabelGroup2 from '@/Components/CustomLabelGroup2.vue'
import PasswordInputField from '@/Components/PasswordInputField.vue'
import CollapsibleSection from '@/Components/CollapsibleSection.vue'
import BaseTextInputField from '@/Components/BaseTextInputField.vue'
import BaseSelectInputField from '@/Components/BaseSelectInputField.vue'
import CustomTextInputField from '@/Components/CustomTextInputField.vue'
import CustomTextInputField2 from '@/Components/CustomTextInputField2.vue'
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue'
import CustomSelectInputField2 from '@/Components/CustomSelectInputField2.vue'
import CustomDateTimeInputField from '@/Components/CustomDateTimeInputField.vue';
import CustomDateTimeInputField2 from '@/Components/CustomDateTimeInputField2.vue';

dayjs.extend(utc);
dayjs.extend(timezone);

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    errors:Object,
    data: {
        type: Object,
        default: () => ({}),
    },
    saleOrderItemsData: {
        type: Object,
        default: () => ({}),
    },
})

const siteArray  = reactive({});
const usersList  = reactive({});
const orderTypeArr  = ref([
	{ 
        'id': 'IN',
        'value': 'IN' 
    },
	{ 
        'id': 'OUT',
        'value': 'OUT' 
    }
]);

const currencyPairsArr  = ref([
	{ 
        'id': 'USDEUR',
        'value': 'USD/EUR' 
    },
	{ 
        'id': 'USDGBP',
        'value': 'USD/GBP' 
    },
	{ 
        'id': 'USDAUD',
        'value': 'USD/AUD' 
    },
	{ 
        'id': 'USDNZD',
        'value': 'USD/NZD' 
    }
]);

const page = usePage();
const user = computed(() => page.props.auth.user)

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	written_date: props.data.written_date,
	vc: props.data.vc,
	room_number: props.data.room_number,
	allo: props.data.allo,
	allo_name: props.data.allo_name,
	sm_number: props.data.sm_number,
	gm_number: props.data.gm_number,
	fm_number: props.data.fm_number,
	lm_number: props.data.lm_number,
	ao_1: props.data.ao_1,
	ao_1_name: props.data.ao_1_name,
	ao_2: props.data.ao_2,
	ao_2_name: props.data.ao_2_name,
	bonus_comment: props.data.bonus_comment,
	currency_pair: props.data.currency_pair,
	exchange_rate: props.data.exchange_rate,
	registered_name: props.data.registered_name,
	contact_name: props.data.contact_name,
	office_number_1: props.data.office_number_1,
	office_number_2: props.data.office_number_2,
	home_number: props.data.home_number,
	mobile_number: props.data.mobile_number,
	fax_number: props.data.fax_number,
	date_of_birth: props.data.date_of_birth,
	email: props.data.email,
	address_1: props.data.address_1,
	address_2: props.data.address_2,
	city: props.data.city,
	country: props.data.country,
	exit_time_frame: props.data.exit_time_frame,
	sell_price: props.data.sell_price,
	allocation_officer: props.data.allocation_officer,
	trade_plus: props.data.trade_plus,
	settlement_date: props.data.settlement_date,
	factory: props.data.factory,
	pay_via: props.data.pay_via,
	allo_comment: props.data.allo_comment,
	docs_received: props.data.docs_received,
	tc_sent: props.data.tc_sent,
	tt_received: props.data.tt_received,
	edited_at: props.data.edited_at,
	created_at: props.data.created_at,
	balance_due: props.data.balance_due,
	exchanged_balance_due: props.data.exchanged_balance_due,
	site_id: props.data.site_id,
	se_name: props.data.se_name,
	se_number: props.data.se_number,
	created_by_id: props.data.created_by_id,
    sale_order_items: (props.saleOrderItemsData) ? props.saleOrderItemsData : [],
});

onMounted(async () => {
    try {
        const siteResponse = await axios.get(route('sale-orders.getAllSites'));
        populateArrayFromResponse(siteResponse.data, siteArray, 'id', 'domain');

        const usersListResponse = await axios.get(route('sale-orders.getAllUsers'));
        populateArrayFromResponse(usersListResponse.data, usersList, 'id', 'value');

    } catch (error) {
        console.error("Error fetching data:", error);
    }
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
    form.written_date ? setFormattedDateTimeWithOffset(form.written_date) : form.written_date;
    form.edited_at = setDateTimeWithOffset(true);
    form.created_at = setDateTimeWithOffset(true);

    if (form.sale_order_items.length > 0) {
        form.sale_order_items.forEach(item => {
            item.price = isValidNumber(item.price) ? parseFloat(item.price) : item.price;
            item.exchanged_price = isValidNumber(item.exchanged_price) ? parseFloat(item.exchanged_price) : item.exchanged_price;
            item.quantity = isValidNumber(item.quantity) ? parseFloat(item.quantity) : item.quantity;
            item.subtotal = isValidNumber(item.subtotal) ? parseFloat(item.price * item.quantity) : item.subtotal;
            item.commission_rate = isValidNumber(item.commission_rate) ? parseFloat(item.commission_rate) : item.commission_rate;
            item.commission = isValidNumber(item.commission) ? parseFloat((item.commission_rate / 100) * (item.price * item.quantity)) : item.commission;
            item.total_exchanged_price = isValidNumber(item.total_exchanged_price) ? parseFloat((item.price * item.quantity) + ((item.commission_rate / 100) * (item.price * item.quantity))) : item.total_exchanged_price;
            item.total_price = isValidNumber(item.total_price) ? parseFloat((item.price * item.quantity) + ((item.commission_rate / 100) * (item.price * item.quantity))) : item.total_price;
            item.edited_at = setDateTimeWithOffset(true);
            item.created_at = (item.created_at === '') 
                                ? item.created_at = setDateTimeWithOffset(true) 
                                : setFormattedDateTimeWithOffset(item.created_at, true);
            
        });
    }

    form.post(route('sale-orders.update'), {
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

// const updateItemValues = (e, item, withDot = true) => {
//     isNumber(e);
//     cl(e.target.value);
//     cl(item.subtotal);
// };

// Check if the value is not empty and is a valid number
const isValidNumber = (value) => {
    return value !== '' && !isNaN(parseFloat(value)) && isFinite(value);
};

const removeItem = (i) => {
    form.sale_order_items.splice(i, 1)

    // if (form.sale_order_items.length === 0) {
    //     setTimeout(() => {
    //         addItem();
    //     }, 0);
    // }
}
const addItem = () => {
    form.sale_order_items.push({
        'order_type': '',
        'product': '',
        'price': parseFloat(0.00),
        'exchanged_price': parseFloat(0.00),
        'quantity': parseFloat(0.00),
        'subtotal': parseFloat(0.00),
        'commission_rate': parseFloat(1.00),
        'commission': parseFloat(0.00),
        'total_exchanged_price': parseFloat(0.00),
        'total_price': parseFloat(0.00),
    });
}

// Filtered array containing only new lead notes
// const filteredSaleOrderItems = computed(() => {
//   const existingSaleOrderItemIds = props.saleOrderItemsData.map(item => item.id)
//   return form.sale_order_items.filter(item => !existingSaleOrderItemIds.includes(item.id))
// })

const balanceDue = computed(() => {
    let totalBalanceDue = 0;
    form.sale_order_items.forEach(item => {
        totalBalanceDue += (item.price * item.quantity) + ((item.commission_rate / 100) * (item.price * item.quantity));
    });
    return totalBalanceDue;
  }
)
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
            <div class="form-input-section dark:bg-dark-eval-1">
                <div class="flex justify-between items-center mb-2">
                    <Label 
                        :value="'Sale Order Details'" 
                        class="mt-4 !text-2xl !font-bold pl-2" 
                    >
                    </Label>
                </div>
                <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                <div class="input-group-wrapper flex flex-col gap-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <CustomSelectInputField2
                            :inputArray="siteArray"
                            :inputId="'site_id'"
                            :labelValue="'Website'"
                            :customValue="true"
                            :dataValue="props.data.site_id"
                            :errorMessage="form?.errors?.site_id ?? ''"
                            class="col-span-full lg:col-span-3"
                            v-model="form.site_id"
                        />
                        <CustomDateTimeInputField2
                            :labelValue="'Written Date'"
                            :inputId="'written_date'"
                            :dateTimeOpt="false"
                            :dataValue="props.data.written_date"
                            :errorMessage="form?.errors?.written_date ?? '' "
                            class="col-span-full lg:col-span-3"
                            v-model="form.written_date"
                        />
                    </div>
                    <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <CustomTextInputField2
                            :labelValue="'VC'"
                            :inputId="'vc'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.vc"
                            :errorMessage="form?.errors?.vc ?? '' "
                            v-model="form.vc"
                        />
                        <CustomTextInputField2
                            :labelValue="'Room #'"
                            :inputId="'room_number'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.room_number"
                            :errorMessage="form?.errors?.room_number ?? '' "
                            v-model="form.room_number"
                        />
                        <CustomTextInputField2
                            :labelValue="'Allo'"
                            :inputId="'allo'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.allo"
                            :errorMessage="form?.errors?.allo ?? '' "
                            v-model="form.allo"
                        />
                        <CustomTextInputField2
                            :labelValue="'Allo Name'"
                            :inputId="'allo_name'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.allo_name"
                            :errorMessage="form?.errors?.allo_name ?? '' "
                            v-model="form.allo_name"
                        />
                    </div>
                    <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <CustomTextInputField
                            :labelValue="'SM #'"
                            :inputId="'sm_number'"
                            class="col-span-full lg:col-span-3"
                            :dataValue="props.data.sm_number"
                            :errorMessage="form?.errors?.sm_number ?? '' "
                            v-model="form.sm_number"
                        />
                        <CustomTextInputField
                            :labelValue="'GM #'"
                            :inputId="'gm_number'"
                            class="col-span-full lg:col-span-3"
                            :dataValue="props.data.gm_number"
                            :errorMessage="form?.errors?.gm_number ?? '' "
                            v-model="form.gm_number"
                        />
                        <CustomTextInputField
                            :labelValue="'AO #1'"
                            :inputId="'ao_1'"
                            class="col-span-full lg:col-span-3"
                            :dataValue="props.data.ao_1"
                            :errorMessage="form?.errors?.ao_1 ?? '' "
                            v-model="form.ao_1"
                        />
                        <CustomTextInputField
                            :labelValue="'AO #1 Name'"
                            :inputId="'ao_1_name'"
                            class="col-span-full lg:col-span-3"
                            :dataValue="props.data.ao_1_name"
                            :errorMessage="form?.errors?.ao_1_name ?? '' "
                            v-model="form.ao_1_name"
                        />
                        <CustomTextInputField
                            :labelValue="'FM #'"
                            :inputId="'fm_number'"
                            class="col-start-1 col-span-full lg:col-span-3"
                            :dataValue="props.data.fm_number"
                            :errorMessage="form?.errors?.fm_number ?? '' "
                            v-model="form.fm_number"
                        />
                        <CustomTextInputField
                            :labelValue="'LM #'"
                            :inputId="'lm_number'"
                            class="col-span-full lg:col-span-3"
                            :dataValue="props.data.lm_number"
                            :errorMessage="form?.errors?.lm_number ?? '' "
                            v-model="form.lm_number"
                        />
                        <CustomTextInputField
                            :labelValue="'AO #2'"
                            :inputId="'ao_2'"
                            class="col-span-full lg:col-span-3"
                            :dataValue="props.data.ao_2"
                            :errorMessage="form?.errors?.ao_2 ?? '' "
                            v-model="form.ao_2"
                        />
                        <CustomTextInputField
                            :labelValue="'AO #2 Name'"
                            :inputId="'ao_2_name'"
                            class="col-span-full lg:col-span-3"
                            :dataValue="props.data.ao_2_name"
                            :errorMessage="form?.errors?.ao_2_name ?? '' "
                            v-model="form.ao_2_name"
                        />
                        <CustomTextInputField
                            :labelValue="'SE #'"
                            :inputId="'se_number'"
                            class="col-span-full lg:col-span-3"
                            :dataValue="props.data.se_number"
                            :errorMessage="form?.errors?.se_number ?? '' "
                            v-model="form.se_number"
                        />
                        <CustomTextInputField
                            :labelValue="'SE Name'"
                            :inputId="'se_name'"
                            class="col-span-full lg:col-span-3"
                            :dataValue="props.data.se_name"
                            :errorMessage="form?.errors?.se_name ?? '' "
                            v-model="form.se_name"
                        />
                    </div>
                    <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <CustomTextInputField2
                            :inputType="'textarea'"
                            :labelValue="'Bonus Comments'"
                            :inputId="'bonus_comment'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.bonus_comment"
                            :errorMessage="form?.errors?.bonus_comment ?? '' "
                            v-model="form.bonus_comment"
                        />
                        <div class="col-span-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <CustomSelectInputField2
                                :inputArray="currencyPairsArr"
                                :inputId="'currency_pair'"
                                :labelValue="'Exchange rate for'"
                                :customValue="true"
                                :errorMessage="form?.errors?.currency_pair ?? ''"
                                :dataValue="props.data.currency_pair"
                                class="col-span-full !col-start-1 lg:col-span-7 self-start"
                                v-model="form.currency_pair"
                            />
                            <BaseTextInputField
                                :inputType="'number'"
                                :inputId="'exchange_rate'"
                                :decimalOption="true"
                                :step="0.01"
                                @keypress="isNumber($event)"
                                class="col-span-full lg:col-span-5"
                                :dataValue="props.data.exchange_rate"
                                :errorMessage="form?.errors?.exchange_rate ?? '' "
                                v-model="form.exchange_rate"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-input-section dark:bg-dark-eval-1">
                <div class="flex justify-between items-center mb-2">
                    <Label 
                        :value="'Orders'" 
                        class="mt-4 !text-xl !font-semibold pl-2" 
                    >
                    </Label>
                    <span class="text-xs font-thin pl-4 self-end pb-1">
                        ( Total Items: {{ form.sale_order_items.length }} )
                    </span>
                </div>
                <div class="input-group-wrapper">
                    <div class="input-group overflow-x-auto">
                        <table 
                            class="table text-sm w-full" 
                            :class="[{
                                'table-fixed': form.sale_order_items.length == 0,
                            }]"
                        >
                            <thead class="bg-gray-600 text-gray-300 rounded-md">
                                <tr class="w-full">
                                    <th scope="col" class="text-xs text-nowrap">IN/OUT <span class="text-red-500">*</span></th>
                                    <th scope="col" class="text-xs text-nowrap">PRODUCT <span class="text-red-500">*</span></th>
                                    <th scope="col" class="text-xs text-nowrap">PRICE <span class="text-red-500">*</span></th>
                                    <th scope="col" class="text-xs text-nowrap">US$/EU <span class="text-red-500">*</span></th>
                                    <th scope="col" class="text-xs text-nowrap">QUANTITY <span class="text-red-500">*</span></th>
                                    <th scope="col" class="text-xs text-nowrap">SUBTOTAL <span class="text-red-500">*</span></th>
                                    <th scope="col" class="text-xs text-nowrap">COMMISSION % <span class="text-red-500">*</span></th>
                                    <th scope="col" class="text-xs text-nowrap">COMMISSION <span class="text-red-500">*</span></th>
                                    <th scope="col" class="text-xs text-nowrap">TOTAL EU <span class="text-red-500">*</span></th>
                                    <th scope="col" class="text-xs text-nowrap">TOTAL US$ <span class="text-red-500">*</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-600">
                                <tr class="lg:text-black" v-for="(item, i) in form.sale_order_items" :key="i">
                                    <td class="py-2">
                                        <BaseSelectInputField
                                            :inputArray="orderTypeArr"
                                            :inputId="'order_type_'+(i+1)"
                                            :customValue="true"
                                            :dataValue="orderTypeArr.findIndex(items => items.id === item.order_type)"
                                            :errorMessage="(form.errors) ? form.errors['sale_order_items.' + (i+1) + '.order_type']  : '' "
                                            class="col-span-full lg:col-span-2"
                                            v-model="item.order_type"
                                        />
                                    </td>
                                    <td class="py-2">
                                        <BaseTextInputField
                                            :inputId="'product_'+(i+1)"
                                            class="col-span-full lg:col-span-3"
                                            :dataValue="item.product"
                                            :errorMessage="(form.errors) ? form.errors['sale_order_items.' + (i+1) + '.product']  : '' "
                                            v-model="item.product"
                                        />
                                    </td>
                                    <td>
                                        <BaseTextInputField
                                            :inputType="'number'"
                                            :inputId="'price_'+(i+1)"
										    :dataValue="parseFloat(item.price).toFixed(2)"
                                            :decimalOption="true"
                                            :step="0.01"
                                            @keypress="isNumber($event)"
                                            class="col-span-full lg:col-span-3"
                                            :errorMessage="(form.errors) ? form.errors['sale_order_items.' + (i+1) + '.price']  : '' "
                                            v-model="item.price"
                                        />
                                    </td>
                                    <td>
                                        <BaseTextInputField
                                            :inputType="'number'"
                                            :inputId="'exchanged_price_'+(i+1)"
										    :dataValue="parseFloat(item.exchanged_price).toFixed(2)"
                                            :decimalOption="true"
                                            :step="0.01"
                                            @keypress="isNumber($event)"
                                            class="col-span-full lg:col-span-3"
                                            :errorMessage="(form.errors) ? form.errors['sale_order_items.' + (i+1) + '.exchanged_price']  : '' "
                                            v-model="item.exchanged_price"
                                        />
                                    </td>
                                    <td>
                                        <BaseTextInputField
                                            :inputType="'number'"
                                            :inputId="'quantity_'+(i+1)"
										    :dataValue="parseFloat(item.quantity).toFixed(2)"
                                            :decimalOption="true"
                                            :step="0.01"
                                            @keypress="isNumber($event)"
                                            class="col-span-full lg:col-span-3"
                                            :errorMessage="(form.errors) ? form.errors['sale_order_items.' + (i+1) + '.quantity']  : '' "
                                            v-model="item.quantity"
                                        />
                                    </td>
                                    <td>
                                        <BaseTextInputField
                                            :inputType="'number'"
                                            :inputId="'subtotal_'+(i+1)"
										    :dataValue="parseFloat(item.price * item.quantity).toFixed(2)"
                                            :decimalOption="true"
                                            :step="0.01"
                                            @keypress="isNumber($event)"
                                            class="col-span-full lg:col-span-3"
                                            :errorMessage="(form.errors) ? form.errors['sale_order_items.' + (i+1) + '.subtotal']  : '' "
                                            v-model="item.subtotal"
                                        />
                                    </td>
                                    <td>
                                        <BaseTextInputField
                                            :inputType="'number'"
                                            :inputId="'commission_rate_'+(i+1)"
										    :dataValue="parseFloat(item.commission_rate).toFixed(2)"
                                            :decimalOption="true"
                                            :step="0.01"
                                            @keypress="isNumber($event)"
                                            class="col-span-full lg:col-span-3"
                                            :errorMessage="(form.errors) ? form.errors['sale_order_items.' + (i+1) + '.commission_rate']  : '' "
                                            v-model="item.commission_rate"
                                        />
                                    </td>
                                    <td>
                                        <BaseTextInputField
                                            :inputType="'number'"
                                            :inputId="'commission_'+(i+1)"
										    :dataValue="parseFloat((item.commission_rate / 100) * (item.price * item.quantity)).toFixed(2)"
                                            :decimalOption="true"
                                            :step="0.01"
                                            @keypress="isNumber($event)"
                                            class="col-span-full lg:col-span-3"
                                            :errorMessage="(form.errors) ? form.errors['sale_order_items.' + (i+1) + '.commission']  : '' "
                                            v-model="item.commission"
                                        />
                                    </td>
                                    <td>
                                        <BaseTextInputField
                                            :inputType="'number'"
                                            :inputId="'total_exchanged_price_'+(i+1)"
										    :dataValue="parseFloat((item.price * item.quantity) + ((item.commission_rate / 100) * (item.price * item.quantity))).toFixed(2)"
                                            :decimalOption="true"
                                            :step="0.01"
                                            @keypress="isNumber($event)"
                                            class="col-span-full lg:col-span-3"
                                            :errorMessage="(form.errors) ? form.errors['sale_order_items.' + (i+1) + '.total_exchanged_price']  : '' "
                                            v-model="item.total_exchanged_price"
                                        />
                                    </td>
                                    <td>
                                        <BaseTextInputField
                                            :inputType="'number'"
                                            :inputId="'total_price_'+(i+1)"
										    :dataValue="parseFloat((item.price * item.quantity) + ((item.commission_rate / 100) * (item.price * item.quantity))).toFixed(2)"
                                            :decimalOption="true"
                                            :step="0.01"
                                            @keypress="isNumber($event)"
                                            class="col-span-full lg:col-span-3"
                                            :errorMessage="(form.errors) ? form.errors['sale_order_items.' + (i+1) + '.total_price']  : '' "
                                            v-model="item.total_price"
                                        />
                                    </td>
                                    <td>
                                        <Button 
                                            :type="'button'"
                                            :variant="'transparent'" 
                                            :size="'sm'" 
                                            class="justify-center gap-2 w-full"
                                            @click="removeItem(i)"
                                        >
                                            <TimesCircleIcon class="flex-shrink-0 w-5 h-5 cursor-pointer" aria-hidden="true" />
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="!py-2">
                                    <td colspan="7" class="pl-3.5 py-2.5">
                                        <Button 
                                            :type="'button'"
                                            :variant="'success'" 
                                            :size="'sm'"
                                            @click="addItem"
                                            class="justify-center gap-2 px-8"
                                        >
                                            <span>Add Row</span>
                                        </Button>
                                    </td>
                                    <td>
                                        <Label 
                                            :value="'Balance Due:'" 
                                            class="!text-sm pl-3.5" 
                                        >
                                        </Label>
                                    </td>
                                    <td>
                                        <BaseTextInputField
                                            :inputType="'number'"
                                            :inputId="'exchanged_balance_due'"
										    :dataValue="parseFloat(balanceDue).toFixed(2)"
                                            :decimalOption="true"
                                            :step="0.01"
                                            @keypress="isNumber($event)"
                                            class=""
                                            :errorMessage="form?.errors?.exchanged_balance_due ?? '' "
                                            v-model="form.exchanged_balance_due"
                                        />
                                    </td>
                                    <td>
                                        <BaseTextInputField
                                            :inputType="'number'"
                                            :inputId="'balance_due'"
										    :dataValue="parseFloat(balanceDue).toFixed(2)"
                                            :decimalOption="true"
                                            :step="0.01"
                                            @keypress="isNumber($event)"
                                            class=""
                                            :errorMessage="form?.errors?.balance_due ?? '' "
                                            v-model="form.balance_due"
                                        />
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-input-section dark:bg-dark-eval-1">
                <div class="input-group-wrapper flex flex-col gap-6">
                    <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <CustomTextInputField2
                            :labelValue="'Registered Name *'"
                            :inputId="'registered_name'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.registered_name"
                            :errorMessage="form?.errors?.registered_name ?? '' "
                            v-model="form.registered_name"
                        />
                        <CustomTextInputField2
                            :labelValue="'Contact Name *'"
                            :inputId="'contact_name'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.contact_name"
                            :errorMessage="form?.errors?.contact_name ?? '' "
                            v-model="form.contact_name"
                        />
                        <CustomTextInputField2
                            :labelValue="'Office Number'"
                            :inputId="'office_number_1'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.office_number_1"
                            :errorMessage="form?.errors?.office_number_1 ?? '' "
                            v-model="form.office_number_1"
                        />
                        <CustomDateTimeInputField2
                            :labelValue="'Date Of Birth'"
                            :inputId="'date_of_birth'"
                            :dateTimeOpt="false"
                            :dataValue="props.data.date_of_birth"
                            :errorMessage="form?.errors?.date_of_birth ?? '' "
                            class="col-span-full lg:col-span-6"
                            v-model="form.date_of_birth"
                        />
                        <CustomTextInputField2
                            :labelValue="'Office Number 2'"
                            :inputId="'office_number_2'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.office_number_2"
                            :errorMessage="form?.errors?.office_number_2 ?? '' "
                            v-model="form.office_number_2"
                        />
                        <CustomTextInputField2
                            :labelValue="'Home Number *'"
                            :inputId="'home_number'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.home_number"
                            :errorMessage="form?.errors?.home_number ?? '' "
                            v-model="form.home_number"
                        />
                        <CustomTextInputField2
                            :labelValue="'Fax'"
                            :inputId="'fax_number'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.fax_number"
                            :errorMessage="form?.errors?.fax_number ?? '' "
                            v-model="form.fax_number"
                        />
                        <CustomTextInputField2
                            :labelValue="'Mobile Number *'"
                            :inputId="'mobile_number'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.mobile_number"
                            :errorMessage="form?.errors?.mobile_number ?? '' "
                            v-model="form.mobile_number"
                        />
                        <CustomTextInputField2
                            :labelValue="'Email *'"
                            :inputId="'email'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.email"
                            :errorMessage="form?.errors?.email ?? '' "
                            v-model="form.email"
                        />
                    </div>
                    <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <CustomTextInputField2
                            :inputType="'textarea'"
                            :labelValue="'Address 1 *'"
                            :inputId="'address_1'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.address_1"
                            :errorMessage="form?.errors?.address_1 ?? '' "
                            v-model="form.address_1"
                        />
                        <CustomTextInputField2
                            :inputType="'textarea'"
                            :labelValue="'Address 2 *'"
                            :inputId="'address_2'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.address_2"
                            :errorMessage="form?.errors?.address_2 ?? '' "
                            v-model="form.address_2"
                        />
                        <CustomTextInputField2
                            :labelValue="'City / State / PC *'"
                            :inputId="'city'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.city"
                            :errorMessage="form?.errors?.city ?? '' "
                            v-model="form.city"
                        />
                        <CustomTextInputField2
                            :labelValue="'Country *'"
                            :inputId="'country'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.country"
                            :errorMessage="form?.errors?.country ?? '' "
                            v-model="form.country"
                        />
                    </div>
                    <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <CustomTextInputField
                            :labelValue="'Exit Time Frame *'"
                            :inputId="'exit_time_frame'"
                            class="col-span-full lg:col-span-3"
                            :dataValue="props.data.exit_time_frame"
                            :errorMessage="form?.errors?.exit_time_frame ?? '' "
                            v-model="form.exit_time_frame"
                        />
                        <CustomTextInputField
                            :labelValue="'Sell Price *'"
                            :inputId="'sell_price'"
                            class="col-span-full lg:col-span-2"
                            :dataValue="props.data.sell_price"
                            :errorMessage="form?.errors?.sell_price ?? '' "
                            v-model="form.sell_price"
                        />
                        <CustomTextInputField
                            :labelValue="'Allocation Officer'"
                            :inputId="'allocation_officer'"
                            class="col-span-full lg:col-span-3"
                            :dataValue="props.data.allocation_officer"
                            :errorMessage="form?.errors?.allocation_officer ?? '' "
                            v-model="form.allocation_officer"
                        />
                        <CustomTextInputField
                            :labelValue="'Trade + *'"
                            :inputId="'trade_plus'"
                            class="col-span-full lg:col-span-2"
                            :dataValue="props.data.trade_plus"
                            :errorMessage="form?.errors?.trade_plus ?? '' "
                            v-model="form.trade_plus"
                        />
                        <CustomDateTimeInputField
                            :labelValue="'Settlement Date *'"
                            :inputId="'settlement_date'"
                            :dateTimeOpt="false"
                            :dataValue="props.data.settlement_date"
                            :errorMessage="form?.errors?.settlement_date ?? '' "
                            class="col-span-full lg:col-span-2"
                            v-model="form.settlement_date"
                        />
                    </div>
                    <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <CustomTextInputField2
                            :labelValue="'Factory'"
                            :inputId="'factory'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.factory"
                            :errorMessage="form?.errors?.factory ?? '' "
                            v-model="form.factory"
                        />
                        <CustomTextInputField2
                            :labelValue="'Paying Via'"
                            :inputId="'pay_via'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.pay_via"
                            :errorMessage="form?.errors?.pay_via ?? '' "
                            v-model="form.pay_via"
                        />
                        <CustomTextInputField2
                            :inputType="'textarea'"
                            :labelValue="'Allo Comments'"
                            :inputId="'allo_comment'"
                            class="col-span-full lg:col-span-6"
                            :dataValue="props.data.allo_comment"
                            :errorMessage="form?.errors?.allo_comment ?? '' "
                            v-model="form.allo_comment"
                        />
                    </div>
                    <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <CustomTextInputField2
                            :labelValue="'Docs Received'"
                            :inputId="'docs_received'"
                            class="col-span-full lg:col-span-4"
                            :dataValue="props.data.docs_received"
                            :errorMessage="form?.errors?.docs_received ?? '' "
                            v-model="form.docs_received"
                        />
                        <CustomDateTimeInputField2
                            :labelValue="'TC Sent'"
                            :inputId="'tc_sent'"
                            :dateTimeOpt="false"
                            class="col-span-full lg:col-span-4"
                            :dataValue="props.data.tc_sent"
                            :errorMessage="form?.errors?.tc_sent ?? '' "
                            v-model="form.tc_sent"
                        />
                        <CustomDateTimeInputField2
                            :labelValue="'TT Received'"
                            :inputId="'tt_received'"
                            :dateTimeOpt="false"
                            :dataValue="props.data.tt_received"
                            :errorMessage="form?.errors?.tt_received ?? '' "
                            class="col-span-full lg:col-span-4"
                            v-model="form.tt_received"
                        />
                    </div>
                    <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <CustomLabelGroup2
                            :inputId="'leadFrontEditedAt'"
                            :labelValue="'Edited at'"
                            :dataValue="props.data.edited_at ? formatToUserTimezone(props.data.edited_at, user.timezone, true) : '-'"
                            class="col-span-full lg:col-span-4"
                        />
                        <CustomLabelGroup2
                            :inputId="'leadFrontCreatedAt'"
                            :labelValue="'Created at'"
                            :dataValue="props.data.created_at ? formatToUserTimezone(props.data.created_at, user.timezone, true) : '-'"
                            class="col-span-full lg:col-span-4"
                        />
                        <CustomSelectInputField2
                            :inputArray="usersList"
                            :inputId="'created_by_id'"
                            :labelValue="'Created By'"
                            :customValue="true"
                            :dataValue="props.data.created_by_id"
                            :errorMessage="form?.errors?.created_by_id ?? ''"
                            class="col-span-full lg:col-span-4"
                            v-model="form.created_by_id"
                        />
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
