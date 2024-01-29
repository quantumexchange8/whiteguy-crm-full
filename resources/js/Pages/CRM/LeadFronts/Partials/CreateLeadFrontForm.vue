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

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
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
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
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
