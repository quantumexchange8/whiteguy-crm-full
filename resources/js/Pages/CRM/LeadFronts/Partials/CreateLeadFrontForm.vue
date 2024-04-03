<script setup>
import { cl, back, populateArrayFromResponse, setDateTimeWithOffset } from '@/Composables'
import { ref, onMounted, watch, reactive } from 'vue'
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

const leadsList = reactive({});
const leadEmail = ref('');
const leadPhoneNumber = ref('');

onMounted(async () => {
  try {
    const leadsListResponse = await axios.get(route('lead-fronts.getLeadList'));
    populateArrayFromResponse(leadsListResponse.data, leadsList, 'id', 'value');

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	lead_id: '',
	lead_front_name: '',
	lead_front_mimo: '',
	lead_front_product: '',
	lead_front_quantity: 0,
	lead_front_price: 0,
	lead_front_vc: '',
	lead_front_sdm: false,
	lead_front_liquid: false,
	lead_front_bank: '',
	lead_front_note: '',
	lead_front_commission: 0,
	lead_front_edited_at: '',
	lead_front_created_at: '',
	lead_front_email: '',
	lead_front_phone_number: '',
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
	form.lead_front_quantity = isValidNumber(form.lead_front_quantity) ? parseFloat(form.lead_front_quantity) : '';
	form.lead_front_price = isValidNumber(form.lead_front_price) ? parseFloat(form.lead_front_price) : '';
	form.lead_front_commission = isValidNumber(form.lead_front_commission) ? parseFloat(form.lead_front_commission) : '';
	form.lead_front_edited_at = setDateTimeWithOffset(true);
	form.lead_front_created_at = setDateTimeWithOffset(true);

	form.post(route('lead-fronts.store'), {
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

const getLeadDetails = async (lead_id) => {
	try {
		const leadResponse = await axios.get(route('lead-fronts.getLeadDetails', lead_id));
		leadEmail.value = leadResponse.data.email ?? '';
		leadPhoneNumber.value = leadResponse.data.phone_number ?? '';
		
		form.lead_front_email = leadResponse.data.email;
		form.lead_front_phone_number = leadResponse.data.phone_number;

	} catch (error) {
		console.error('Error fetching data:', error);
	}
}

watch(() => form.lead_id, (lead_id) => {
	getLeadDetails(lead_id);
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
                                :inputId="'lead_id'"
                                :labelValue="'Linked Lead'"
								:customValue="true"
                                :errorMessage="(form.errors) ? form.errors.lead_id : '' "
                                v-model="form.lead_id"
                            />
							<CustomTextInputField
								:inputType="'text'"
								:inputId="'leadFrontName'"
								:labelValue="'Name'"
								:dataValue="form.lead_front_name"
								:errorMessage="(form.errors) ? form.errors.lead_front_name : '' "
								v-model="form.lead_front_name"
							/>
							<CustomLabelGroup
								:inputId="'leadFrontEmail'"
								:labelValue="'Email'"
								:dataValue="false"
								class="text-gray-300"
							>
							<Label
								:id="'emalie'"
								class="py-2 px-3 border border-gray-400 rounded-md  h-[41px]
										dark:border-gray-600 dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1"
							>
								{{ leadEmail || '-' }}
							</Label>
							</CustomLabelGroup>
							<CustomLabelGroup
								:inputId="'leadFrontPhoneNumber'"
								:labelValue="'Phone Number'"
								:dataValue="false"
								class="text-gray-300"
							>
							<Label
								:id="'emalie'"
								class="py-2 px-3 border border-gray-400 rounded-md  h-[41px]
										dark:border-gray-600 dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1"
							>
								{{ leadPhoneNumber || '-' }}
							</Label>
							</CustomLabelGroup>
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
								<div class="input-group grid grid-cols-1 lg:grid-cols-12 gap-6">
									<CustomTextInputField
										:inputType="'number'"
										:inputId="'leadFrontQuantity'"
										:labelValue="'Amount of Shares'"
										:dataValue="parseFloat(form.lead_front_quantity).toFixed(2)"
										:decimalOption="true"
										:step="0.01"
										@keypress="isNumber($event)"
										class="col-span-6"
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
										class="col-span-6"
										:errorMessage="(form.errors) ? form.errors.lead_front_price : '' "
										@keypress="isNumber($event)"
										v-model="form.lead_front_price"
									/>
									<Checkbox
										v-model:checked="form.lead_front_sdm"
										:inputId="'leadFrontSdm'"
										:labelValue="'Sdm'"
										class="col-span-2"
										:errorMessage="(form.errors) ? form.errors.lead_front_sdm : '' "
									/>
									<Checkbox
										v-model:checked="form.lead_front_liquid"
										:inputId="'leadFrontLiquid'"
										:labelValue="'Liquid'"
										class="col-span-2"
										:errorMessage="(form.errors) ? form.errors.lead_front_liquid : '' "
									/>
									<CustomTextInputField
										:inputType="'textarea'"
										:inputId="'leadFrontBank'"
										:labelValue="'Bank'"
										class="col-span-8"
										:dataValue="form.lead_front_bank"
										:errorMessage="(form.errors) ? form.errors.lead_front_bank : '' "
										v-model="form.lead_front_bank"
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
						</div>
					</div>
				</div>
            </div>
        </form>
    </div>
</template>
