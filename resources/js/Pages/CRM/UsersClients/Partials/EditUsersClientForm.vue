<script setup>
import { cl, back } from '@/Composables'
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { PlusIcon } from '@/Components/Icons/solid'
import { TrashIcon } from '@/Components/Icons/outline'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Checkbox from '@/Components/Checkbox.vue'
import CustomTextInputField from '@/Components/CustomTextInputField.vue'
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue'
import CustomDateTimeInputField from '@/Components/CustomDateTimeInputField.vue'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import Modal from '@/Components/Modal.vue'
import dayjs from 'dayjs';
import CollapsibleSection from '@/Components/CollapsibleSection.vue'
import Checkbox2 from '@/Components/Checkbox2.vue'
import PasswordInputField from '@/Components/PasswordInputField.vue'

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    errors:Object,
    data: {
        type: Object,
        default: () => ({}),
    },
})

const siteArray = ref(
	[ "namba1.com", "namba2.com", "namba3.com" ]
);
const countriesArray = ref(
	[ "Malaysia", "USA", "China", "Japan" ]
);
const accountTypeArray = ref(
	[ "Individual", "Joint", "Trust", "Corporate" ]
);
const accountManagerArray  = ref(
	[ "122", "123", "124" ]
);
const rankArray  = ref(
	[ "Normal", "VIP" ]
);
const clientArray  = ref(
	[ "ALLO", "NO ALLO", "REMM", "TT", "CLEARED", "PENDING", "KICKED", "CARRIED OVER", "FREE SWITCH", "CXL", "CXL-CLIENT DROPPED" ]
);

const kycArray  = ref(
	[ "Not started", "Pending documents", "In progress", "Rejected", "Approved" ]
); 

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	id: props.data.id,
	site: props.data.site,
	username: props.data.username,
	password: props.data.password,
	password_confirmation: props.data.password_confirmation,
	account_id: props.data.account_id,
	first_name: props.data.first_name,
	last_name: props.data.last_name,
	full_legal_name: props.data.full_legal_name,
	email: props.data.email,
	phone_number: props.data.phone_number,
	address: props.data.address,
	country_of_citizenship: props.data.country_of_citizenship,
	account_holder: props.data.account_holder,
	account_type: props.data.account_type,
	customer_type: props.data.customer_type,
	account_manager: props.data.account_manager,
	lead_status: props.data.lead_status,
	client_stage: props.data.client_stage,
	rank: props.data.rank,
	remark: props.data.remark,
	previous_broker_name: props.data.previous_broker_name,
	kyc_status: props.data.kyc_status,
	is_active: Boolean(props.data.is_active),
	has_crm_access: Boolean(props.data.has_crm_access),
	has_leads_access: Boolean(props.data.has_leads_access),
	is_staff: Boolean(props.data.is_staff),
	is_superuser: Boolean(props.data.is_superuser),
	last_login: props.data.last_login,
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
	form.phone_number = form.phone_number ? parseInt(form.phone_number) : '';
	
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
            <div class="grid grid-cols-1 lg:grid-cols-12 lg:gap-8">
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
                                        :inputId="'site'"
                                        :labelValue="'Site'"
                                        :errorMessage="form?.errors?.site ?? ''"
                                        :dataValue="props.data.site"
                                        v-model="form.site"
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
                                    <CustomTextInputField
                                        :labelValue="'Password'"
                                        :inputId="'password'"
                                        class=" col-start-1 col-span-full lg:col-span-4"
                                        :withTooltip="true"
                                        :dataValue="props.data.password"
                                        :errorMessage="form?.errors?.password ?? '' "
                                        v-model="form.password"
                                    >
                                        <p class="font-bold">The password must contain:</p>
                                        <ul class="pl-6 list-disc font-semibold">
                                            <li>at least 8 characters.</li>
                                            <li>at least one uppercase letter.</li>
                                            <li>at least one lowercase letter.</li>
                                            <li>at least one symbol.</li>
                                            <li>at least one number.</li>
                                        </ul>
                                    </CustomTextInputField>
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
                                :inputId="'full_legal_name'"
                                class="col-span-full lg:col-span-2"
                                :withTooltip="true"
								:dataValue="props.data.full_legal_name"
                                :errorMessage="form?.errors?.full_legal_name ?? '' "
                                v-model="form.full_legal_name"
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
                                :inputId="'country_of_citizenship'"
                                :labelValue="'Country of Citizenship'"
                                class="col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.country_of_citizenship ?? ''"
                                :dataValue="props.data.country_of_citizenship"
                                v-model="form.country_of_citizenship"
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
                            <CustomLabelGroup
                                :inputId="'orders'"
                                :labelValue="'Orders'"
                                class="col-span-full lg:col-span-2"
                                :dataValue="'-'"
                            />
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
                                :dataValue="'0.00'"
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
                                class="col-start-1 col-span-full lg:col-span-2"
                                :errorMessage="form?.errors?.account_type ?? ''"
                                :dataValue="props.data.account_type"
                                v-model="form.account_type"
                            />
                            <CustomSelectInputField
                                :inputArray="accountManagerArray"
                                :inputId="'account_manager'"
                                :labelValue="'Account manager'"
                                class="col-span-full lg:col-span-2"
                                :withTooltip="true"
                                :errorMessage="form?.errors?.account_manager ?? ''"
                                :dataValue="props.data.account_manager"
                                v-model="form.account_manager"
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
            <div class="grid grid-cols-1 lg:grid-cols-12 lg:gap-8">
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
                                        :dataValue="dayjs(props.data.last_login).format('YYYY-MM-DD HH:mm:ss')"
                                    />
                                    <CustomLabelGroup
                                        :inputId="'date_joined'"
                                        :labelValue="'Date joined'"
                                        :dataValue="dayjs(props.data.created_at).format('YYYY-MM-DD HH:mm:ss')"
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
