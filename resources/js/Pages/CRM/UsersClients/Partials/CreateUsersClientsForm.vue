<script setup>
import { cl, back } from '@/Composables'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { PlusIcon } from '@/Components/Icons/solid'
import { TrashIcon } from '@/Components/Icons/outline'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import CheckboxTile from '@/Components/CheckboxTile.vue'
import CustomTextInputField from '@/Components/CustomTextInputField.vue'
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue'
import CustomDateTimeInputField from '@/Components/CustomDateTimeInputField.vue'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import CollapsibleSection from '@/Components/CollapsibleSection.vue'
import dayjs from 'dayjs';

// Get the errors thats passed back from controller if there are any error after backend validations
defineProps({
    errors:Object
})

const showPersonalInformationSection= ref(true);
const showLeadDetailsSection = ref(true);
const showLeadFrontForm = ref(false);
const showButtonShow = ref(true);
const clearButtonShow = ref(false);

const appointmentLabelArray = ref(
	[ "Call Back", "Close", "Front Follow Up", "Close Follow Up", "Service Call" ]
);
const contactOutcomeArray  = ref(
	[ "Disconnected Line", "Voicemail", "No Answer", "W/N", "Line not connecting", "H/U", "HU/DC", "N/I", "Deceased", "Left company", "Gate Keeper", "HU/NI", "Voip Blocker", "Front", "Call Back", "DEAL!", "Misc", "Voicemail (Ring)", "Not speak English", "Dup Batch" ]
);
const assigneeArray  = ref(
	[ "125", "124", "123", "122", "121", "120" ]
);
const stageArray  = ref(
	[ "Contact Made", "HTR", "Failed Close", "Front (Front Form)", "New Account (Sale Order Form)" ]
);
const createdByArray  = ref(
	[ "125", "124", "123", "122", "121", "120" ]
);

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	first_name: '',
	last_name: '',
	country: '',
	address: '',
	date_oppd_in: '',
	campaign_product: '',
	sdm: '',
	date_of_birth: '',
	occupation: '',
	agents_book: '',
	account_manager: '',
	is_active: false,
	has_crm_access: false,
	has_leads_access: false,
	is_staff: false,
	is_superuser: false,
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
	form.phone_number = form.phone_number ? parseInt(form.phone_number) : '';
	form.phone_number_alt_1 = form.phone_number_alt_1 ? parseInt(form.phone_number_alt_1) : '';
	form.phone_number_alt_2 = form.phone_number_alt_2 ? parseInt(form.phone_number_alt_2) : '';
	form.phone_number_alt_3 = form.phone_number_alt_3 ? parseInt(form.phone_number_alt_3) : '';
	form.lead_front_bank_account = form.lead_front_bank_account ? parseInt(form.lead_front_bank_account) : '';
	form.lead_front_quantity = isValidNumber(form.lead_front_quantity) ? parseFloat(form.lead_front_quantity) : 0;
	form.lead_front_price = isValidNumber(form.lead_front_price) ? parseFloat(form.lead_front_price) : 0;
	form.lead_front_commission = isValidNumber(form.lead_front_commission) ? parseFloat(form.lead_front_commission) : 0;
	form.lead_front_edited_at = dayjs(new Date()).format('YYYY-MM-DD HH:mm:ss');

	form.post(route('leads.store'), {
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
                                    :inputArray="appointmentLabelArray"
                                    :inputId="'appointmentLabel'"
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
                                <CustomTextInputField
                                    :labelValue="'Password'"
                                    :inputId="'password'"
                                    class=" col-start-1 col-span-4"
                                    :errorMessage="form?.errors?.password ?? '' "
                                    v-model="form.password"
                                />
                                <CustomTextInputField
                                    :labelValue="'Confirm Password'"
                                    :inputId="'firstName'"
                                    class="col-span-4"
                                    :errorMessage="form?.errors?.confirmPassword ?? '' "
                                    v-model="form.confirmPassword"
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
                                :inputId="'username'"
                                class="col-span-3"
                                :errorMessage="form?.errors?.username ?? '' "
                                v-model="form.username"
                            />
                            <CustomTextInputField
                                :labelValue="'First name'"
                                :inputId="'password'"
                                class="col-start-1 col-span-2"
                                :errorMessage="form?.errors?.password ?? '' "
                                v-model="form.password"
                            />
                            <CustomTextInputField
                                :labelValue="'Last name'"
                                :inputId="'firstName'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.confirmPassword ?? '' "
                                v-model="form.confirmPassword"
                            />
                            <CustomTextInputField
                                :labelValue="'Full Legal Name'"
                                :inputId="'username'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.username ?? '' "
                                v-model="form.username"
                            />
                        </div>
                    </div>
                    <div class="col-span-6">
                        <div class="input-group grid grid-cols-1 sm:grid-cols-4 gap-6">
                            <CustomTextInputField
                                :labelValue="'Email'"
                                :inputId="'password'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.password ?? '' "
                                v-model="form.password"
                            />
                            <CustomTextInputField
                                :labelValue="'Phone number'"
                                :inputId="'firstName'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.confirmPassword ?? '' "
                                v-model="form.confirmPassword"
                            />
                            <CustomTextInputField
                                :labelValue="'Country of Citizenship'"
                                :inputId="'password'"
                                class=" col-span-2"
                                :errorMessage="form?.errors?.password ?? '' "
                                v-model="form.password"
                            />
                            <CustomTextInputField
                                :inputType="'textarea'"
                                :labelValue="'Address'"
                                :inputId="'username'"
                                class="col-span-full"
                                :errorMessage="form?.errors?.username ?? '' "
                                v-model="form.username"
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
                                :inputId="'username'"
                                class="col-span-3"
                                :errorMessage="form?.errors?.username ?? '' "
                                v-model="form.username"
                            />
                            <CustomSelectInputField
                                :inputArray="appointmentLabelArray"
                                :inputId="'appointmentLabel'"
                                :labelValue="'Account type'"
                                class="col-start-1 col-span-2"
                                :errorMessage="form?.errors?.site ?? ''"
                                v-model="form.site"
                            />
                            <CustomSelectInputField
                                :inputArray="appointmentLabelArray"
                                :inputId="'appointmentLabel'"
                                :labelValue="'Account manager'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.site ?? ''"
                                v-model="form.site"
                            />
                            <CustomSelectInputField
                                :inputArray="appointmentLabelArray"
                                :inputId="'appointmentLabel'"
                                :labelValue="'Rank'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.site ?? ''"
                                v-model="form.site"
                            />
                        </div>
                    </div>
                    <div class="col-span-6">
                        <div class="input-group grid grid-cols-1 sm:grid-cols-4 gap-6">
                            <CustomTextInputField
                                :labelValue="'Customer type'"
                                :inputId="'password'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.password ?? '' "
                                v-model="form.password"
                            />
                            <CustomTextInputField
                                :labelValue="'Lead Status'"
                                :inputId="'firstName'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.confirmPassword ?? '' "
                                v-model="form.confirmPassword"
                            />
                            <CustomSelectInputField
                                :inputArray="appointmentLabelArray"
                                :inputId="'appointmentLabel'"
                                :labelValue="'Client stage'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.site ?? ''"
                                v-model="form.site"
                            />
                            <CustomTextInputField
                                :labelValue="'Previous Broker Name'"
                                :inputId="'username'"
                                class="col-span-2"
                                :errorMessage="form?.errors?.username ?? '' "
                                v-model="form.username"
                            />
                            <CustomTextInputField
                                :inputType="'textarea'"
                                :labelValue="'Remark'"
                                :inputId="'username'"
                                class="col-span-full"
                                :errorMessage="form?.errors?.username ?? '' "
                                v-model="form.username"
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
                                    :inputArray="appointmentLabelArray"
                                    :inputId="'appointmentLabel'"
                                    :labelValue="'Kyc status'"
                                    :errorMessage="form?.errors?.site ?? ''"
                                    v-model="form.site"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-5">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <CollapsibleSection 
                            :title="'Permissions'"
                        >
                            <div class="input-group-wrapper">
                                <div class="input-group flex flex-wrap gap-6">
									<CheckboxTile
										v-model:checked="form.is_active"
										:inputId="'is_active'"
										:labelValue="'Active'"
										:errorMessage="form?.errors?.is_active ?? '' "
									/>
									<CheckboxTile
										v-model:checked="form.has_crm_access"
										:inputId="'has_crm_access'"
										:labelValue="'CRM Access'"
										:errorMessage="form?.errors?.has_crm_access ?? '' "
									/>
									<CheckboxTile
										v-model:checked="form.has_leads_access"
										:inputId="'has_leads_access'"
										:labelValue="'Lead Management Access'"
										:errorMessage="form?.errors?.has_leads_access ?? '' "
									/>
									<CheckboxTile
										v-model:checked="form.is_staff"
										:inputId="'is_staff'"
										:labelValue="'Staff status'"
										:errorMessage="form?.errors?.is_staff ?? '' "
									/>
									<CheckboxTile
										v-model:checked="form.is_superuser"
										:inputId="'is_superuser'"
										:labelValue="'Superuser status'"
										:errorMessage="form?.errors?.is_superuser ?? '' "
                                        @click="cl(form.is_superuser)"
									/>
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
                                    :inputId="'leadFrontEditedAt'"
                                    :labelValue="'Last login'"
                                    :dataValue="'-'"
                                />
                                <CustomLabelGroup
                                    :inputId="'leadFrontEditedAt'"
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
