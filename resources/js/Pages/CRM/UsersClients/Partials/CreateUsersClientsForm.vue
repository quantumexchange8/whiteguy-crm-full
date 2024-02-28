<script setup>
import { ref, onMounted } from 'vue'
import { cl, back } from '@/Composables'
import { useForm } from '@inertiajs/vue3'
import { PlusIcon } from '@/Components/Icons/solid'
import { DashboardIcon, TrashIcon } from '@/Components/Icons/outline'
import CustomDateTimeInputField from '@/Components/CustomDateTimeInputField.vue'
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue'
import CustomTextInputField from '@/Components/CustomTextInputField.vue'
import CollapsibleSection from '@/Components/CollapsibleSection.vue'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import CheckboxTile from '@/Components/CheckboxTile.vue'
import AltCheckbox from '@/Components/AltCheckbox.vue'
import Button from '@/Components/Button.vue'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import dayjs from 'dayjs';
import PasswordInputField from '@/Components/PasswordInputField.vue'

// Get the errors thats passed back from controller if there are any error after backend validations
defineProps({
    errors:Object
})

const siteArray = ref(
	[ "Call Back", "Close", "Front Follow Up", "Close Follow Up", "Service Call" ]
);
const accountTypeArray = ref(
	[ "Call Back", "Close", "Front Follow Up", "Close Follow Up", "Service Call" ]
);
const accountManagerArray  = ref(
	[ "Disconnected Line", "Voicemail", "No Answer", "W/N", "Line not connecting", "H/U", "HU/DC", "N/I", "Deceased", "Left company", "Gate Keeper", "HU/NI", "Voip Blocker", "Front", "Call Back", "DEAL!", "Misc", "Voicemail (Ring)", "Not speak English", "Dup Batch" ]
);
const rankArray  = ref(
	[ "125", "124", "123", "122", "121", "120" ]
);
const clientArray  = ref(
	[ "Contact Made", "HTR", "Failed Close", "Front (Front Form)", "New Account (Sale Order Form)" ]
);
const kycArray  = ref(
	[ "125", "124", "123", "122", "121", "120" ]
);

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	site: '',
	username: '',
	password: '',
	password_confirmation: '',
	account_id: '',
	first_name: '',
	last_name: '',
	full_legal_name: '',
	email: '',
	phone_number: '',
	address: '',
	country_of_citizenship: '',
	account_holder: '',
	account_type: '',
	customer_type: '',
	account_manager: '',
	lead_status: '',
	client_stage: '',
	rank: '',
	remark: '',
	previous_broker_name: '',
	kyc_status: '',
	is_active: false,
	has_crm_access: false,
	has_leads_access: false,
	is_staff: false,
	is_superuser: false,
	last_login: '',
});

onMounted(async () => {
  try {
    const accountIdResponse = await axios.get(route('users-clients.generateAccountId'));
    form.account_id = accountIdResponse.data.account_id;
    // cl(generatedAccountId.value);

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
	form.phone_number = form.phone_number ? parseInt(form.phone_number) : '';
	form.last_login = dayjs(new Date()).format('YYYY-MM-DD HH:mm:ss');

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
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-8">
                <div class="col-span-4">
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
                                    :inputId="'site'"
                                    :labelValue="'Site'"
                                    :errorMessage="form?.errors?.site ?? ''"
                                    v-model="form.site"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-8">
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
                            <div class="input-group grid grid-cols-1 sm:grid-cols-12 gap-6">
                                <CustomTextInputField
                                    :labelValue="'Username'"
                                    :inputId="'username'"
                                    class="col-span-7"
                                    :errorMessage="form?.errors?.username ?? '' "
                                    v-model="form.username"
                                />
                                <PasswordInputField
                                    :labelValue="'Password'"
                                    :inputId="'password'"
                                    class=" col-start-1 col-span-4"
                                    :errorMessage="form?.errors?.password ?? '' "
                                    v-model="form.password"
                                />
                                <CustomTextInputField
                                    :labelValue="'Confirm Password'"
                                    :inputId="'password_confirmation'"
                                    class="col-span-4"
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
                <div class="input-group-wrapper grid grid-cols-1 sm:grid-cols-12 gap-8">
                    <div class="col-span-6">
                        <div class="input-group grid grid-cols-1 sm:grid-cols-4 gap-6">
                            <CustomTextInputField
                                :labelValue="'Account ID'"
                                :inputId="'account_id'"
                                class="col-span-3"
                                :dataValue="form.account_id"
                                :errorMessage="form?.errors?.account_id ?? '' "
                                v-model="form.account_id"
                            />
                            <CustomTextInputField
                                :labelValue="'First name'"
                                :inputId="'first_name'"
                                class="col-start-1 col-span-2"
                                :errorMessage="form?.errors?.first_name ?? '' "
                                v-model="form.first_name"
                            />
                            <CustomTextInputField
                                :labelValue="'Last name'"
                                :inputId="'last_name'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.last_name ?? '' "
                                v-model="form.last_name"
                            />
                            <CustomTextInputField
                                :labelValue="'Full Legal Name'"
                                :inputId="'full_legal_name'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.full_legal_name ?? '' "
                                v-model="form.full_legal_name"
                            />
                        </div>
                    </div>
                    <div class="col-span-6">
                        <div class="input-group grid grid-cols-1 sm:grid-cols-4 gap-6">
                            <CustomTextInputField
                                :labelValue="'Email'"
                                :inputId="'email'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.email ?? '' "
                                v-model="form.email"
                            />
                            <CustomTextInputField
                                :labelValue="'Phone number'"
                                :inputId="'phone_number'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.phone_number ?? '' "
                                v-model="form.phone_number"
                                @keypress="isNumber($event, false)"
                            />
                            <CustomTextInputField
                                :labelValue="'Country of Citizenship'"
                                :inputId="'country_of_citizenship'"
                                class=" col-span-2"
                                :errorMessage="form?.errors?.country_of_citizenship ?? '' "
                                v-model="form.country_of_citizenship"
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
                <div class="input-group-wrapper grid grid-cols-1 sm:grid-cols-12 gap-8">
                    <div class="col-span-6">
                        <div class="input-group grid grid-cols-1 sm:grid-cols-4 gap-6">
                            <CustomTextInputField
                                :labelValue="'Account Holder Name'"
                                :inputId="'account_holder'"
                                class="col-span-3"
                                :errorMessage="form?.errors?.account_holder ?? '' "
                                v-model="form.account_holder"
                            />
                            <CustomSelectInputField
                                :inputArray="accountTypeArray"
                                :inputId="'account_type'"
                                :labelValue="'Account type'"
                                class="col-start-1 col-span-2"
                                :errorMessage="form?.errors?.account_type ?? ''"
                                v-model="form.account_type"
                            />
                            <CustomSelectInputField
                                :inputArray="accountManagerArray"
                                :inputId="'account_manager'"
                                :labelValue="'Account manager'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.account_manager ?? ''"
                                v-model="form.account_manager"
                            />
                            <CustomSelectInputField
                                :inputArray="rankArray"
                                :inputId="'rank'"
                                :labelValue="'Rank'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.rank ?? ''"
                                v-model="form.rank"
                            />
                        </div>
                    </div>
                    <div class="col-span-6">
                        <div class="input-group grid grid-cols-1 sm:grid-cols-4 gap-6">
                            <CustomTextInputField
                                :labelValue="'Customer type'"
                                :inputId="'customer_type'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.customer_type ?? '' "
                                v-model="form.customer_type"
                            />
                            <CustomTextInputField
                                :labelValue="'Lead Status'"
                                :inputId="'lead_status'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.lead_status ?? '' "
                                v-model="form.password_confirmation"
                            />
                            <CustomSelectInputField
                                :inputArray="clientArray"
                                :inputId="'client_stage'"
                                :labelValue="'Client stage'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.client_stage ?? ''"
                                v-model="form.client_stage"
                            />
                            <CustomTextInputField
                                :labelValue="'Previous Broker Name'"
                                :inputId="'previous_broker_name'"
                                class="col-span-2"
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
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-8">
                <div class="col-span-4">
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
                                    v-model="form.kyc_status"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-5">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <CollapsibleSection 
                            :title="'Permissions'"
                            :hideSection="true"
                        >
                            <div class="input-group-wrapper">
                                <div class="input-group flex flex-col gap-3">
									<AltCheckbox
                                        :inputId="'is_active'"
                                        :labelValue="'Active'"
                                        :errorMessage="form?.errors?.is_active ?? '' "
                                        :checked="true"
                                        v-model:checked="form.is_active"
									>
                                        Designates whether this user should be treated as active. Unselect this instead of deleting accounts.
                                    </AltCheckbox>
									<AltCheckbox
                                        :inputId="'has_crm_access'"
                                        :labelValue="'CRM Access'"
                                        :errorMessage="form?.errors?.has_crm_access ?? '' "
										v-model:checked="form.has_crm_access"
									>
                                        User can access CRM system.
                                    </AltCheckbox>
									<AltCheckbox
                                        :inputId="'has_leads_access'"
                                        :labelValue="'Lead Management Access'"
                                        :errorMessage="form?.errors?.has_leads_access ?? '' "
										v-model:checked="form.has_leads_access"
									>
                                        User can access Lead Management System.
                                    </AltCheckbox>
									<AltCheckbox
                                        :inputId="'is_staff'"
                                        :labelValue="'Staff status'"
                                        :errorMessage="form?.errors?.is_staff ?? '' "
										v-model:checked="form.is_staff"
                                    >
                                        Designates whether the user can log into this admin site.
                                    </AltCheckbox>
									<AltCheckbox
                                        :inputId="'is_superuser'"
                                        :labelValue="'Superuser status'"
                                        :errorMessage="form?.errors?.is_superuser ?? '' "
										v-model:checked="form.is_superuser"
                                    >
                                        Designates that this user has all permissions without explicitly assigning them.
                                    </AltCheckbox>
                                </div>
                            </div>
                        </CollapsibleSection>
                    </div>
                </div>
                <div class="col-span-3">
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
