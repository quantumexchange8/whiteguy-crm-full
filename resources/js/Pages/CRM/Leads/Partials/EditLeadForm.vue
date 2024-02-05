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

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    errors:Object,
    data: {
        type: Object,
        default: () => ({}),
    },
    leadFrontData: {
        type: Object,
        default: () => ({}),
    },
    leadNotesData: {
        type: Object,
        default: () => ({}),
    },
})

const showPersonalInformationSection= ref(true);
const showLeadDetailsSection = ref(true);
const showLeadFrontForm = ref((props.leadFrontData) ? true : false);
const showButtonShow = ref((props.leadFrontData) ? false : true);
const clearButtonShow = ref((props.leadFrontData) ? true : false);
const showModal = ref(false);
// const showLeadNoteModal = ref(false);
const selectedLeadNote = ref(null);
const leadFrontEditedAt = ref('-');
const leadFrontCreatedAt = ref('-');

onMounted(() => {
    // cl(props.leadFrontData.created_at);
    if (props.leadFrontData) {
        leadFrontCreatedAt.value = props.leadFrontData.created_at;
        cl(leadFrontCreatedAt.value);
    } else {
        leadFrontCreatedAt.value = '-';
    }
});

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
	id: props.data.id,
	first_name: props.data.first_name,
	last_name: props.data.last_name,
	country: props.data.country,
	address: props.data.address,
	date_oppd_in: props.data.date_oppd_in,
	campaign_product: props.data.campaign_product,
	sdm: props.data.sdm,
	date_of_birth: props.data.date_of_birth,
	occupation: props.data.occupation,
	agents_book: props.data.agents_book,
	account_manager: props.data.account_manager,
	vc: props.data.vc,
	data_type: props.data.data_type,
	data_source: props.data.data_source,
	data_code: props.data.data_code,
	email: props.data.email,
	email_alt_1: props.data.email_alt_1,
	email_alt_2: props.data.email_alt_2,
	email_alt_3: props.data.email_alt_3,
	phone_number: props.data.phone_number,
	phone_number_alt_1: props.data.phone_number_alt_1,
	phone_number_alt_2: props.data.phone_number_alt_2,
	phone_number_alt_3: props.data.phone_number_alt_3,
	private_remark: props.data.private_remark,
	remark: props.data.remark,
	appointment_start_at: props.data.appointment_start_at,
	appointment_end_at: props.data.appointment_end_at,
	last_called: props.data.last_called,
	assignee_read_at: props.data.assignee_read_at,
	give_up_at: props.data.give_up_at,
	appointment_label: props.data.appointment_label,
	contact_outcome: props.data.contact_outcome,
	stage: props.data.stage,
	assignee: props.data.assignee,
	created_by: props.data.created_by,
	delete_at: props.data.delete_at,
    create_lead_front: (props.leadFrontData) ? true : false,
    lead_front_id: (props.leadFrontData) ? props.leadFrontData.id : '',
	lead_front_name: (props.leadFrontData) ? props.leadFrontData.name : '',
	lead_front_mimo: (props.leadFrontData) ? props.leadFrontData.mimo : '',
	lead_front_product: (props.leadFrontData) ? props.leadFrontData.product : '',
	lead_front_quantity: (props.leadFrontData) ? props.leadFrontData.quantity : '',
	lead_front_price: (props.leadFrontData) ? props.leadFrontData.price : '',
	lead_front_sdm: (props.leadFrontData) ? Boolean(props.leadFrontData.sdm) : false,
	lead_front_liquid: (props.leadFrontData) ? Boolean(props.leadFrontData.liquid) : false,
	lead_front_bank_name: (props.leadFrontData) ? props.leadFrontData.bank_name : '',
	lead_front_bank_account: (props.leadFrontData) ? props.leadFrontData.bank_account : '',
	lead_front_note: (props.leadFrontData) ? props.leadFrontData.note : '',
	lead_front_commission: (props.leadFrontData) ? props.leadFrontData.commission : '',
	lead_front_vc: (props.leadFrontData) ? props.leadFrontData.vc : '',
	lead_front_edited_at: (props.leadFrontData) ? props.leadFrontData.edited_at : dayjs(new Date()).format('YYYY-MM-DD HH:mm:ss'),
	lead_front_created_at: (props.leadFrontData) ? props.leadFrontData.created_at : '-',
    lead_notes: (props.leadNotesData) ? props.leadNotesData : [],
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

	form.put(route('leads.update', props.data.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    
    })
};

const isNull = (value) => value === null;

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
    deleteLeadFront()
}

const clearLeadFrontFormFields = () => {
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
    form.lead_front_edited_at = '-';
    form.lead_front_created_at = '-';
    form.reset()

    closeModal()
}

const deleteLeadFront = () => {
    if (props.leadFrontData && props.leadFrontData.id) {
        form.delete(route('leads.deleteLeadFront', props.leadFrontData.id), {
            preserveScroll: true,
            preserveState : false,
            onSuccess: () => {
                form.defaults({
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
                    lead_front_edited_at: '-',
                    lead_front_created_at: '-',
                });
                clearLeadFrontFormFields();
            },
        
        })
    } else {
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
        
        closeModal();
    }
}

const deleteLeadNote = (id) => {
    form.delete(route('leads.deleteLeadNote', id));
}

const removeLeadNote = (i, id) => {
    form.lead_notes.splice(i, 1);

    if (id !== undefined) {
        deleteLeadNote(id);
    }
    closeLeadNoteModal();
}
const addLeadNote = () => {
    form.lead_notes.push({
        'note': '',
        'user_editable': false,
        'created_by': '',
    });
}

const openModal = () => {
     showModal.value = true;
}

const closeModal = () => {
    showModal.value = false;
}

const openLeadNoteModal = (i) => {
    selectedLeadNote.value = i;
}

const closeLeadNoteModal = () => {
    selectedLeadNote.value = null;
}

</script>

<template>
    <div class="form-wrapper">
        <form novalidate @submit.prevent="formSubmit">
            <div class="sticky top-[72px] z-10 mb-6 border border-gray-700 rounded-md">
                <div class="dark:bg-dark-eval-1 bg-white rounded-md flex flex-row items-center px-12 py-2 gap-8">
                    <p class="text-xl font-semibold leading-tight">Action</p>
                    <div class="border-l border-gray-500 h-20"></div>
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
                            :value="'Personal Information'"
                            class="mt-4 !text-2xl !font-bold pl-2"
                        >
                        </Label>
                        <div class="inline-block">
                            <Button 
                                :type="'button'"
                                :variant="'success'" 
                                :size="'sm'" 
                                class="justify-center px-6 py-2"
                                @click="showPersonalInformationSection = !showPersonalInformationSection"
                            >
                                {{ showPersonalInformationSection ? 'Collapse' : 'Expand' }}
                            </Button>
                        </div>
                    </div>
                    <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                </div>
                <div class="input-group-wrapper grid grid-cols-1 lg:grid-cols-3 gap-8" v-show="showPersonalInformationSection">
                    <div class="input-group col-span-1">
                        <CustomTextInputField
                            :labelValue="'First Name'"
                            :inputId="'firstName'"
                            :errorMessage="(form.errors) ? form.errors.first_name : '' "
                            :dataValue="props.data.first_name"
                            v-model="form.first_name"
                        />
                        <CustomTextInputField
                            :labelValue="'Last Name'"
                            :inputId="'lastName'"
                            :errorMessage="(form.errors) ? form.errors.last_name : '' "
                            :dataValue="props.data.last_name"
                            v-model="form.last_name"
                        />
                        <CustomDateTimeInputField
                            :labelValue="'DOB'"
                            :inputId="'dob'"
                            :dateTimeOpt="false"
                            :errorMessage="(form.errors) ? form.errors.date_of_birth : '' "
                            :dataValue="props.data.date_of_birth"
                            v-model="form.date_of_birth"
                        />
                        <CustomTextInputField
                            :inputId="'country'"
                            :labelValue="'Country'"
                            :errorMessage="(form.errors) ? form.errors.country : '' "
                            :dataValue="props.data.country"
                            v-model="form.country"
                        />
                        <CustomTextInputField
                            :inputType="'textarea'"
                            :inputId="'address'"
                            :labelValue="'Address'"
                            :errorMessage="(form.errors) ? form.errors.address : '' "
                            :dataValue="props.data.address"
                            v-model="form.address"
                        />
                    </div>
                    <div class="col-span-2 flex flex-col justify-between">
                        <div class="input-group grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <CustomTextInputField
                                :inputType="'number'"
                                :inputId="'phoneNumber'"
                                :labelValue="'Phone Number'"
                                :errorMessage="(form.errors) ? form.errors.phone_number : '' "
                                :dataValue="props.data.phone_number"
                                v-model="form.phone_number"
                                @keypress="isNumber($event)"
                            />
                            <CustomTextInputField
                                :inputType="'number'"
                                :inputId="'addPhoneNumber1'"
                                :labelValue="'Additional Phone Number 1'"
                                :errorMessage="(form.errors) ? form.errors.phone_number_alt_1 : '' "
                                :dataValue="props.data.phone_number_alt_1"
                                v-model="form.phone_number_alt_1"
                                @keypress="isNumber($event)"
                            />
                            <CustomTextInputField
                                :inputType="'number'"
                                :inputId="'addPhoneNumber2'"
                                :labelValue="'Additional Phone Number 2'"
                                :errorMessage="(form.errors) ? form.errors.phone_number_alt_2 : '' "
                                :dataValue="props.data.phone_number_alt_2"
                                v-model="form.phone_number_alt_2"
                                @keypress="isNumber($event)"
                            />
                            <CustomTextInputField
                                :inputType="'number'"
                                :inputId="'addPhoneNumber3'"
                                :labelValue="'Additional Phone Number 3'"
                                :errorMessage="(form.errors) ? form.errors.phone_number_alt_3 : '' "
                                :dataValue="props.data.phone_number_alt_3"
                                v-model="form.phone_number_alt_3"
                                @keypress="isNumber($event)"
                            />
                        </div>
                        <div class="input-group grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <CustomTextInputField
                                :inputType="'text'"
                                :inputId="'email'"
                                :labelValue="'Email'"
                                :errorMessage="(form.errors) ? form.errors.email : '' "
                                :dataValue="props.data.email"
                                v-model="form.email"
                            />
                            <CustomTextInputField
                                :inputType="'text'"
                                :inputId="'addEmail1'"
                                :labelValue="'Additional Email 1'"
                                :errorMessage="(form.errors) ? form.errors.email_alt_1 : '' "
                                :dataValue="props.data.email_alt_1"
                                v-model="form.email_alt_1"
                            />
                            <CustomTextInputField
                                :inputType="'text'"
                                :inputId="'addEmail2'"
                                :labelValue="'Additional Email 2'"
                                :errorMessage="(form.errors) ? form.errors.email_alt_2 : '' "
                                :dataValue="props.data.email_alt_2"
                                v-model="form.email_alt_2"
                            />
                            <CustomTextInputField
                                :inputType="'text'"
                                :inputId="'addEmail3'"
                                :labelValue="'Additional Email 3'"
                                :errorMessage="(form.errors) ? form.errors.email_alt_3 : '' "
                                :dataValue="props.data.email_alt_3"
                                v-model="form.email_alt_3"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <hr class="divider">
            <div class="form-input-section dark:bg-dark-eval-1">
                <div class="px-3">
                    <div class="flex flex-row justify-between items-center mb-2">
                        <Label
                            :value="'Lead Details'"
                            class="mt-4 !text-2xl !font-bold pl-2"
                        >
                        </Label>
                        <div class="inline-block">
                            <Button 
                                :type="'button'"
                                :variant="'success'" 
                                :size="'sm'" 
                                class="justify-center px-6 py-2"
                                @click="showLeadDetailsSection = !showLeadDetailsSection"
                            >
                                {{ showLeadDetailsSection ? 'Collapse' : 'Expand' }}
                            </Button>
                        </div>
                    </div>
                    <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                </div>
                <div class="input-group-wrapper grid grid-cols-1 lg:grid-cols-3 gap-8" v-show="showLeadDetailsSection">
                    <div class="input-group col-span-1">
                        <CustomDateTimeInputField
                            :labelValue="'Date Opp&rsquo;d In'"
                            :inputId="'dateOppdIn'"
                            :dateTimeOpt="true"
                            :errorMessage="(form.errors) ? form.errors.date_oppd_in : '' "
                            :dataValue="props.data.date_oppd_in"
                            v-model="form.date_oppd_in"
                        />
                        <CustomTextInputField
                            :inputId="'campaignProduct'"
                            :labelValue="'Campaign Product'"
                            :errorMessage="(form.errors) ? form.errors.campaign_product : '' "
                            :dataValue="props.data.campaign_product"
                            v-model="form.campaign_product"
                        />
                        <CustomTextInputField
                            :inputId="'sdm'"
                            :labelValue="'Sdm'"
                            :errorMessage="(form.errors) ? form.errors.sdm : '' "
                            :dataValue="props.data.sdm"
                            v-model="form.sdm"
                        />
                        <CustomTextInputField
                            :inputId="'occupation'"
                            :labelValue="'Occupation'"
                            :errorMessage="(form.errors) ? form.errors.occupation : '' "
                            :dataValue="props.data.occupation"
                            v-model="form.occupation"
                        />
                        <CustomTextInputField
                            :inputId="'agentsBook'"
                            :labelValue="'Agents Book'"
                            :errorMessage="(form.errors) ? form.errors.agents_book : '' "
                            :dataValue="props.data.agents_book"
                            v-model="form.agents_book"
                        />
                        <CustomTextInputField
                            :inputId="'accountManager'"
                            :labelValue="'Account Manager'"
                            :errorMessage="(form.errors) ? form.errors.account_manager : '' "
                            :dataValue="props.data.account_manager"
                            v-model="form.account_manager"
                        />
                        <CustomTextInputField
                            :inputId="'vc'"
                            :labelValue="'VC'"
                            :errorMessage="(form.errors) ? form.errors.vc : '' "
                            :dataValue="props.data.vc"
                            v-model="form.vc"
                        />
                        <CustomTextInputField
                            :inputId="'dataSource'"
                            :labelValue="'Data Source'"
                            :errorMessage="(form.errors) ? form.errors.data_source : '' "
                            :dataValue="props.data.data_source"
                            v-model="form.data_source"
                        />
                        <CustomTextInputField
                            :inputId="'dataCode'"
                            :labelValue="'Data Code'"
                            :errorMessage="(form.errors) ? form.errors.data_code : '' "
                            :dataValue="props.data.data_code"
                            v-model="form.data_code"
                        />
                        <CustomTextInputField
                            :inputId="'dataType'"
                            :labelValue="'Data Type'"
                            :errorMessage="(form.errors) ? form.errors.data_type : '' "
                            :dataValue="props.data.data_type"
                            v-model="form.data_type"
                        />
                    </div>
                    <!-- <div class="input-group">
                    </div> -->
                    <!-- <div class="input-group">
                        <CustomFileInputField
                            :inputId="'attachment'"
                            :labelValue="'Attachment'"
                        />
                    </div> -->
                    <div class="col-span-2 flex flex-col justify-between">
                        <div class="input-group grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <CustomSelectInputField
                                :inputArray="appointmentLabelArray"
                                :inputId="'appointmentLabel'"
                                :labelValue="'Appointment Label'"
                                :errorMessage="(form.errors) ? form.errors.appointment_label : '' "
                                :dataValue="props.data.appointment_label"
                                v-model="form.appointment_label"
                            />
                            <CustomDateTimeInputField
                                :labelValue="'Appointment Start'"
                                :inputId="'appointmentStart'"
                                :dateTimeOpt="true"
                                :errorMessage="(form.errors) ? form.errors.appointment_start_at : '' "
                                :dataValue="props.data.appointment_start_at"
                                v-model="form.appointment_start_at"
                            />
                            <CustomDateTimeInputField
                                :labelValue="'Appointment End'"
                                :inputId="'appointmentEnd'"
                                :dateTimeOpt="true"
                                :errorMessage="(form.errors) ? form.errors.appointment_end_at : '' "
                                :dataValue="props.data.appointment_end_at"
                                v-model="form.appointment_end_at"
                            />
                        </div>
                        <div class="input-group grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <CustomDateTimeInputField
                                :labelValue="'Last Called'"
                                :inputId="'lastCalled'"
                                :dateTimeOpt="true"
                                :errorMessage="(form.errors) ? form.errors.last_called : '' "
                                :dataValue="props.data.last_called"
                                v-model="form.last_called"
                            />
                            <CustomSelectInputField
                                :inputArray="contactOutcomeArray"
                                :inputId="'contactOutcome'"
                                :labelValue="'Contact Outcome'"
                                :errorMessage="(form.errors) ? form.errors.contact_outcome : '' "
                                :dataValue="props.data.contact_outcome"
                                v-model="form.contact_outcome"
                            />
                            <CustomDateTimeInputField
                                :labelValue="'Give Up?'"
                                :inputId="'giveUp'"
                                :dateTimeOpt="true"
                                :errorMessage="(form.errors) ? form.errors.give_up_at : '' "
                                :dataValue="props.data.give_up_at"
                                v-model="form.give_up_at"
                            />
                        </div>
                        <div class="input-group grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <CustomSelectInputField
                                :inputArray="assigneeArray"
                                :inputId="'assignee'"
                                :labelValue="'Assignee'"
                                :errorMessage="(form.errors) ? form.errors.assignee : '' "
                                :dataValue="props.data.assignee"
                                v-model="form.assignee"
                            />
                            <CustomDateTimeInputField
                                :labelValue="'Assignee Read At'"
                                :inputId="'assigneeReadAt'"
                                :dateTimeOpt="true"
                                :errorMessage="(form.errors) ? form.errors.assignee_read_at : '' "
                                :dataValue="props.data.assignee_read_at"
                                v-model="form.assignee_read_at"
                            />
                            <CustomSelectInputField
                                :inputArray="stageArray"
                                :inputId="'stage'"
                                :labelValue="'Stage'"
                                :errorMessage="(form.errors) ? form.errors.stage : '' "
                                :dataValue="props.data.stage"
                                v-model="form.stage"
                            />
                        </div>
                        <div class="input-group grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <CustomTextInputField
                                :inputType="'textarea'"
                                :inputId="'privateRemark'"
                                :labelValue="'Private Remark'"
                                :errorMessage="(form.errors) ? form.errors.private_remark : '' "
                                :dataValue="props.data.private_remark"
                                v-model="form.private_remark"
                            />
                            <CustomTextInputField
                                :inputType="'textarea'"
                                :inputId="'remark'"
                                :labelValue="'Remark'"
                                :errorMessage="(form.errors) ? form.errors.remark : '' "
                                :dataValue="props.data.remark"
                                v-model="form.remark"
                            />
                        </div>
                        <div class="input-group grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <CustomSelectInputField
                                :inputArray="createdByArray"
                                :inputId="'createdBy'"
                                :labelValue="'Created By'"
                                :errorMessage="(form.errors) ? form.errors.created_by : '' "
                                :dataValue="props.data.created_by"
                                v-model="form.created_by"
                            />
                            <CustomDateTimeInputField
                                :labelValue="'Delete At'"
                                :inputId="'deletedAt'"
                                :dateTimeOpt="true"
                                :errorMessage="(form.errors) ? form.errors.delete_at : '' "
                                :dataValue="props.data.delete_at"
                                v-model="form.delete_at"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <hr class="divider">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="px-3">
                            <div class="flex flex-row justify-between items-center mb-2">
                                <Label
                                    :value="'Lead Front'"
                                    class="mt-4 !text-2xl !font-bold pl-2"
                                >
                                </Label>
                                <div class="flex flex-row gap-2">
                                    <Button 
                                        :type="'button'"
                                        :variant="'success'" 
                                        :size="'sm'" 
                                        class="justify-center px-6 py-2"
                                        @click="expandLeadFrontForm"
                                        v-show="showButtonShow"
                                    >
                                        <PlusIcon class="flex-shrink-0 w-5 h-5 cursor-pointer" aria-hidden="true" />
                                        <span> Add</span>
                                    </Button>
                                    <Button 
                                        :type="'button'"
                                        :variant="'danger'" 
                                        :size="'sm'" 
                                        class="justify-center px-6 py-2"
                                        @click="openModal"
                                        v-show="clearButtonShow"
                                    >
                                        <span>Clear Form</span>
                                    </Button>
                                </div>
                            </div>
                            <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                            <Modal 
                                :show="showModal" 
                                maxWidth="2xl" 
                                :closeable="true" 
                                @close="closeModal">

                                <div class="modal">
                                    <p class="text-gray-200 text-2xl bg-red-500 p-4 rounded-md font-bold">Delete Lead Front Confirmation.</p>
                                    <p class="text-gray-200 text-lg p-4">Are you sure that you want to <span class="font-bold">delete</span> this lead front?</p>
                                </div>
                                <div class="flex flex-row justify-end p-9">
                                    <div class="dark:bg-gray-600 p-4 rounded-md flex gap-4">
                                        <Button 
                                            :type="'button'"
                                            :variant="'info'" 
                                            :size="'sm'"
                                            @click="closeModal"
                                            class="justify-center px-6 py-2"
                                        >
                                            <span>Back</span>   
                                        </Button>
                                        <Button 
                                            :type="'button'"
                                            :variant="'danger'" 
                                            :size="'sm'" 
                                            class="justify-center px-6 py-2"
                                            @click="clearLeadFrontForm"
                                        >
                                            <span>Confirm</span>
                                        </Button>
                                    </div>
                                </div>
                            </Modal>
                        </div>
                        <div class="input-group-wrapper grid grid-cols-1 lg:grid-cols-2 gap-8" v-show="showLeadFrontForm">
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
                                    :dataValue="props.leadFrontData ? props.leadFrontData.name : ''"
                                    :errorMessage="(form.errors) ? form.errors.lead_front_name : '' "
                                    v-model="form.lead_front_name"
                                />
                                <CustomTextInputField
                                    :inputType="'text'"
                                    :inputId="'leadFrontVc'"
                                    :labelValue="'VC'"
                                    :dataValue="props.leadFrontData ? props.leadFrontData.vc : ''"
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
                                            :dataValue="props.leadFrontData ? props.leadFrontData.mimo : ''"
                                            class="col-span-4 w-full"
                                            :errorMessage="(form.errors) ? form.errors.lead_front_mimo : '' "
                                            v-model="form.lead_front_mimo"
                                        />
                                        <CustomTextInputField
                                            :inputType="'text'"
                                            :inputId="'leadFrontProduct'"
                                            :labelValue="'Product'"
                                            :dataValue="props.leadFrontData ? props.leadFrontData.product : ''"
                                            class="col-span-2 w-full"
                                            :errorMessage="(form.errors) ? form.errors.lead_front_product : '' "
                                            v-model="form.lead_front_product"
                                        />
                                        <CustomTextInputField
                                            :inputType="'number'"
                                            :inputId="'leadFrontQuantity'"
                                            :labelValue="'Amount of Shares'"
                                            :dataValue="String(props.leadFrontData ? props.leadFrontData.quantity : '')"
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
                                            :dataValue="String(props.leadFrontData ? props.leadFrontData.price : '')"
                                            :decimalOption="true"
                                            :step="0.01"
                                            class="col-span-2"
                                            :errorMessage="(form.errors) ? form.errors.lead_front_price : ''"
                                            @keypress="isNumber($event)"
                                            v-model="form.lead_front_price"
                                        />
                                        <Checkbox
                                            v-model:checked="form.lead_front_sdm"
                                            :inputId="'leadFrontSdm'"
                                            :labelValue="'Sdm'"
                                            :errorMessage="(form.errors) ? form.errors.lead_front_sdm : ''"
                                        />
                                        <Checkbox
                                            v-model:checked="form.lead_front_liquid"
                                            :inputId="'leadFrontLiquid'"
                                            :labelValue="'Liquid'"
                                            :errorMessage="(form.errors) ? form.errors.lead_front_liquid : ''"
                                        />
                                        <CustomTextInputField
                                            :inputType="'text'"
                                            :inputId="'leadFrontBankName'"
                                            :labelValue="'Bank Name'"
                                            :dataValue="props.leadFrontData ? props.leadFrontData.bank_name : ''"
                                            class="col-span-2"
                                            :errorMessage="(form.errors) ? form.errors.lead_front_bank_name : '' "
                                            v-model="form.lead_front_bank_name"
                                        />
                                        <CustomTextInputField
                                            :inputType="'number'"
                                            :inputId="'leadFrontBankAccount'"
                                            :labelValue="'Bank Account'"
                                            :errorMessage="(form.errors) ? form.errors.lead_front_bank_account : '' "
                                            :dataValue="props.leadFrontData ? props.leadFrontData.bank_account : ''"
                                            class="col-span-2"
                                            v-model="form.lead_front_bank_account"
                                            @keypress="isNumber($event)"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2 flex flex-col gap-5">
                                <div class="input-group">
                                    <CustomTextInputField
                                        :inputType="'textarea'"
                                        :inputId="'leadFrontNote'"
                                        :labelValue="'Special Note'"
                                        :dataValue="props.leadFrontData ? props.leadFrontData.note : ''"
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
                                            :dataValue="String(props.leadFrontData ? props.leadFrontData.commission : '')"
                                            :decimalOption="true"
                                            :step="0.01"
                                            :errorMessage="(form.errors) ? form.errors.lead_front_commission : '' "
                                            @keypress="isNumber($event)"
                                            v-model="form.lead_front_commission"
                                        />
                                    </div>
                                    <div class="input-group">
                                        <CustomLabelGroup
                                            ref="leadFrontEditedAt"
                                            :inputId="'leadFrontEditedAt'"
                                            :labelValue="'Edited at'"
                                            :dataValue="form.lead_front_edited_at"
                                            v-model="form.lead_front_edited_at"
                                        />
                                        <CustomLabelGroup
                                            ref="leadFrontCreatedAt"
                                            :inputId="'leadFrontCreatedAt'"
                                            :labelValue="'Created at'"
                                            :dataValue="form.lead_front_created_at"
                                            v-model="form.lead_front_created_at"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="form-input-section dark:bg-dark-eval-1">
                        <div class="px-3">
                            <div class="flex flex-row justify-between items-center mb-2">
                                <Label
                                    :value="'Lead Notes'"
                                    class="mt-4 !text-2xl !font-bold pl-2"
                                >
                                </Label>
                                <div class="inline-block">
                                    <Button 
                                        :type="'button'"
                                        :variant="'success'" 
                                        :size="'sm'" 
                                        class="justify-center px-6 py-2"
                                        @click="addLeadNote"
                                    >
                                        Add Note
                                    </Button>
                                </div>
                            </div>
                            <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
                        </div>
                        <div class="input-group-wrapper flex flex-col gap-8">
                            <div class="grid grid-cols-1 sm:grid-cols-8 gap-8" v-for="(item, i) in form.lead_notes" :key="i">
                                <hr class="divider !mb-0 col-span-8" v-if="i > 0">
                                <div class="col-span-8 sm:col-span-7 grid grid-cols-1 lg:grid-cols-3 gap-8">
                                    <div class="input-group col-span-3">
                                        <CustomTextInputField
                                            :inputType="'textarea'"
                                            :inputId="'leadNotesNote_'+i"
                                            :labelValue="'Note'"
                                            :dataValue="item.note"
                                            :errorMessage="(form.errors) ? form.errors['lead_notes.' + i + '.note']  : '' "
                                            v-model="item.note"
                                            class="col-span-3"
                                        />
                                        <div class="grid grid-cols-1 sm:grid-cols-3 col-span-3">
                                            <Checkbox
                                                v-model:checked="item.user_editable"
                                                :inputId="'leadNotesNote_'+i"
                                                :labelValue="'User Editable'"
                                                class="col-span-1"
                                            />
                                            <CustomSelectInputField
                                                :inputArray="contactOutcomeArray"
                                                :inputId="'leadNotesCreatedBy_'+i"
                                                :labelValue="'Created By'"
                                                :dataValue="item.created_by"
                                                v-model="item.created_by"
                                                :errorMessage="(form.errors) ? form.errors['lead_notes.' + i + '.created_by'] : '' "
                                                class="col-span-2"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-8 sm:col-span-1 gap-6 inline-block">
                                    <Button 
                                        :type="'button'"
                                        :variant="'danger'" 
                                        :size="'sm'" 
                                        class="justify-center gap-2 w-full"
                                        @click="openLeadNoteModal(i)"
                                    >
                                        <TrashIcon class="flex-shrink-0 w-5 h-5 cursor-pointer" aria-hidden="true" />
                                    </Button>
                                    <Modal 
                                        :show="i === selectedLeadNote" 
                                        maxWidth="2xl" 
                                        :closeable="true" 
                                        @close="closeLeadNoteModal">

                                        <div class="modal">
                                            <p class="text-gray-200 text-2xl bg-red-500 p-4 rounded-md font-bold">Delete Lead Note Confirmation.</p>
                                            <p class="text-gray-200 text-lg p-4">Are you sure that you want to <span class="font-bold">delete</span> this lead note?</p>
                                        </div>
                                        <div class="flex flex-row justify-end p-9">
                                            <div class="dark:bg-gray-600 p-4 rounded-md flex gap-4">
                                                <Button 
                                                    :type="'button'"
                                                    :variant="'info'" 
                                                    :size="'sm'"
                                                    @click="closeLeadNoteModal"
                                                    class="justify-center px-6 py-2"
                                                >
                                                    <span>Back</span>   
                                                </Button>
                                                <Button 
                                                    :type="'button'"
                                                    :variant="'danger'" 
                                                    :size="'sm'" 
                                                    class="justify-center px-6 py-2"
                                                    @click="removeLeadNote(i, item.id)"
                                                >
                                                    <span>Confirm</span>
                                                </Button>
                                            </div>
                                        </div>
                                    </Modal>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
