<script setup>
import { cl, back } from '@/Composables'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { PlusIcon } from '@/Components/Icons/solid'
import { DeleteIcon } from '@/Components/Icons/outline'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Checkbox from '@/Components/Checkbox.vue'
import CustomTextInputField from '@/Components/CustomTextInputField.vue'
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue'
import CustomDateTimeInputField from '@/Components/CustomDateTimeInputField.vue'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
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
	vc: '',
	data_type: '',
	data_source: '',
	data_code: '',
	email: '',
	email_alt_1: '',
	email_alt_2: '',
	email_alt_3: '',
	phone_number: '',
	phone_number_alt_1: '',
	phone_number_alt_2: '',
	phone_number_alt_3: '',
	private_remark: '',
	remark: '',
	appointment_start_at: '',
	appointment_end_at: '',
	last_called: '',
	assignee_read_at: '',
	give_up_at: '',
	appointment_label: '',
	contact_outcome: '',
	stage: '',
	assignee: '',
	created_by: '',
	delete_at: '',
    create_lead_front: false,
	lead_front_name: '',
	lead_front_mimo: '',
	lead_front_product: '',
	lead_front_quantity: '',
	lead_front_price: '',
	lead_front_sdm: false,
	lead_front_liquid: false,
	lead_front_bank_name: '',
	lead_front_bank_account: '',
	lead_front_note: '',
	lead_front_commission: '',
	lead_front_vc: '',
	lead_front_edited_at: dayjs(new Date()).format('YYYY-MM-DD HH:mm:ss'),
    lead_notes: [],
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
	form.phone_number = form.phone_number ? parseInt(form.phone_number) : '';
	form.phone_number_alt_1 = form.phone_number_alt_1 ? parseInt(form.phone_number_alt_1) : '';
	form.phone_number_alt_2 = form.phone_number_alt_2 ? parseInt(form.phone_number_alt_2) : '';
	form.phone_number_alt_3 = form.phone_number_alt_3 ? parseInt(form.phone_number_alt_3) : '';
	form.lead_front_bank_account = form.lead_front_bank_account ? parseInt(form.lead_front_bank_account) : '';
	form.lead_front_quantity = isValidNumber(form.lead_front_quantity) ? parseFloat(form.lead_front_quantity) : '';
	form.lead_front_price = isValidNumber(form.lead_front_price) ? parseFloat(form.lead_front_price) : '';
	form.lead_front_commission = isValidNumber(form.lead_front_commission) ? parseFloat(form.lead_front_commission) : '';

	form.post(route('leads.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    
    })
};


// Check and allows only the following keypressed
const isNumber = (e) => {
    const keysAllowed = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.'];
    const keyPressed = e.key;
    
    if (!keysAllowed.includes(keyPressed)) {
		e.preventDefault();
    }
};

// Check if the value is not empty and is a valid number
const isValidNumber = (value) => {
    return value !== '' && !isNaN(parseFloat(value)) && isFinite(value);
};

// Unhide the Lead Front Form and Clear button, and hide itself
const expandLeadFrontForm = () => {
	showLeadFrontForm.value = true;
	clearButtonShow.value = true;
	showButtonShow.value = false;
    form.create_lead_front = true;
}

// Clears/Resets Lead Front Create Form input fields, unhide the show button and hide itself
const clearLeadFrontForm = () => {
	showLeadFrontForm.value = false;
	showButtonShow.value = true;
	clearButtonShow.value = false;

    form.create_lead_front = false;
	form.lead_front_name = '';
	form.lead_front_mimo = '';
	form.lead_front_product = '';
	form.lead_front_quantity = '';
	form.lead_front_price = '';
	form.lead_front_sdm = false;
	form.lead_front_liquid = false;
	form.lead_front_bank_name = '';
	form.lead_front_bank_account = '';
	form.lead_front_note = '';
	form.lead_front_commission = '';
	form.lead_front_vc = '';
}

const remove = (i) => {
    form.lead_notes.splice(i, 1)
}
const addLeadNote = () => {
    form.lead_notes.push({
        'note': '',
        'user_editable': false,
        'created_by': '',
    });
}

</script>

<template>
    <div class="form-wrapper">
        <form novalidate @submit.prevent="formSubmit">
            <div class="form-input-section dark:bg-dark-eval-1">
                <div class="px-3">
                    <div class="flex flex-row justify-between items-center mb-2">
                        <Label
                            :value="'Lead Front Information'"
                            class="mt-4 !text-2xl !font-bold pl-2"
                        >
                        </Label>
                    </div>
                    <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                </div>
				<div class="input-group-wrapper grid grid-cols-1 lg:grid-cols-2 gap-8">
					<div class="flex flex-col col-span-1 gap-8">
						<div class="input-group col-span-2 grid grid-cols-1 lg:grid-cols-2">
							<Checkbox
								:inputId="'createLeadFront'"
								v-model:checked="form.create_lead_front"
								v-show="false"
							/>
							<CustomTextInputField
								:inputType="'text'"
								:inputId="'leadFrontName'"
								:labelValue="'Name'"
								:dataValue="form.lead_front_name"
								:errorMessage="(form.errors) ? form.errors.lead_front_name : '' "
								v-model="form.lead_front_name"
							/>
							<CustomTextInputField
								:inputType="'text'"
								:inputId="'leadFrontVc'"
								:labelValue="'VC'"
								:dataValue="form.lead_front_vc"
								:errorMessage="(form.errors) ? form.errors.lead_front_vc : '' "
								v-model="form.lead_front_vc"
							/>
						</div>
						<div class="col-span-2 flex flex-col gap-10">
							<div class="grid grid-cols-1 gap-10">
								<div class="input-group grid grid-cols-1 lg:grid-cols-4 gap-6">
									<CustomTextInputField
										:inputType="'textarea'"
										:inputId="'leadFrontMimo'"
										:labelValue="'MIMO'"
										:dataValue="form.lead_front_mimo"
										class="col-span-4 w-full"
										:errorMessage="(form.errors) ? form.errors.lead_front_mimo : '' "
										v-model="form.lead_front_mimo"
									/>
									<CustomTextInputField
										:inputType="'text'"
										:inputId="'leadFrontProduct'"
										:labelValue="'Product'"
										:dataValue="form.lead_front_product"
										class="col-span-2 w-full"
										:errorMessage="(form.errors) ? form.errors.lead_front_product : '' "
										v-model="form.lead_front_product"
									/>
									<CustomTextInputField
										:inputType="'number'"
										:inputId="'leadFrontQuantity'"
										:labelValue="'Amount of Shares'"
										:dataValue="String(form.lead_front_quantity)"
										:decimalOption="true"
										:step="0.01"
										@keypress="isNumber($event)"
										class="col-span-2"
										:errorMessage="(form.errors) ? form.errors.lead_front_quantity : '' "
										v-model="form.lead_front_quantity"
									/>
									<CustomTextInputField
										:inputType="'number'"
										:inputId="'leadFrontPrice'"
										:labelValue="'Price per Share'"
										:dataValue="String(form.lead_front_price)"
										:decimalOption="true"
										:step="0.01"
										class="col-span-2"
										:errorMessage="(form.errors) ? form.errors.lead_front_price : '' "
										@keypress="isNumber($event)"
										v-model="form.lead_front_price"
									/>
									<Checkbox
										v-model:checked="form.lead_front_sdm"
										:inputId="'leadFrontSdm'"
										:labelValue="'Sdm'"
										:errorMessage="(form.errors) ? form.errors.lead_front_sdm : '' "
									/>
									<Checkbox
										v-model:checked="form.lead_front_liquid"
										:inputId="'leadFrontLiquid'"
										:labelValue="'Liquid'"
										:errorMessage="(form.errors) ? form.errors.lead_front_liquid : '' "
									/>
									<CustomTextInputField
										:inputId="'leadFrontBankName'"
										:labelValue="'Bank Name'"
										:dataValue="form.lead_front_bank_name"
										class="col-span-2"
										:errorMessage="(form.errors) ? form.errors.lead_front_bank_name : '' "
										v-model="form.lead_front_bank_name"
									/>
									<CustomTextInputField
										:inputType="'number'"
										:inputId="'leadFrontBankAccount'"
										:labelValue="'Bank Account'"
										class="col-span-2"
										:errorMessage="(form.errors) ? form.errors.lead_front_bank_account : '' "
										v-model="form.lead_front_bank_account"
										@keypress="isNumber($event)"
									/>
								</div>
							</div>
						</div>
					</div>
					<div class="grid grid-cols-1 lg:grid-cols-2 col-span-1 gap-8">
						<div class="col-span-2 flex flex-col gap-5">
							<div class="input-group">
								<CustomTextInputField
									:inputType="'textarea'"
									:inputId="'leadFrontNote'"
									:labelValue="'Special Note'"
									:dataValue="form.lead_front_note"
									:errorMessage="(form.errors) ? form.errors.lead_front_note : '' "
									v-model="form.lead_front_note"
								/>
							</div>
							<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
								<div class="input-group">
									<CustomTextInputField
										:inputType="'number'"
										:inputId="'leadFrontCommission'"
										:labelValue="'Agent Commission %'"
										:dataValue="String(form.lead_front_commission)"
										:decimalOption="true"
										:step="0.01"
										:errorMessage="(form.errors) ? form.errors.lead_front_commission : '' "
										@keypress="isNumber($event)"
										v-model="form.lead_front_commission"
									/>
								</div>
								<div class="input-group">
									<CustomLabelGroup
										:inputId="'leadFrontEditedAt'"
										:labelValue="'Edited at'"
									/>
									<CustomLabelGroup
										:inputId="'leadFrontCreatedAt'"
										:labelValue="'Created at'"
									/>
								</div>
							</div>
						</div>
					</div>
				</div>
            </div>
            <div class="form-action-section">
                <div class="action-button-group">
                    <Button 
                        :type="'button'"
                        :variant="'info'" 
                        :size="'base'" 
                        class="justify-center gap-2"
                        @click="back"
                    >
                        <span>Back</span>
                    </Button>
                    <Button 
                        :variant="'primary'" 
                        :size="'base'" 
                        class="justify-center gap-2"
                        :disabled="form.processing"
                    >
                        <span>{{ form.processing ? 'Saving...' : 'Save' }}</span>
                    </Button>
                </div>
            </div>
        </form>
    </div>
</template>
