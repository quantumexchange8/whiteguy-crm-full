<script setup>
import { cl, back } from '@/Composables'
import { ref, onMounted } from 'vue'
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

const leadsList = ref([]);
const selectedLeadError = ref()
const selectedAssigneeError = ref()
const assigneeArray  = ref(
	[ "125", "124", "123", "122", "121", "120" ]
);

onMounted(async () => {
  try {
    const leadsListResponse = await axios.get(route('lead-fronts.getLeadList'));
    leadsList.value = leadsListResponse.data;
    // cl(leadsList.value);

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	linked_lead: '',
	lead_front_name: '',
	lead_front_mimo: '',
	lead_front_product: '',
	lead_front_quantity: 0,
	lead_front_price: 0,
	lead_front_sdm: false,
	lead_front_liquid: false,
	lead_front_bank_name: '',
	lead_front_bank_account: '',
	lead_front_note: '',
	assignee: '',
	lead_front_commission: 0,
	lead_front_vc: '',
	lead_front_edited_at: '',
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
	form.lead_front_bank_account = form.lead_front_bank_account ? parseInt(form.lead_front_bank_account) : '';
	form.lead_front_quantity = isValidNumber(form.lead_front_quantity) ? parseFloat(form.lead_front_quantity) : '';
	form.lead_front_price = isValidNumber(form.lead_front_price) ? parseFloat(form.lead_front_price) : '';
	form.lead_front_commission = isValidNumber(form.lead_front_commission) ? parseFloat(form.lead_front_commission) : '';
	form.lead_front_edited_at = dayjs(new Date()).format('YYYY-MM-DD HH:mm:ss');

	// Check and allow submit only if user has selected a lead to link the lead front to
	if (form.linked_lead !== '' && form.assignee !== '') {
		selectedLeadError.value = '';
		selectedAssigneeError.value = '';

		form.post(route('lead-fronts.store'), {
		    preserveScroll: true,
		    onSuccess: () => form.reset(),
		
		})
	} else {
		selectedLeadError.value = 'A linked lead is required.';
		selectedAssigneeError.value = 'An assignee is required.';
	}
	// cl(selectedLeadError.value);
	// cl(selectedAssigneeError.value);

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
						<div class="input-group col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <CustomSelectInputField
                                :inputArray="leadsList"
                                :inputId="'linked_lead'"
                                :labelValue="'Lead'"
                                :errorMessage="selectedLeadError ?? ''"
                                v-model="form.linked_lead"
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
								:inputId="'leadFrontProduct'"
								:labelValue="'Product'"
								:dataValue="form.lead_front_product"
								:errorMessage="(form.errors) ? form.errors.lead_front_product : '' "
								v-model="form.lead_front_product"
							/>
							<CustomTextInputField
								:inputType="'text'"
								:inputId="'leadFrontVc'"
								:labelValue="'VC'"
								:dataValue="form.lead_front_vc"
								:errorMessage="(form.errors) ? form.errors.lead_front_vc : '' "
								v-model="form.lead_front_vc"
							/>
							<CustomTextInputField
								:inputType="'textarea'"
								:inputId="'leadFrontMimo'"
								:labelValue="'MIMO'"
								:dataValue="form.lead_front_mimo"
								class="col-span-2 w-full"
								:errorMessage="(form.errors) ? form.errors.lead_front_mimo : '' "
								v-model="form.lead_front_mimo"
							/>
						</div>
						<div class="col-span-2 flex flex-col gap-10">
							<div class="grid grid-cols-1 gap-10">
								<div class="input-group grid grid-cols-1 lg:grid-cols-4 gap-6">
									<CustomTextInputField
										:inputType="'number'"
										:inputId="'leadFrontQuantity'"
										:labelValue="'Amount of Shares'"
										:dataValue="parseFloat(form.lead_front_quantity).toFixed(2)"
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
										:dataValue="parseFloat(form.lead_front_price).toFixed(2)"
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
										class="col-span-2 col-start-1"
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
										@keypress="isNumber($event, false)"
									/>
								</div>
							</div>
						</div>
					</div>
					<div class="grid grid-cols-1 lg:grid-cols-2 col-span-1 gap-8">
						<div class="col-span-2 flex flex-col gap-5">
							<div class="input-group grid grid-cols-1 lg:grid-cols-2 gap-6">
								<CustomTextInputField
									:inputType="'textarea'"
									:inputId="'leadFrontNote'"
									:labelValue="'Special Note'"
									class="col-span-2"
									:dataValue="form.lead_front_note"
									:errorMessage="(form.errors) ? form.errors.lead_front_note : '' "
									v-model="form.lead_front_note"
								/>
								<CustomSelectInputField
									:inputArray="assigneeArray"
									:inputId="'assignee'"
									:labelValue="'Assignee'"
									:errorMessage="selectedAssigneeError ?? '' "
									v-model="form.assignee"
								/>
								<CustomTextInputField
									:inputType="'number'"
									:inputId="'leadFrontCommission'"
									:labelValue="'Agent Commission %'"
									:dataValue="parseFloat(form.lead_front_commission).toFixed(2)"
									:decimalOption="true"
									:step="0.01"
									:errorMessage="(form.errors) ? form.errors.lead_front_commission : '' "
									@keypress="isNumber($event)"
									v-model="form.lead_front_commission"
								/>
							</div>
							<div class="input-group grid grid-cols-1 lg:grid-cols-2 gap-6">
								<CustomLabelGroup
									:inputId="'leadFrontEditedAt'"
									:labelValue="'Edited at'"
									:dataValue="'-'"
								/>
								<CustomLabelGroup
									:inputId="'leadFrontCreatedAt'"
									:labelValue="'Created at'"
									:dataValue="'-'"
								/>
							</div>
							<!-- <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
							</div> -->
						</div>
					</div>
				</div>
            </div>
        </form>
    </div>
</template>
