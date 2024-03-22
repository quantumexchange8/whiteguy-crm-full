<script setup>
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { ref, onMounted, reactive } from 'vue';
import { 
    cl, back, populateArrayFromResponse, convertToIndexedValueObject, 
    setDateTimeWithOffset, formatToUserTimezone 
} from '@/Composables'
import { useForm } from '@inertiajs/vue3';
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
import UserOrdersModal from './UserOrdersModal.vue';
import UpdateUserPasswordForm from './UpdateUserPasswordForm.vue';

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

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	id: props.data.id,
	password: props.data.password,
	is_staff: Boolean(props.data.is_staff),
	is_active: Boolean(props.data.is_active),
	password_confirmation: props.data.password_confirmation,
	last_login: props.data.last_login,
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
    edited_at: props.data.edited_at,
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
                        <CollapsibleSection 
                            :title="'Website'"
                            :hideSection="true"
                        >
                            <div class="input-group-wrapper">
                                <div class="input-group">
                                    <CustomSelectInputField
                                        :inputArray="siteArray"
                                        :inputId="'site_id'"
                                        :labelValue="'Site'"
                                        :customValue="true"
                                        :errorMessage="form?.errors?.site_id ?? ''"
                                        :dataValue="props.data.site_id"
                                        v-model="form.site_id"
                                    />
                                </div>
                            </div>
                        </CollapsibleSection>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-8">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <CollapsibleSection 
                            :title="'Login'"
                            :hideSection="true"
                        >
                            <div class="input-group-wrapper">
                                <div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
                                    <CustomTextInputField
                                        :labelValue="'Username'"
                                        :inputId="'username'"
                                        class="col-span-full lg:col-span-7"
                                        :dataValue="props.data.username"
                                        :errorMessage="form?.errors?.username ?? '' "
                                        v-model="form.username"
                                    />
                                    <div class="col-start-1 col-span-full lg:col-span-8 grid grid-cols-1 lg:grid-cols-12 gap-6">
                                        <PasswordInputField
                                            :labelValue="'Current Password'"
                                            :inputId="'curPassword'"
                                            class="col-span-full lg:col-span-8"
                                            :dataValue="props.data.password"
                                        />
                                        <div class="col-span-full lg:col-span-4 flex flex-col">
                                            <Label
                                                :value="'Update Password'"
                                                :for="'updatePasswordBtn'"
                                                class="mb-2"
                                            >
                                            </Label>
                                            <Button 
                                                :id="'updatePasswordBtn'"
                                                :type="'button'"
                                                :size="'sm'"
                                                class="self-center justify-center gap-2 w-full lg:w-4/5" 
                                                @click="openFilterModal"
                                            >
                                                Reset password
                                            </Button>
                                        </div>
                                    </div>
                                    <Modal 
                                        :show="filterIsOpen" 
                                        maxWidth="2xl" 
                                        :closeable="true" 
                                        @close="closeFilterModal">

                                        <div class="p-9">
                                            <UpdateUserPasswordForm
                                                :id="props.data.id"
                                                class="col-start-1 col-span-full lg:col-span-6"
                                            >
                                            </UpdateUserPasswordForm>
                                            <!-- <p class="text-gray-200 text-2xl bg-blue-500 p-4 rounded-md font-bold mb-6">Filters</p>
                                            <div class="flex flex-col justify-center divide-y divide-gray-500">
                                            </div>
                                            <div class="flex justify-end px-9">
                                                <div class="rounded-md shadow-lg flex flex-row gap-2">
                                                    <Button 
                                                        :type="'button'"
                                                        :variant="'secondary'"
                                                        :size="'sm'"
                                                        class="justify-center px-6 py-2 border border-purple-500" 
                                                        @click="reset"
                                                    >
                                                        Reset
                                                    </Button>
                                                    <Button 
                                                        :type="'button'"
                                                        :size="'sm'"
                                                        class="justify-center px-6 py-2" 
                                                        @click="getFilteredData(checkedFilters)"
                                                    >
                                                        Apply
                                                    </Button>
                                                </div>
                                            </div> -->
                                        </div>
                                    </Modal>
                                </div>
                            </div>
                        </CollapsibleSection>
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
                                :dataValue="props.data.account_id"
                                :errorMessage="form?.errors?.account_id ?? '' "
                                v-model="form.account_id"
                            >
                                Account ID is auto generated.
                            </CustomTextInputField>
                            <CustomTextInputField
                                :labelValue="'First name'"
                                :inputId="'first_name'"
                                class="col-start-1 col-span-full lg:col-span-2"
								:dataValue="props.data.first_name"
                                :errorMessage="form?.errors?.first_name ?? '' "
                                v-model="form.first_name"
                            />
                            <CustomTextInputField
                                :labelValue="'Last name'"
                                :inputId="'last_name'"
                                class="col-span-full lg:col-span-2"
								:dataValue="props.data.last_name"
                                :errorMessage="form?.errors?.last_name ?? '' "
                                v-model="form.last_name"
                            />
                            <CustomTextInputField
                                :labelValue="'Full Legal Name'"
                                :inputId="'full_name'"
                                class="col-span-full lg:col-span-2"
                                :withTooltip="true"
								:dataValue="props.data.full_name"
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
								:dataValue="props.data.email"
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
								:dataValue="props.data.phone_number"
                                :errorMessage="form?.errors?.phone_number ?? '' "
                                v-model="form.phone_number"
                                @keypress="isNumber($event, false)"
                            />
                            <CustomSelectInputField
                                :inputArray="countriesArray"
                                :inputId="'country'"
                                :labelValue="'Country of Citizenship'"
                                class="col-span-full lg:col-span-2"
                                :customValue="true"
                                :errorMessage="form?.errors?.country ?? ''"
                                :dataValue="props.data.country"
                                v-model="form.country"
                            />
                            <CustomTextInputField
                                :inputType="'textarea'"
                                :labelValue="'Address'"
                                :inputId="'address'"
                                class="col-span-full"
								:dataValue="props.data.address"
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
                            <div class="input-wrapper col-span-full lg:col-span-2">
                                <Label
                                    :value="'Orders'"
                                    :for="'orders'"
                                    class="mb-2"
                                >
                                </Label>
                                <div class="my-4 ml-6">
                                    <Button 
                                        :type="'button'"
                                        :size="'sm'"
                                        class="self-center justify-center gap-2 w-full lg:w-3/5 text-sm" 
                                        @click="openUserOrdersModal"
                                    >
                                        View Orders
                                    </Button>
                                    <UserOrdersModal 
                                        :isOpen="isUserOrdersModalOpen" 
                                        :userId="props.data.id" 
                                        @close="closeUserOrdersModal" 
                                    />
                                </div>
                            </div>
                            <CustomLabelGroup
                                :inputId="'application_form'"
                                :labelValue="'Application Form'"
                                class="col-span-full lg:col-span-2"
                                :dataValue="'-'"
                            />
                            <CustomLabelGroup
                                :inputId="'wallet_balance'"
                                :labelValue="'Wallet Balance'"
                                class="col-span-full lg:col-span-1"
                                :dataValue="form.wallet_balance"
                            />
                            <CustomTextInputField
                                :labelValue="'Account Holder Name'"
                                :inputId="'account_holder'"
                                class="col-span-full lg:col-span-3"
                                :withTooltip="true"
                                :errorMessage="form?.errors?.account_holder ?? '' "
                                :dataValue="props.data.account_holder"
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
                                :dataValue="props.data.account_type"
                                v-model="form.account_type"
                            />
                            <CustomSelectInputField
                                :inputArray="accountManagerArray"
                                :inputId="'account_manager_id'"
                                :labelValue="'Account manager'"
                                class="col-span-full lg:col-span-2"
                                :withTooltip="true"
                                :customValue="true"
                                :errorMessage="form?.errors?.account_manager_id ?? ''"
                                :dataValue="props.data.account_manager_id"
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
                                :dataValue="props.data.rank"
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
								:dataValue="props.data.customer_type"
                                :errorMessage="form?.errors?.customer_type ?? '' "
                                v-model="form.customer_type"
                            />
                            <CustomTextInputField
                                :labelValue="'Lead Status'"
                                :inputId="'lead_status'"
                                class="col-span-full lg:col-span-2"
								:dataValue="props.data.linked_lead"
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
                                :dataValue="props.data.client_stage"
                                v-model="form.client_stage"
                            />
                            <CustomTextInputField
                                :labelValue="'Previous Broker Name'"
                                :inputId="'previous_broker_name'"
                                class="col-span-full lg:col-span-2"
								:dataValue="props.data.previous_broker_name"
                                :errorMessage="form?.errors?.previous_broker_name ?? '' "
                                v-model="form.previous_broker_name"
                            />
                            <CustomTextInputField
                                :inputType="'textarea'"
                                :labelValue="'Remark'"
                                :inputId="'remark'"
                                class="col-span-full"
								:dataValue="props.data.remark"
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
                        <CollapsibleSection 
                            :title="'KYC Status'"
                            :hideSection="true"
                        >
                            <div class="input-group-wrapper">
                                <div class="input-group">
                                    <CustomSelectInputField
                                        :inputArray="kycArray"
                                        :inputId="'kyc_status'"
                                        :labelValue="'Kyc status'"
                                        :errorMessage="form?.errors?.kyc_status ?? ''"
                                        :customValue="true"
                                        :dataValue="props.data.kyc_status"
                                        v-model="form.kyc_status"
                                    />
                                </div>
                            </div>
                        </CollapsibleSection>
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
                        <CollapsibleSection 
                            :title="'Important Dates'"
                            :hideSection="true"
                        >
                            <div class="input-group-wrapper">
                                <div class="input-group">
                                    <CustomLabelGroup
                                        :inputId="'last_login'"
                                        :labelValue="'Last login'"
                                        :dataValue="props.data.last_login ? formatToUserTimezone(props.data.last_login) : '-'"
                                    />
                                    <CustomLabelGroup
                                        :inputId="'date_joined'"
                                        :labelValue="'Date joined'"
                                        :dataValue="formatToUserTimezone(props.data.created_at)"
                                    />
                                </div>
                            </div>
                        </CollapsibleSection>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
