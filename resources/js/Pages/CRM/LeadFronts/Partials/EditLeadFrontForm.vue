<script setup>
import { cl, back, setDateTimeWithOffset, setFormattedDateTimeWithOffset, formatToUserTimezone } from '@/Composables'
import { ref, onMounted, computed } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
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

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    errors:Object,
    data: {
        type: Object,
        default: () => ({}),
    },
})


const page = usePage();
const user = computed(() => page.props.auth.user)

const selectedAssigneeError = ref()
const assigneeArray  = ref(
	[ "125", "124", "123", "122", "121", "120" ]
);
// const leadFrontEditedAt = ref('-');
// const leadFrontCreatedAt = ref('-');

onMounted(() => {
});

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	lead_front_name: props.data.name,
	lead_front_mimo: props.data.mimo,
	lead_front_product: props.data.product,
	lead_front_quantity: props.data.quantity,
	lead_front_price: props.data.price,
	lead_front_vc: props.data.vc,
	lead_front_sdm: props.data.sdm,
	lead_front_liquid: props.data.liquid,
	lead_front_bank: props.data.bank,
	lead_front_note: props.data.note,
	lead_front_commission: props.data.commission,
	lead_front_edited_at: props.data.edited_at,
	lead_front_created_at: props.data.created_at,
	lead_id: props.data.lead_id,
	lead_front_email: props.data.email,
	lead_front_phone_number: props.data.phone_number,
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
	form.lead_front_quantity = isValidNumber(form.lead_front_quantity) ? parseFloat(form.lead_front_quantity) : '';
	form.lead_front_price = isValidNumber(form.lead_front_price) ? parseFloat(form.lead_front_price) : '';
	form.lead_front_commission = isValidNumber(form.lead_front_commission) ? parseFloat(form.lead_front_commission) : '';
	form.lead_front_edited_at = setDateTimeWithOffset(true);
	form.lead_front_created_at = setFormattedDateTimeWithOffset(form.lead_front_created_at, true);

	form.put(route('lead-fronts.update', props.data.id), {
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
                            <CustomLabelGroup
                                :inputId="'lead_id'"
                                :labelValue="'Linked Lead'"
                                :dataValue="false"
                            >
								<Link
									:href="route('leads.edit', props.data.lead.id)"
									class="font-medium"
								>
									<Label
										:id="'emalie'"
										class="py-2 px-3 border border-gray-400 rounded-md h-[41px] cursor-pointer
												dark:border-gray-600 dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1"
									>
										<strong><span class="text-cyan-500 hover:text-cyan-600">{{ props.data.lead.first_name + ' ' + props.data.lead.last_name }}</span></strong>
									</Label>
								</Link>
							</CustomLabelGroup>
							<CustomTextInputField
								:inputType="'text'"
								:inputId="'leadFrontName'"
								:labelValue="'Name'"
								:dataValue="props.data.name"
								:errorMessage="(form.errors) ? form.errors.lead_front_name : '' "
								v-model="form.lead_front_name"
							/>
							<CustomTextInputField
								:inputType="'text'"
								:inputId="'leadFrontProduct'"
								:labelValue="'Product'"
								:dataValue="props.data.product"
								:errorMessage="(form.errors) ? form.errors.lead_front_product : '' "
								v-model="form.lead_front_product"
							/>
							<CustomTextInputField
								:inputType="'text'"
								:inputId="'leadFrontVc'"
								:labelValue="'VC'"
								:dataValue="props.data.vc"
								:errorMessage="(form.errors) ? form.errors.lead_front_vc : '' "
								v-model="form.lead_front_vc"
							/>
							<CustomTextInputField
								:inputType="'textarea'"
								:inputId="'leadFrontMimo'"
								:labelValue="'MIMO'"
								:dataValue="props.data.mimo"
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
										:dataValue="parseFloat(props.data.quantity).toFixed(2)"
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
										:dataValue="parseFloat(props.data.price).toFixed(2)"
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
                                    <CustomLabelGroup
                                        :inputId="'total'"
                                        :labelValue="'Total'"
                                        :dataValue="parseFloat(props.data.quantity * props.data.price).toFixed(2)"
                                    />
									<CustomTextInputField
										:inputType="'textarea'"
										:inputId="'leadFrontBank'"
										:labelValue="'Bank'"
										:dataValue="props.data.bank"
										class="col-span-2"
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
									class="col-span-full lg:col-span-2"
									:dataValue="props.data.note"
									:errorMessage="(form.errors) ? form.errors.lead_front_note : '' "
									v-model="form.lead_front_note"
								/>
                                <CustomLabelGroup
                                    :inputId="'assignee'"
                                    :labelValue="'Assignee'"
									:dataValue="false"
								>
									<Link
										:href="route('users-clients.edit', props.data.lead.assignee.id)"
										class="font-medium"
									>
										<Label
											:id="'emalie'"
											class="py-2 px-3 border border-gray-400 rounded-md h-[41px] cursor-pointer
													dark:border-gray-600 dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1"
										>
											<strong><span class="text-cyan-500 hover:text-cyan-600">{{ props.data.lead.assignee.username + ' (' + props.data.lead.assignee.site.name + ')' }}</span></strong>
										</Label>
									</Link>
								</CustomLabelGroup>
                                <CustomLabelGroup
                                    :inputId="'leadFrontCommission'"
                                    :labelValue="'Agent Commission %'"
                                    :dataValue="'(' + parseFloat(props.data.commission).toFixed(2) + '%) ' + (props.data.commission / 100 * (props.data.quantity * props.data.price)).toFixed(2)"
                                />
							</div>
							<div class="input-group grid grid-cols-1 lg:grid-cols-2 gap-6">
								<CustomLabelGroup
									:inputId="'leadFrontEditedAt'"
									:labelValue="'Edited at'"
                                    :dataValue="formatToUserTimezone(props.data.edited_at, user.timezone, true)"
                                    v-model="form.lead_front_edited_at"
								/>
								<CustomLabelGroup
									:inputId="'leadFrontCreatedAt'"
									:labelValue="'Created at'"
                                    :dataValue="formatToUserTimezone(props.data.created_at, user.timezone, true)"
                                    v-model="form.lead_front_created_at"
								/>
							</div>
						</div>
					</div>
				</div>
            </div>
        </form>
    </div>
</template>
