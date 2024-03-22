<script setup>
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { ref, onMounted, reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { 
    cl, back, populateArrayFromResponse, convertToIndexedValueObject, 
    setDateTimeWithOffset 
} from '@/Composables'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Checkbox2 from '@/Components/Checkbox2.vue'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import PasswordInputField from '@/Components/PasswordInputField.vue'
import CollapsibleSection from '@/Components/CollapsibleSection.vue'
import CustomTextInputField from '@/Components/CustomTextInputField.vue'
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue'

dayjs.extend(utc);
dayjs.extend(timezone);

// Get the errors thats passed back from controller if there are any error after backend validations
defineProps({
    errors:Object
})

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

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	password: '',
	password_confirmation: '',
	last_login: '',
	is_superuser: false,
	first_name: '',
	last_name: '',
	is_staff: false,
	is_active: true,
	date_joined: '',
	username: '',
	full_name: '',
	email: '',
	phone_number: '',
	profile_picture: '',
	is_email_verified: false,
	timezone: dayjs.tz.guess(),
	country: '',
	address: '',
	account_type: '',
	account_holder: '',
	customer_type: '',
	rank: '',
	remark: '',
	wallet_balance: parseFloat(0.00),
	account_manager_id: '',
	site_id: '',
	has_crm_access: false,
	lead_status: '',
	client_stage: '',
	has_leads_access: false,
	identification_document_1: '',
	identification_document_2: '',
	identification_document_3: '',
	kyc_status: '',
	proof_of_address_document_1: '',
	proof_of_address_document_2: '',
	account_id: '',
	previous_broker_name: '',
});

onMounted(async () => {
    try {
        const accountIdResponse = await axios.get(route('users-clients.generateAccountId'));
        form.account_id = accountIdResponse.data.account_id;
        
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
	form.account_manager_id = parseInt(form.account_manager_id) ?? '';
	form.site_id = parseInt(form.site_id) ?? '';
	form.client_stage = parseInt(form.client_stage) ?? '';
	form.kyc_status = parseInt(form.kyc_status) ?? '';
    form.date_joined = setDateTimeWithOffset(true);
    
    // if (form.is_staff) {
    //     form.last_login = setDateTimeWithOffset(true);
    // }

    form.post(route('users-clients.store'), {
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
                <div class="col-span-12 lg:col-span-4">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="flex justify-between items-center mb-2">
                            <Label 
                                :value="'Website'" 
                                class="mt-4 !text-2xl !font-bold pl-2" 
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                        <div class="input-group-wrapper">
                            <div class="input-group">
                                <CustomSelectInputField
                                    :inputArray="siteArray"
                                    :inputId="'site_id'"
                                    :labelValue="'Site'"
                                    :customValue="true"
                                    :errorMessage="form?.errors?.site_id ?? ''"
                                    v-model="form.site_id"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-8">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="flex justify-between items-center mb-2">
                            <Label 
                                :value="'Login'" 
                                class="mt-4 !text-2xl !font-bold pl-2" 
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                        <div class="input-group-wrapper">
                            <div class="input-group flex flex-col gap-6">
                                <CustomTextInputField
                                    :labelValue="'Username'"
                                    :inputId="'username'"
                                    class="w-full lg:max-w-lg"
                                    :errorMessage="form?.errors?.username ?? '' "
                                    v-model="form.username"
                                />
                                <PasswordInputField
                                    :labelValue="'Password'"
                                    :inputId="'password'"
                                    :withTooltip="true"
                                    class="w-full lg:max-w-lg"
                                    :errorMessage="form?.errors?.password ?? '' "
                                    v-model="form.password"
                                />
                                <PasswordInputField
                                    :labelValue="'Confirm Password'"
                                    :inputId="'password_confirmation'"
                                    class="w-full lg:max-w-lg"
                                    :errorMessage="form?.errors?.password_confirmation ?? '' "
                                    v-model="form.password_confirmation"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-input-section dark:bg-dark-eval-1">
                <div class="flex justify-between items-center mb-2">
                    <Label 
                        :value="'User Information'" 
                        class="mt-4 !text-2xl !font-bold pl-2" 
                    >
                    </Label>
                </div>
                <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                <div class="input-group-wrapper grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div class="col-span-6">
                        <div class="input-group grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <CustomTextInputField
                                :labelValue="'Account ID'"
                                :inputId="'account_id'"
                                class="col-span-full lg:col-span-3"
                                :withTooltip="true"
                                :dataValue="form.account_id"
                                :errorMessage="form?.errors?.account_id ?? '' "
                                v-model="form.account_id"
                            >
                                Account ID is auto generated.
                            </CustomTextInputField>
                            <CustomTextInputField
                                :labelValue="'First name'"
                                :inputId="'first_name'"
                                class="col-start-1 col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.first_name ?? '' "
                                v-model="form.first_name"
                            />
                            <CustomTextInputField
                                :labelValue="'Last name'"
                                :inputId="'last_name'"
                                class="col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.last_name ?? '' "
                                v-model="form.last_name"
                            />
                            <CustomTextInputField
                                :labelValue="'Full Legal Name'"
                                :inputId="'full_name'"
                                class="col-span-full lg:col-span-2"
                                :withTooltip="true"
                                :errorMessage="form?.errors?.full_name ?? '' "
                                v-model="form.full_name"
                            >
                                Please enter full legal name for the owner of this account.<br>
                                For business, enter your full business name.
                            </CustomTextInputField>
                        </div>
                    </div>
                    <div class="col-span-6">
                        <div class="input-group grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <CustomTextInputField
                                :labelValue="'Email'"
                                :inputId="'email'"
                                class="col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.email ?? '' "
                                v-model="form.email"
                                :withTooltip="true"
                            >
                                Email is required for account recovery in case you lose your password.
                            </CustomTextInputField>
                            <CustomTextInputField
                                :labelValue="'Phone number'"
                                :inputId="'phone_number'"
                                class="col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.phone_number ?? '' "
                                v-model="form.phone_number"
                            />
                            <CustomSelectInputField
                                :inputArray="countriesArray"
                                :inputId="'country'"
                                :labelValue="'Country of Citizenship'"
                                :customValue="true"
                                :errorMessage="form?.errors?.country ?? ''"
                                class="col-span-full lg:col-span-2"
                                v-model="form.country"
                            />
                            <CustomTextInputField
                                :inputType="'textarea'"
                                :labelValue="'Address'"
                                :inputId="'address'"
                                class="col-span-full"
                                :errorMessage="form?.errors?.address ?? '' "
                                v-model="form.address"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-input-section dark:bg-dark-eval-1">
                <div class="flex justify-between items-center mb-2">
                    <Label 
                        :value="'Account Information'" 
                        class="mt-4 !text-2xl !font-bold pl-2" 
                    >
                    </Label>
                </div>
                <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                <div class="input-group-wrapper grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div class="col-span-6">
                        <div class="input-group grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <CustomTextInputField
                                :labelValue="'Account Holder Name'"
                                :inputId="'account_holder'"
                                class="col-span-full lg:col-span-3"
                                :withTooltip="true"
                                :errorMessage="form?.errors?.account_holder ?? '' "
                                v-model="form.account_holder"
                            >
                                The name of the primary holder of this account.
                            </CustomTextInputField>
                            <CustomSelectInputField
                                :inputArray="accountTypeArray"
                                :inputId="'account_type'"
                                :labelValue="'Account type'"
                                :customValue="true"
                                class="col-start-1 col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.account_type ?? ''"
                                v-model="form.account_type"
                            />
                            <CustomSelectInputField
                                :inputArray="accountManagerArray"
                                :inputId="'account_manager_id'"
                                :labelValue="'Account manager'"
                                :withTooltip="true"
                                :customValue="true"
                                :errorMessage="form?.errors?.account_manager_id ?? ''"
                                class="col-span-full lg:col-span-2"
                                v-model="form.account_manager_id"
                            >
                                Only CRM users can be assigned as account manager.
                            </CustomSelectInputField>
                            <CustomSelectInputField
                                :inputArray="rankArray"
                                :inputId="'rank'"
                                :labelValue="'Rank'"
                                class="col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.rank ?? ''"
                                :customValue="true"
                                v-model="form.rank"
                            />
                        </div>
                    </div>
                    <div class="col-span-6">
                        <div class="input-group grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <CustomTextInputField
                                :labelValue="'Customer type'"
                                :inputId="'customer_type'"
                                class="col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.customer_type ?? '' "
                                v-model="form.customer_type"
                            />
                            <CustomTextInputField
                                :labelValue="'Lead Status'"
                                :inputId="'lead_status'"
                                class="col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.lead_status ?? '' "
                                v-model="form.lead_status"
                            />
                            <CustomSelectInputField
                                :inputArray="clientArray"
                                :inputId="'client_stage'"
                                :labelValue="'Client stage'"
                                class="col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.client_stage ?? ''"
                                :customValue="true"
                                v-model="form.client_stage"
                            />
                            <CustomTextInputField
                                :labelValue="'Previous Broker Name'"
                                :inputId="'previous_broker_name'"
                                class="col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.previous_broker_name ?? '' "
                                v-model="form.previous_broker_name"
                            />
                            <CustomTextInputField
                                :inputType="'textarea'"
                                :labelValue="'Remark'"
                                :inputId="'remark'"
                                class="col-span-full"
                                :errorMessage="form?.errors?.remark ?? '' "
                                v-model="form.remark"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 lg:gap-6">
                <div class="col-span-12 lg:col-span-4">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="flex justify-between items-center mb-2">
                            <Label 
                                :value="'KYC Status'" 
                                class="mt-4 !text-2xl !font-bold pl-2" 
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                        <div class="input-group-wrapper">
                            <div class="input-group">
                                <CustomSelectInputField
                                    :inputArray="kycArray"
                                    :inputId="'kyc_status'"
                                    :labelValue="'Kyc status'"
                                    :errorMessage="form?.errors?.kyc_status ?? ''"
                                    :customValue="true"
                                    v-model="form.kyc_status"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-5">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <CollapsibleSection 
                            :title="'Permissions'"
                            :hideSection="true"
                        >
                            <div class="input-group-wrapper">
                                <div class="input-group flex flex-col gap-3">
									<Checkbox2
                                        :inputId="'is_active'"
                                        :labelValue="'Active'"
                                        :errorMessage="form?.errors?.is_active ?? '' "
                                        :checked="true"
                                        v-model:checked="form.is_active"
									>
                                        Designates whether this user should be treated as active.<br>Unselect this instead of deleting accounts.
                                    </Checkbox2>
									<Checkbox2
                                        :inputId="'has_crm_access'"
                                        :labelValue="'CRM Access'"
                                        :errorMessage="form?.errors?.has_crm_access ?? '' "
										v-model:checked="form.has_crm_access"
									>
                                        User can access CRM system.
                                    </Checkbox2>
									<Checkbox2
                                        :inputId="'has_leads_access'"
                                        :labelValue="'Lead Management Access'"
                                        :errorMessage="form?.errors?.has_leads_access ?? '' "
										v-model:checked="form.has_leads_access"
									>
                                        User can access Lead Management System.
                                    </Checkbox2>
									<Checkbox2
                                        :inputId="'is_staff'"
                                        :labelValue="'Staff status'"
                                        :errorMessage="form?.errors?.is_staff ?? '' "
										v-model:checked="form.is_staff"
                                    >
                                        Designates whether the user can log into this admin site.
                                    </Checkbox2>
									<Checkbox2
                                        :inputId="'is_superuser'"
                                        :labelValue="'Superuser status'"
                                        :errorMessage="form?.errors?.is_superuser ?? '' "
										v-model:checked="form.is_superuser"
                                    >
                                        Designates that this user has all permissions without explicitly assigning them.
                                    </Checkbox2>
                                </div>
                            </div>
                        </CollapsibleSection>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-3">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="flex justify-between items-center mb-2">
                            <Label 
                                :value="'Important Dates'" 
                                class="mt-4 !text-2xl !font-bold pl-2" 
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                        <div class="input-group-wrapper">
                            <div class="input-group">
                                <CustomLabelGroup
                                    :inputId="'last_login'"
                                    :labelValue="'Last login'"
                                    :dataValue="'-'"
                                />
                                <CustomLabelGroup
                                    :inputId="'date_joined'"
                                    :labelValue="'Date joined'"
                                    :dataValue="'-'"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
