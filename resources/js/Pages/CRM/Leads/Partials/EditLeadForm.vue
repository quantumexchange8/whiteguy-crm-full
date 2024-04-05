<script setup>
import dayjs from 'dayjs';
import { ref, onMounted, reactive, computed, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { cl, back, populateArrayFromResponse, setDateTimeWithOffset, setFormattedDateTimeWithOffset, formatToUserTimezone } from '@/Composables'
import LeadChangelogsModalSection from './LeadChangelogsModalSection.vue'
import Modal from '@/Components/Modal.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Checkbox from '@/Components/Checkbox.vue'
import { PlusIcon } from '@/Components/Icons/solid'
import { TrashIcon } from '@/Components/Icons/outline'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import CustomTextInputField from '@/Components/CustomTextInputField.vue'
import CustomSelectInputField from '@/Components/CustomSelectInputField.vue'
import CustomDateTimeInputField from '@/Components/CustomDateTimeInputField.vue'

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

const page = usePage();
const stageArray = reactive({});
const contactOutcomeArray = reactive({});
const appointmentLabelArray = reactive({});
const userListArray  = reactive({});
const newLeadNotesArray  = reactive([]);
const showPersonalInformationSection= ref(true);
const showLeadDetailsSection = ref(true);
const showLeadFrontForm = ref((props.leadFrontData) ? true : false);
const showButtonShow = ref((props.leadFrontData) ? false : true);
const clearButtonShow = ref((props.leadFrontData) ? true : false);
const showModal = ref(false);
const selectedLeadNote = ref(null);
const leadFrontEditedAt = ref('-');
const leadFrontCreatedAt = ref('-');

const user = computed(() => page.props.auth.user)

onMounted(async () => {
    try {
        const leadStageResponse = await axios.get(route('leads.getAllLeadStages'));
        populateArrayFromResponse(leadStageResponse.data, stageArray, 'id', 'title');

        const leadContactOutcomeResponse = await axios.get(route('leads.getAllLeadContactOutcomes'));
        populateArrayFromResponse(leadContactOutcomeResponse.data, contactOutcomeArray, 'id', 'title');

        const leadAppointmentLabelResponse = await axios.get(route('leads.getAllLeadAppointmentLabels'));
        populateArrayFromResponse(leadAppointmentLabelResponse.data, appointmentLabelArray, 'id', 'title');

        const userListResponse = await axios.get(route('users-clients.getUserList'));

        userListResponse.data.forEach(item => {
            userListArray[item['id']] = {
                value: item['username'] + " (" + item.site['name'] + ")",
            };
        });
    } catch (error) {
        console.error('Error fetching data:', error);
    }

    if (props.leadFrontData) {
        leadFrontCreatedAt.value = props.leadFrontData.created_at;
    } else {
        leadFrontCreatedAt.value = '-';
    }
});

// Create a form with the following fields to make accessing the errors and posting more convenient
const form = useForm({
	id: props.data.id,
	date: props.data.date,
	first_name: props.data.first_name,
	last_name: props.data.last_name,
	country: props.data.country,
	date_of_birth: props.data.date_of_birth,
	occupation: props.data.occupation,
	vc: props.data.vc,
	sdm: props.data.sdm,
	email: props.data.email,
	email_alt_1: props.data.email_alt_1,
	email_alt_2: props.data.email_alt_2,
	email_alt_3: props.data.email_alt_3,
	phone_number: props.data.phone_number,
	phone_number_alt_1: props.data.phone_number_alt_1,
	phone_number_alt_2: props.data.phone_number_alt_2,
	phone_number_alt_3: props.data.phone_number_alt_3,
	attachment: props.data.attachment,
	private_remark: props.data.private_remark,
	remark: props.data.remark,
	data_source: props.data.data_source,
	appointment_start_at: props.data.appointment_start_at,
	appointment_end_at: props.data.appointment_end_at,
	contacted_at: props.data.contacted_at,
	assignee_read_at: props.data.assignee_read_at,
	edited_at: props.data.edited_at,
	created_at: `${props.data.created_at}00`,
	appointment_label_id: props.data.appointment_label_id,
	assignee_id: props.data.assignee_id,
	contact_outcome_id: props.data.contact_outcome_id,
	created_by_id: props.data.created_by_id,
	stage_id: props.data.stage_id,
	give_up_at: props.data.give_up_at,
	account_manager: props.data.account_manager,
	address: props.data.address,
	agents_book: props.data.agents_book,
	campaign_product: props.data.campaign_product,
	data_code: props.data.data_code,
	data_type: props.data.data_type,
	deleted_at: props.data.deleted_at,
	deleted_note: props.data.deleted_note,
	sort_id: props.data.sort_id,
    create_lead_front: (props.leadFrontData) ? true : false,
    lead_front_id: (props.leadFrontData) ? props.leadFrontData.id : '',
	lead_front_name: (props.leadFrontData) ? props.leadFrontData.name : '',
	lead_front_mimo: (props.leadFrontData) ? props.leadFrontData.mimo : '',
	lead_front_product: (props.leadFrontData) ? props.leadFrontData.product : '',
	lead_front_quantity: (props.leadFrontData) ? props.leadFrontData.quantity : '',
	lead_front_price: (props.leadFrontData) ? props.leadFrontData.price : '',
	lead_front_sdm: (props.leadFrontData) ? props.leadFrontData.sdm : false,
	lead_front_liquid: (props.leadFrontData) ? props.leadFrontData.liquid : false,
	lead_front_bank: (props.leadFrontData) ? props.leadFrontData.bank : '',
	lead_front_note: (props.leadFrontData) ? props.leadFrontData.note : '',
	lead_front_commission: (props.leadFrontData) ? props.leadFrontData.commission : '',
	lead_front_vc: (props.leadFrontData) ? props.leadFrontData.vc : '',
	lead_front_edited_at: (props.leadFrontData) ? props.leadFrontData.edited_at : '',
	lead_front_created_at: (props.leadFrontData) ? `${props.leadFrontData.created_at}00` : '',
	lead_id: (props.leadFrontData) ? props.leadFrontData.lead_id : '',
	lead_front_email: (props.leadFrontData) ? props.leadFrontData.lead_front_email : '',
	lead_front_phone_number: (props.leadFrontData) ? props.leadFrontData.lead_front_phone_number : '',
    lead_notes: (props.leadNotesData) ? props.leadNotesData : [],
});

// Post form fields to controller after executing the checking and parsing the input fields
const formSubmit = () => {
    form.edited_at = setDateTimeWithOffset(true);
    form.date = form.date ? setFormattedDateTimeWithOffset(form.date ,true) : form.date;
    form.appointment_start_at = form.appointment_start_at ? setFormattedDateTimeWithOffset(form.appointment_start_at, true) : form.appointment_start_at;
    form.appointment_end_at = form.appointment_end_at ? setFormattedDateTimeWithOffset(form.appointment_end_at, true) : form.appointment_end_at;
    form.contacted_at = form.contacted_at ? setFormattedDateTimeWithOffset(form.contacted_at, true) : form.contacted_at;
    form.assignee_read_at = form.assignee_read_at ? setFormattedDateTimeWithOffset(form.assignee_read_at, true) : form.assignee_read_at;
    form.give_up_at = form.give_up_at ? setFormattedDateTimeWithOffset(form.give_up_at, true) : form.give_up_at;
    form.deleted_at = form.deleted_at ? setFormattedDateTimeWithOffset(form.deleted_at, true) : form.deleted_at;

    // let dayete = dayjs().utcOffset('+11:00').format('YYYY-MM-DD HH:mm:ss.SSSSSSZZ');
    // let dayetes = form.date;
    // cl(dayetes);

	if (form.create_lead_front) {
        form.lead_front_quantity = isValidNumber(form.lead_front_quantity) ? parseFloat(form.lead_front_quantity) : 0;
        form.lead_front_price = isValidNumber(form.lead_front_price) ? parseFloat(form.lead_front_price) : 0;
        form.lead_front_commission = isValidNumber(form.lead_front_commission) ? parseFloat(form.lead_front_commission) : 0;
        form.lead_front_edited_at = setDateTimeWithOffset(true);
        form.lead_front_created_at = form.lead_front_created_at ? form.lead_front_created_at : setDateTimeWithOffset(true);
        form.lead_front_email = form.email;
        form.lead_front_phone_number = form.phone_number;
    }

    // cl(form.lead_notes);
    form.lead_notes.forEach(note => {
        note.created_at = (note.created_at === '') 
                            ? note.created_at = setDateTimeWithOffset(true) 
                            : setFormattedDateTimeWithOffset(note.created_at, true);
        
        note.edited_at = setDateTimeWithOffset(true);
    });

	form.put(route('leads.update', props.data.id), {
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

// Unhide the Lead Front Form and Clear button, and hide itself
const expandLeadFrontForm = () => {
	showLeadFrontForm.value = true;
	clearButtonShow.value = true;
	showButtonShow.value = false;
	form.lead_id = props.data.id;
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
    form.lead_front_quantity = 0.00;
    form.lead_front_price = 0.00;
    form.lead_front_vc = '';
    form.lead_front_sdm = false;
    form.lead_front_liquid = false;
    form.lead_front_bank = '';
    form.lead_front_note = '';
    form.lead_front_commission = 0.00;
    form.lead_front_edited_at = '-';
    form.lead_front_created_at = '-';
	form.lead_id = '';
	form.lead_front_email = '';
	form.lead_front_phone_number = '';
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
                    lead_front_quantity: 0.00,
                    lead_front_price: 0.00,
                    lead_front_vc: '',
                    lead_front_sdm: false,
                    lead_front_liquid: false,
                    lead_front_bank: '',
                    lead_front_note: '',
                    lead_front_commission: 0.00,
                    lead_front_edited_at: '-',
                    lead_front_created_at: '-',
                    lead_id: '',
                    lead_front_email: '',
                    lead_front_phone_number: '',
                });
                clearLeadFrontFormFields();
            },
        
        })
    } else {
        form.create_lead_front = false;
        form.lead_front_name = '';
        form.lead_front_mimo = '';
        form.lead_front_product = '';
        form.lead_front_quantity = 0.00;
        form.lead_front_price = 0.00;
        form.lead_front_vc = '';
        form.lead_front_sdm = false;
        form.lead_front_liquid = false;
        form.lead_front_bank = '';
        form.lead_front_note = '';
        form.lead_front_commission = 0.00;
        form.lead_front_edited_at = '';
        form.lead_front_created_at = '';
	    form.lead_id = '';
        form.lead_front_email = '';
        form.lead_front_phone_number = '';
        
        closeModal();
    }
}

// const deleteLeadNote = (id) => {
//     form.delete(route('leads.deleteLeadNote', id));
// }

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
        'attachment': '',
        'edited_at': '',
        'created_at': '',
        'created_by_id': '',
        'user_editable': true,
    });
}

// Filtered array containing only new lead notes
const filteredLeadNotes = computed(() => {
  const existingLeadNoteIds = props.leadNotesData.map(note => note.id)
  return form.lead_notes.filter(note => !existingLeadNoteIds.includes(note.id))
})

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

// watch(() => newLeadNotesArray, (newVal) => {
//     if (newVal) {
//         // Loop through each new lead note
//         newVal.forEach(newLeadNote => {
//             // Push the new lead note into form.lead_notes
//             form.lead_notes.push(newLeadNote);
//         });
//         // Clear the newLeadNotesArray after merging
//         newLeadNotesArray.value = [];
//     }
//     cl(form.lead_notes);

// }, { deep: true });

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
                    <div class="input-group col-span-1 flex flex-col gap-6">
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
                        <CustomTextInputField
                            :labelValue="'DOB'"
                            :inputId="'dob'"
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
                    <div class="col-span-2 flex flex-col gap-8">
                        <div class="input-group grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <CustomTextInputField
                                :inputId="'phoneNumber'"
                                :labelValue="'Phone Number'"
                                :errorMessage="(form.errors) ? form.errors.phone_number : '' "
                                :dataValue="props.data.phone_number"
                                v-model="form.phone_number"
                            />
                            <CustomTextInputField
                                :inputId="'addPhoneNumber1'"
                                :labelValue="'Additional Phone Number 1'"
                                :errorMessage="(form.errors) ? form.errors.phone_number_alt_1 : '' "
                                :dataValue="props.data.phone_number_alt_1"
                                v-model="form.phone_number_alt_1"
                            />
                            <CustomTextInputField
                                :inputId="'addPhoneNumber2'"
                                :labelValue="'Additional Phone Number 2'"
                                :errorMessage="(form.errors) ? form.errors.phone_number_alt_2 : '' "
                                :dataValue="props.data.phone_number_alt_2"
                                v-model="form.phone_number_alt_2"
                            />
                            <CustomTextInputField
                                :inputId="'addPhoneNumber3'"
                                :labelValue="'Additional Phone Number 3'"
                                :errorMessage="(form.errors) ? form.errors.phone_number_alt_3 : '' "
                                :dataValue="props.data.phone_number_alt_3"
                                v-model="form.phone_number_alt_3"
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
                    <div class="input-group col-span-1 flex flex-col gap-6">
                        <CustomDateTimeInputField
                            :labelValue="'Date Opp&rsquo;d In'"
                            :inputId="'dateOppdIn'"
                            :dateTimeOpt="true"
                            :errorMessage="(form.errors) ? form.errors.date : '' "
                            :dataValue="props.data.date"
                            v-model="form.date"
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
                    <div class="col-span-2 flex flex-col gap-8">
                        <div class="input-group grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <CustomSelectInputField
                                :inputArray="appointmentLabelArray"
                                :inputId="'appointmentLabel'"
                                :labelValue="'Appointment Label'"
                                :customValue="true"
                                :errorMessage="(form.errors) ? form.errors.appointment_label_id : '' "
                                :dataValue="props.data.appointment_label_id"
                                v-model="form.appointment_label_id"
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
                                :errorMessage="(form.errors) ? form.errors.contacted_at : '' "
                                :dataValue="props.data.contacted_at"
                                v-model="form.contacted_at"
                            />
                            <CustomSelectInputField
                                :inputArray="contactOutcomeArray"
                                :inputId="'contactOutcome'"
                                :labelValue="'Contact Outcome'"
                                :customValue="true"
                                :errorMessage="(form.errors) ? form.errors.contact_outcome_id : '' "
                                :dataValue="props.data.contact_outcome_id"
                                v-model="form.contact_outcome_id"
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
                                :inputArray="userListArray"
                                :inputId="'assignee_id'"
                                :labelValue="'Assignee'"
                                :customValue="true"
                                :errorMessage="(form.errors) ? form.errors.assignee_id : '' "
                                :dataValue="props.data.assignee_id"
                                v-model="form.assignee_id"
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
                                :inputId="'stage_id'"
                                :labelValue="'Stage'"
                                :customValue="true"
                                :errorMessage="(form.errors) ? form.errors.stage_id : '' "
                                :dataValue="props.data.stage_id"
                                v-model="form.stage_id"
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
                                :inputArray="userListArray"
                                :inputId="'createdBy'"
                                :labelValue="'Created By'"
                                :customValue="true"
                                :errorMessage="(form.errors) ? form.errors.created_by_id : '' "
                                :dataValue="props.data.created_by_id"
                                v-model="form.created_by_id"
                            />
                            <CustomDateTimeInputField
                                :labelValue="'Delete At'"
                                :inputId="'deletedAt'"
                                :dateTimeOpt="true"
                                :errorMessage="(form.errors) ? form.errors.deleted_at : '' "
                                :dataValue="props.data.deleted_at"
                                v-model="form.deleted_at"
                            />
                        </div>
                    </div>
                </div>
            </div>
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
                                    :dataValue="props.leadFrontData?.name ?? ''"
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
                                            :dataValue="parseFloat(props.leadFrontData ? props.leadFrontData.quantity : 0).toFixed(2)"
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
                                            :dataValue="parseFloat(props.leadFrontData ? props.leadFrontData.price : 0).toFixed(2)"
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
                                            :inputType="'textarea'"
                                            :inputId="'leadFrontBan'"
                                            :labelValue="'Bank'"
                                            :dataValue="props.leadFrontData ? props.leadFrontData.bank : ''"
                                            class="col-span-full"
                                            :errorMessage="(form.errors) ? form.errors.lead_front_bank : '' "
                                            v-model="form.lead_front_bank"
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
                                            :dataValue="parseFloat(props.leadFrontData ? props.leadFrontData.commission : 0).toFixed(2)"
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
                                            :dataValue="props.leadFrontData ? formatToUserTimezone(props.leadFrontData.edited_at, user.timezone, true) : '-'"
                                            v-model="form.lead_front_edited_at"
                                        />
                                        <CustomLabelGroup
                                            ref="leadFrontCreatedAt"
                                            :inputId="'leadFrontCreatedAt'"
                                            :labelValue="'Created at'"
                                            :dataValue="props.leadFrontData ? formatToUserTimezone(props.leadFrontData.created_at, user.timezone, true) : '-'"
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
                            <LeadChangelogsModalSection
                                :selectedRowData="props.data"
                                :withHistory="false"
                                class="w-full"
                            />
                            <div class="grid grid-cols-1 sm:grid-cols-8 gap-8" v-for="(item, i) in filteredLeadNotes" :key="i">
                                <hr class="divider !mb-0 col-span-8" v-if="i > 0">
                                <div class="col-span-8 sm:col-span-7 grid grid-cols-1 lg:grid-cols-3 gap-8">
                                    <div class="input-group col-span-3 flex flex-col gap-6">
                                        <CustomTextInputField
                                            :inputType="'textarea'"
                                            :inputId="'leadNotesNote_'+(i + props.leadNotesData.length)"
                                            :labelValue="'Note'"
                                            :errorMessage="(form.errors) ? form.errors['lead_notes.' + (i + props.leadNotesData.length) + '.note']  : '' "
                                            v-model="item.note"
                                            class="col-span-3"
                                        />
                                        <div class="grid grid-cols-1 sm:grid-cols-3 col-span-3">
                                            <Checkbox
                                                v-model:checked="item.user_editable"
                                                :inputId="'leadNotesNote_'+(i + props.leadNotesData.length)"
                                                :labelValue="'User Editable'"
                                                :defaultChecked="true"
                                                class="col-span-1"
                                            />
                                            <CustomSelectInputField
                                                :inputArray="userListArray"
                                                :inputId="'leadNotesCreatedBy_'+(i + props.leadNotesData.length)"
                                                :labelValue="'Created By'"
                                                :customValue="true"
                                                v-model="item.created_by_id"
                                                :errorMessage="(form.errors) ? form.errors['lead_notes.' + (i + props.leadNotesData.length) + '.created_by_id'] : '' "
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
                                        @click="openLeadNoteModal((i + props.leadNotesData.length))"
                                    >
                                        <TrashIcon class="flex-shrink-0 w-5 h-5 cursor-pointer" aria-hidden="true" />
                                    </Button>
                                    <Modal 
                                        :show="(i + props.leadNotesData.length) === selectedLeadNote" 
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
                                                    @click="removeLeadNote((i + props.leadNotesData.length), item.id)"
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
