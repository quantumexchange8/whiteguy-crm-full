<script setup>
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { ref, onMounted, reactive, computed } from 'vue';
import { 
    cl, back, populateArrayFromResponse, convertToIndexedValueObject, 
    setDateTimeWithOffset, formatToUserTimezone 
} from '@/Composables'
import { useForm, usePage } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Checkbox2 from '@/Components/Checkbox2.vue';
import { PlusIcon } from '@/Components/Icons/solid';
import { TrashIcon } from '@/Components/Icons/outline';
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue';
import CollapsibleSection from '@/Components/CollapsibleSection.vue';
import PasswordInputField from '@/Components/PasswordInputField.vue';
import CustomTextInputField from '@/Components/CustomTextInputField.vue';
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue';
import CustomDateTimeInputField from '@/Components/CustomDateTimeInputField.vue';

dayjs.extend(utc);
dayjs.extend(timezone);

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    errors:Object,
    data: {
        type: Object,
        default: () => ({}),
    },
})
const page = usePage();
const filterIsOpen = ref(false);
const userId = ref();
const isUserOrdersModalOpen = ref(false);
const siteArray = reactive({});
const countriesArray = reactive({});
const accountTypeArray = ref(convertToIndexedValueObject(
	[ "Individual", "Joint", "Trust", "Corporate" ]
));
const accountManagerArray  = reactive({});
const rankArray  = ref(convertToIndexedValueObject(
	[ "Normal", "VIP" ]
));
const clientArray  = ref(convertToIndexedValueObject(
	[ "ALLO", "NO ALLO", "REMM", "TT", "CLEARED", "PENDING", "KICKED", "CARRIED OVER", "FREE SWITCH", "CXL", "CXL-CLIENT DROPPED" ]
));

const kycArray  = ref(convertToIndexedValueObject(
	[ "Not started", "Pending documents", "In progress", "Rejected", "Approved" ]
));

const user = computed(() => page.props.auth.user)

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	id: props.data.id,
	password: props.data.password,
	is_staff: Boolean(props.data.is_staff),
	is_active: Boolean(props.data.is_active),
	password_confirmation: props.data.password_confirmation,
	last_login: `${props.data.last_login}00`,
	is_superuser: Boolean(props.data.is_superuser),
	first_name: props.data.first_name,
	last_name: props.data.last_name,
	date_joined: `${props.data.date_joined}00`,
	username: props.data.username,
	full_name: props.data.full_name,
	email: props.data.email,
	phone_number: props.data.phone_number,
	profile_picture: props.data.profile_picture,
	is_email_verified: Boolean(props.data.is_email_verified),
	timezone: props.data.timezone,
	country: props.data.country,
	address: props.data.address,
	account_type: props.data.account_type,
	account_holder: props.data.account_holder,
	customer_type: props.data.customer_type,
	rank: props.data.rank,
	remark: props.data.remark,
	wallet_balance: props.data.wallet_balance,
    edited_at: props.data.edited_at,
    created_at: `${props.data.created_at}00`,
	account_manager_id: props.data.account_manager_id,
	site_id: props.data.site_id,
	has_crm_access: Boolean(props.data.has_crm_access),
	lead_status: props.data.lead_status,
	client_stage: props.data.client_stage,
	has_leads_access: Boolean(props.data.has_leads_access),
	identification_document_1: props.data.identification_document_1,
	identification_document_2: props.data.identification_document_2,
	identification_document_3: props.data.identification_document_3,
	kyc_status: props.data.kyc_status,
	proof_of_address_document_1: props.data.proof_of_address_document_1,
	proof_of_address_document_2: props.data.proof_of_address_document_2,
	account_id: props.data.account_id,
	previous_broker_name: props.data.previous_broker_name,
});

onMounted(async () => {
    try {
        const siteResponse = await axios.get(route('users-clients.getAllSites'));
        populateArrayFromResponse(siteResponse.data, siteArray, 'id', 'domain');

        const countriesResponse = await axios.get('https://countriesnow.space/api/v0.1/countries/iso');
        populateArrayFromResponse(countriesResponse.data.data, countriesArray, 'Iso2', 'name');

        const accountManagerResponse = await axios.get(route('users-clients.getAccountManagers'));

        accountManagerResponse.data.forEach(item => {
            accountManagerArray[item['id']] = {
                value: item['username'] + " (" + item.site['name'] + ")",
            };
        });
    } catch (error) {
        console.error('Error fetching data:', error);
    }
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
	form.account_type = parseInt(form.account_type) ?? '';
	form.rank = parseInt(form.rank) ?? '';
	form.wallet_balance = parseFloat(props.data.wallet_balance) ?? '',
	form.account_manager_id = parseInt(form.account_manager_id) ?? '';
	form.site_id = parseInt(form.site_id) ?? '';
	form.client_stage = parseInt(form.client_stage) ?? '';
	form.kyc_status = parseInt(form.kyc_status) ?? '';
	form.edited_at = setDateTimeWithOffset(true);

	form.put(route('users-clients.update', props.data.id), {
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
const openFilterModal = () => {
    filterIsOpen.value = true;
}

const closeFilterModal = () => {
    filterIsOpen.value = false;
}

const openUserOrdersModal = () => {
    userId.value = props.data.id;
    isUserOrdersModalOpen.value = true;
};

const closeUserOrdersModal = () => {
    isUserOrdersModalOpen.value = false;
    userId.value = null;
};
</script>

<template>
    <div class="form-wrapper">
    </div>
</template>
