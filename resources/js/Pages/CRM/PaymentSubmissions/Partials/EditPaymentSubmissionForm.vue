<script setup>
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { ref, onMounted, reactive, computed } from 'vue';
import { 
    cl, back, populateArrayFromResponse, convertToIndexedValueObject, 
    setDateTimeWithOffset, formatToUserTimezone 
} from '@/Composables'
import { useForm, usePage, Link } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';
import { CheckCircleFillIcon } from '@/Components/Icons/solid';
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue';
import CustomTextInputField from '@/Components/CustomTextInputField.vue';
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue';
import CustomDateTimeInputField from '@/Components/CustomDateTimeInputField.vue';

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
const page = usePage();
const usersList = reactive({});
const paymentMethodsList = reactive({});
const showApprovalConfirmationModal = ref(false);

const user = computed(() => page.props.auth.user)

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	id: props.data.id,
	date: props.data.date,
	amount: props.data.amount,
	converted_amount: props.data.converted_amount,
	user_memo: props.data.user_memo,
	admin_memo: props.data.admin_memo,
	admin_remark: props.data.admin_remark,
	status: props.data.status,
	approved_at: props.data.approved_at,
	edited_at: props.data.edited_at,
	created_at: `${props.data.created_at}00`,
	payment_method_id: props.data.payment_method_id,
	user_id: props.data.user_id,
});

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

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
	form.amount = parseFloat(form.amount) ?? '';
	form.converted_amount = parseFloat(form.converted_amount) ?? '';
	form.edited_at = setDateTimeWithOffset(true);

	form.put(route('payment-submissions.update', props.data.id), {
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

const confirmPaymentApproval = () => {
	axios.post(route('payment-submissions.approvePaymentSubmission'), {
			id: form.id,
			approved_at: setDateTimeWithOffset(true),
		})
		.then(response => {
			alert(response);
			window.location.reload();
		})
		.catch(error => {
			console.error("There was an error!", error);
			form.approved_at = '';
		});

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
					<div class="input-group col-span-1 grid grid-cols-1 lg:grid-cols-2 gap-6">
						<CustomSelectInputField
							:inputArray="usersList"
							:inputId="'user_id'"
							:labelValue="'User'"
							:dataValue="form.user_id"
							:customValue="true"
							:errorMessage="(form.errors) ? form.errors.user_id : '' "
							v-model="form.user_id"
							v-if="form.status === 1"
						/>
						<CustomLabelGroup
							:inputId="'user_id'"
							:labelValue="'User'"
							:dataValue="false"
							v-else
						>
							<Link
								:href="route('users-clients.edit', props.data.user.id)"
								class="font-medium"
							>
								<Label
									:id="'user_id_lbl'"
									class="py-2 px-3 border border-gray-400 rounded-md h-[41px] cursor-pointer
											dark:border-gray-600 dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1"
								>
									<strong><span class="text-cyan-500 hover:text-cyan-600">{{ props.data.user.full_name + ' (' + props.data.user.site.name + ')' }}</span></strong>
								</Label>
							</Link>
						</CustomLabelGroup>
						<CustomDateTimeInputField
							:labelValue="'Date'"
							:inputId="'date'"
							:dateTimeOpt="false"
							:dataValue="form.date"
							:errorMessage="(form.errors) ? form.errors.date : '' "
							v-model="form.date"
							v-if="form.status === 1"
						/>
						<CustomLabelGroup
							:inputId="'date'"
							:labelValue="'Date'"
							:dataValue="form.date"
							v-else
						/>
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
                                    :dataValue="form.payment_method_id"
                                    :errorMessage="(form.errors) ? form.errors.payment_method_id : '' "
                                    class="col-span-6 w-full"
                                    v-model="form.payment_method_id"
									v-if="form.status !== 2"
                                />
								<CustomLabelGroup
									:inputId="'payment_method_id'"
									:labelValue="'Payment Method'"
                                    class="col-span-6 w-full"
									:dataValue="false"
									v-else
								>
									<Label
										:id="'payment_method_id_lbl'"
										class="py-2 px-3 border border-gray-400 rounded-md h-[41px]
												dark:border-gray-600 dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1"
									>
										<strong><span class="">{{ props.data.payment_method.title }}</span></strong>
									</Label>
									<!-- <Link
										:href="route('users-clients.edit', props.data.user.id)"
										class="font-medium"
									>
										<Label
											:id="'payment_method_id_lbl'"
											class="py-2 px-3 border border-gray-400 rounded-md h-[41px] cursor-pointer
													dark:border-gray-600 dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1"
										>
											<strong><span class="text-cyan-500 hover:text-cyan-600">{{ props.data.payment_method.title }}</span></strong>
										</Label>
									</Link> -->
								</CustomLabelGroup>
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
									v-if="form.status !== 2"
                                >
                                    <h3>The converted amount in USD that will be added to customer wallet. Please ensure the amount is correct.</h3>
                                </CustomTextInputField>
								<CustomLabelGroup
									:inputId="'converted_amount'"
									:labelValue="'Converted USD Amount'"
									:dataValue="parseFloat(form.converted_amount).toFixed(2)"
                                    :withTooltip="true"
                                    class="col-span-6"
									v-else
								>
									<template #tooltipContent>
										<h3>The converted amount in USD that will be added to customer wallet. Please ensure the amount is correct.</h3>
									</template>
								</CustomLabelGroup>
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
                                    {{ (form.status === 2) ? 'Approved' : 'Pending' }}
                                </Label>
                            </CustomLabelGroup>
                            <CustomLabelGroup
                                :inputId="'approveBtn'"
                                :labelValue="'Approve'"
                                :dataValue="false"
                                :withTooltip="true"
                                class="text-gray-300"
								v-if="form.status !== 2"
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
                                                    @click="confirmPaymentApproval"
                                                >
                                                    <span>Confirm</span>
                                                </Button>
                                            </div>
                                        </div>
                                    </Modal>
                                </div>
                            </CustomLabelGroup>
							<CustomLabelGroup
                                :inputId="'approveGroup'"
                                :labelValue="'Approve'"
                                :dataValue="false"
                                :withTooltip="true"
                                class="text-gray-300"
								v-else
                            >
                                <template #tooltipContent>
                                    <h3>Payment has been approved and the converted USD amount has been added to customer wallet.</h3>
                                </template>
								<div class="flex justify-center items-center">
									<div class="rounded-md px-4 py-2 flex justify-stretch">
										<CheckCircleFillIcon 
											class="flex-shrink-0 w-7 h-7" 
											aria-hidden="true" 
										/> 
										Approved
									</div>
								</div>
                            </CustomLabelGroup>
                            <CustomLabelGroup
                                :inputId="'approved_at'"
                                :labelValue="'Approved At'"
                                :dataValue="formatToUserTimezone(props.data.approved_at, user.timezone, true) ?? '-'"
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
                                    :dataValue="formatToUserTimezone(props.data.edited_at, user.timezone, true)"
                                />
                                <CustomLabelGroup
                                    :inputId="'leadFrontCreatedAt'"
                                    :labelValue="'Created at'"
                                    :dataValue="formatToUserTimezone(props.data.created_at, user.timezone, true)"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
