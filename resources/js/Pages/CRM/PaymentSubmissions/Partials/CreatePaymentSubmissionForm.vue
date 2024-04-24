<script setup>
import dayjs from 'dayjs';
import { ref, onMounted, watch, reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { cl, back, populateArrayFromResponse, setDateTimeWithOffset } from '@/Composables'
import Label from '@/Components/Label.vue'
import Modal from '@/Components/Modal.vue'
import Button from '@/Components/Button.vue'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import CustomTextInputField from '@/Components/CustomTextInputField.vue'
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue'
import CustomDateTimeInputField from '@/Components/CustomDateTimeInputField.vue'

// Get the errors thats passed back from controller if there are any error after backend validations
defineProps({
    errors:Object
})

const usersList = reactive({});
const paymentMethodsList = reactive({});
const showApprovalConfirmationModal = ref(false);

onMounted(async () => {
  try {
    const usersListResponse = await axios.get(route('payment-submissions.getAllUsers'));
    populateArrayFromResponse(usersListResponse.data, usersList, 'id', 'value');

    const paymentMethodsListResponse = await axios.get(route('payment-submissions.getAllPaymentMethods'));
    populateArrayFromResponse(paymentMethodsListResponse.data, paymentMethodsList, 'id', 'value');

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	date: '',
	amount: 0,
	converted_amount: 0,
	user_memo: '',
	admin_memo: '',
	admin_remark: '',
	status: 1,
	approved_at: '',
	edited_at: '',
	created_at: '',
	payment_method_id: '',
	user_id: '',
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
	form.amount = isValidNumber(form.amount) ? parseFloat(form.amount) : '';
	form.converted_amount = isValidNumber(form.converted_amount) ? parseFloat(form.converted_amount) : '';
	form.edited_at = setDateTimeWithOffset(true);
	form.created_at = setDateTimeWithOffset(true);

	form.post(route('payment-submissions.store'), {
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

const openApprovalConfirmationModal = () => {
    showApprovalConfirmationModal.value = true;
};

const closeApprovalConfirmationModal = () => {
    showApprovalConfirmationModal.value = false;
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
                            :value="'Client & Date'"
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
                                :inputArray="usersList"
                                :inputId="'user_id'"
                                :labelValue="'User'"
								:customValue="true"
                                :errorMessage="(form.errors) ? form.errors.user_id : '' "
                                v-model="form.user_id"
                            />
                            <CustomDateTimeInputField
                                :labelValue="'Date'"
                                :inputId="'date'"
                                :dateTimeOpt="false"
                                :errorMessage="(form.errors) ? form.errors.date : '' "
                                v-model="form.date"
                            />
						</div>
						<div class="col-span-2 flex flex-col gap-10">
						</div>
					</div>
					<div class="grid grid-cols-1 lg:grid-cols-2 col-span-1 gap-8">
					</div>
				</div>
            </div>
            <div class="form-input-section dark:bg-dark-eval-1">
                <div class="px-3">
                    <div class="flex flex-row justify-between items-center mb-2">
                        <Label
                            :value="'Memo & Remark'"
                            class="mt-4 !text-2xl !font-bold pl-2"
                        >
                        </Label>
                    </div>
                    <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                </div>
				<div class="input-group-wrapper grid grid-cols-1 lg:grid-cols-1 gap-8">
                    <div class="input-group col-span-2 grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <CustomTextInputField
                            :inputType="'textarea'"
                            :inputId="'user_memo'"
                            :labelValue="'Memo'"
                            :dataValue="form.user_memo"
                            class="col-span-4 w-full"
                            :withTooltip="true"
                            :errorMessage="(form.errors) ? form.errors.user_memo : '' "
                            v-model="form.user_memo"
                        >
                            <h3>Memo will be visible to your account manager(s).</h3>
                        </CustomTextInputField>
                        <CustomTextInputField
                            :inputType="'textarea'"
                            :inputId="'admin_memo'"
                            :labelValue="'Admin Memo'"
                            :dataValue="form.admin_memo"
                            class="col-span-4 w-full"
                            :withTooltip="true"
                            :errorMessage="(form.errors) ? form.errors.admin_memo : '' "
                            v-model="form.admin_memo"
                        >
                            <h3>Memo will be visible to the user.</h3>
                        </CustomTextInputField>
                        <CustomTextInputField
                            :inputType="'textarea'"
                            :inputId="'admin_remark'"
                            :labelValue="'Admin Remark'"
                            :dataValue="form.admin_remark"
                            class="col-span-4 w-full"
                            :withTooltip="true"
                            :errorMessage="(form.errors) ? form.errors.admin_remark : '' "
                            v-model="form.admin_remark"
                        >
                            <h3>Remark is private and only can be seen by admins.</h3>
                        </CustomTextInputField>
                    </div>
				</div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="form-input-section dark:bg-dark-eval-1 col-span-9">
                    <div class="px-3">
                        <div class="flex flex-row justify-between items-center mb-2">
                            <Label
                                :value="'Crypto & USD'"
                                class="mt-4 !text-2xl !font-bold pl-2"
                            >
                            </Label>
                        </div>
                        <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                    </div>
                    <div class="input-group-wrapper grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <div class="input-group col-span-8 grid grid-cols-1 lg:grid-cols-12 gap-6">
                                <CustomSelectInputField
                                    :inputArray="paymentMethodsList"
                                    :inputId="'payment_method_id'"
                                    :labelValue="'Payment Method'"
                                    :customValue="true"
                                    :errorMessage="(form.errors) ? form.errors.payment_method_id : '' "
                                    class="col-span-6 w-full"
                                    v-model="form.payment_method_id"
                                />
                                <CustomTextInputField
                                    :inputType="'number'"
                                    :inputId="'amount'"
                                    :labelValue="'Crypto Amount'"
                                    :dataValue="parseFloat(form.amount).toFixed(2)"
                                    :decimalOption="true"
                                    :step="0.01"
                                    class="col-start-1 col-span-6"
                                    :withTooltip="true"
                                    :errorMessage="(form.errors) ? form.errors.amount : '' "
                                    @keypress="isNumber($event)"
                                    v-model="form.amount"
                                >
                                    <h3>This amount does not affect customer wallet balance.</h3>
                                </CustomTextInputField>
                                <CustomTextInputField
                                    :inputType="'number'"
                                    :inputId="'converted_amount'"
                                    :labelValue="'Converted USD Amount'"
                                    :dataValue="parseFloat(form.converted_amount).toFixed(2)"
                                    :decimalOption="true"
                                    :step="0.01"
                                    class="col-span-6"
                                    :withTooltip="true"
                                    :errorMessage="(form.errors) ? form.errors.converted_amount : '' "
                                    @keypress="isNumber($event)"
                                    v-model="form.converted_amount"
                                >
                                    <h3>The converted amount in USD that will be added to customer wallet. Please ensure the amount is correct.</h3>
                                </CustomTextInputField>
                        </div>
                        <div class="input-group col-span-4 flex flex-col gap-6">
                            <CustomLabelGroup
                                :inputId="'status'"
                                :labelValue="'Status'"
                                :dataValue="false"
                                class="text-gray-300"
                            >
                                <Label
                                    :for="'statusLabel'"
                                    class="py-2 px-3 border border-gray-400 rounded-md h-[41px]
                                            dark:border-gray-600 dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1"
                                >
                                    {{ 'Pending' }}
                                </Label>
                            </CustomLabelGroup>
                            <!-- <CustomLabelGroup
                                :inputId="'approveBtn'"
                                :labelValue="'Approve'"
                                :dataValue="false"
                                :withTooltip="true"
                                class="text-gray-300"
                            >
                                <template #tooltipContent>
                                    <h3>Approve this payment and the converted USD amount will be added to customer wallet automatically.</h3>
                                </template>
                                <div class="my-4 ml-6">
                                    <Button 
                                        :id="'approveBtn'"
                                        :type="'button'"
                                        :size="'sm'"
                                        class="self-center justify-center gap-2 w-full lg:w-3/5 text-sm" 
                                        @click="openApprovalConfirmationModal()"
                                    >
                                        Approve
                                    </Button>
                                    <Modal 
                                        :show="showApprovalConfirmationModal" 
                                        maxWidth="2xl" 
                                        :closeable="true" 
                                        @close="closeApprovalConfirmationModal">

                                        <div class="modal">
                                            <p class="text-gray-200 text-2xl bg-cyan-600 p-4 rounded-md font-bold">
                                                Payment Submission Approval Confirmation.
                                            </p>
                                            <p class="text-gray-200 text-lg p-4">
                                                Are you sure that you want to <span class="font-bold">approve</span> this payment?
                                            </p>
                                        </div>
                                        <div class="flex flex-row justify-end p-9">
                                            <div class="dark:bg-gray-600 p-4 rounded-md flex gap-4">
                                                <Button 
                                                    :type="'button'"
                                                    :variant="'info'" 
                                                    :size="'sm'"
                                                    @click="closeApprovalConfirmationModal"
                                                    class="justify-center px-6 py-2"
                                                >
                                                    <span>Back</span>   
                                                </Button>
                                                <Button 
                                                    :type="'button'"
                                                    :variant="'danger'" 
                                                    :size="'sm'" 
                                                    class="justify-center px-6 py-2"
                                                    @click="closeApprovalConfirmationModal"
                                                >
                                                    <span>Confirm</span>
                                                </Button>
                                            </div>
                                        </div>
                                    </Modal>
                                </div>
                            </CustomLabelGroup> -->
                            <CustomLabelGroup
                                :inputId="'approved_at'"
                                :labelValue="'Approved At'"
                                :dataValue="'-'"
                                class="text-gray-300"
                            />
                        </div>
                    </div>
                </div>
                <div class="col-span-3">
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="px-3">
                            <div class="flex flex-row justify-between items-center mb-2">
                                <Label
                                    :value="'Important Dates'"
                                    class="mt-4 !text-2xl !font-bold pl-2"
                                >
                                </Label>
                            </div>
                            <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                        </div>
                        <div class="input-group-wrapper">
                            <div class="input-group col-span-2 flex flex-col gap-6">
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
