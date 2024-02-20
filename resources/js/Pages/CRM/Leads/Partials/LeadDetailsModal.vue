<script setup>
import { ref, onMounted, reactive } from 'vue'
import { cl, back, convertToHumanReadable } from '@/Composables'
import { AtSymbolIcon } from '@heroicons/vue/outline'
import { 
    TabGroup, TabList, Tab, TabPanels, TabPanel, Disclosure, DisclosureButton, DisclosurePanel, TransitionRoot 
} from '@headlessui/vue'
import { 
    PhoneIcon, MapIcon, FlagIcon, CalendarIcon 
} from '@heroicons/vue/solid'
import { CheckCircleIcon, ErrorCircleIcon } from '@/Components/Icons/outline';
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import Button from '@/Components/Button.vue'
import Label from '@/Components/Label.vue'
import axios from "axios";
import dayjs from 'dayjs';

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    selectedRowData: {
        type: Object,
        default: () => ({}),
    },
})

const categories = ref([
    'Lead Details', 'Data & Appointment', 'Communication & Assignment', 'Contact Info', 'Remarks & System'
])

const leadFrontCategories = ref([
    'Basic Info', 'Financial Details', 'Additional Info', 'Timestamps'
])

const leadChangelogsCategories = ref([
    'All', 'Lead Notes', 'System'
])

const leadFrontData = reactive({});
const leadNotesData = reactive({});
const leadChangelogsData = reactive({});
const combinedLogsData = reactive([]);

const emit = defineEmits(['closeModal', 'rowEdit']);

const getLeadFront = async (id) => {
    try {
        const data = await axios.get(route('leads.getLeadFront', id));

        leadFrontData.value = data.data[0];
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}

const getLeadNotes = async (id) => {
    try {
        const data = await axios.get(route('leads.getLeadNotes', id));

        leadNotesData.value = data.data;
        
        leadNotesData.value.forEach((col, index) => {
            col.created_at = dayjs(col.created_at).format('DD MMMM YYYY, hh:mm A');
        });
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}

const getLeadChangelogs = async (id) => {
    try {
        const data = await axios.get(route('leads.getLeadChangelogs', id));

        leadChangelogsData.value = data.data;

        leadChangelogsData.value.forEach((col, index) => {
            col.created_at = dayjs(col.created_at).format('DD MMMM YYYY, hh:mm A');
        });
        // console.log(leadChangelogsData.value);
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}

onMounted(async () => {
  try {
    const leadFrontResponse = await axios.get(route('leads.getLeadFront', props.selectedRowData.id));
    leadFrontData.value = leadFrontResponse.data[0];

    const leadNotesResponse = await axios.get(route('leads.getLeadNotes', props.selectedRowData.id));
    leadNotesData.value = leadNotesResponse.data;

    const leadChangelogsResponse = await axios.get(route('leads.getLeadChangelogs', props.selectedRowData.id));
    leadChangelogsData.value = leadChangelogsResponse.data;

    let combinedLogsArray = [];

    for (let key in leadNotesData.value) {
        combinedLogsArray.push(leadNotesData.value[key]);
    }
    for (let key in leadChangelogsData.value) {
        combinedLogsArray.push(leadChangelogsData.value[key]);
    }

    // Sort combined data chronologically
    combinedLogsArray.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

    // Format the created_at dates
    combinedLogsArray.forEach(log => {
      log.created_at = dayjs(log.created_at).format('DD MMMM YYYY, hh:mm A');
    });

    // Convert sorted array back to object
    combinedLogsData.value = combinedLogsArray;

    
    // const combinedLogsResponse = await axios.get(route('leads.getLeadNotesAndChangelogs', props.selectedRowData.id));
    // combinedLogsData.value = combinedLogsResponse.data;

    // let combinedLogsArray = [];
    // for (let key in combinedLogsData.value) {
    //     combinedLogsArray.push(combinedLogsData.value[key]);
    // }
    // for (const key in combinedLogsData.value) {
    //   if (Object.prototype.hasOwnProperty.call(combinedLogsData.value, key)) {
    //     // data[key] = formatDatesRecursively(data[key]);
    //     combinedLogsData.value[key].created_at = dayjs(combinedLogsData.value[key].created_at).format('DD MMMM YYYY, hh:mm A');
    //     // cl(combinedLogsData.value[key].created_at);

    //   }
    // }

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

// onMounted(() => {
//     setTimeout(() => {
//         getLeadFront(props.selectedRowData.id);
//         combinedLogsData.value = [
//             getLeadNotes(props.selectedRowData.id),
//             getLeadChangelogs(props.selectedRowData.id),
//         ]
//     }, 200);
// });

</script>

<template>
    <div class="p-6">
        <div class="p-3 rounded-lg grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
            <div class="col-span-2 flex flex-col gap-6 mb-8 lg:mb-0">
                <div class="input-group flex flex-wrap justify-between">
                    <p class="dark:text-gray-300 font-semibold text-3xl pb-2">
                        {{ props.selectedRowData.first_name ? props.selectedRowData.first_name : '' }} {{ props.selectedRowData.last_name ? props.selectedRowData.last_name : '' }}
                        <span class="text-xs font-thin pl-4">
                            ( {{ props.selectedRowData.id ? props.selectedRowData.id : '' }} )
                        </span>
                    </p>
                    <div class="flex flex-col max-w-max gap-5 bg-gray-100 dark:bg-gray-900/60 p-3 rounded-lg">
                        <div class="flex flex-row gap-4">
                            <p class="dark:text-gray-400 text-sm pr-2">
                                <span class="text-xs font-thin">
                                    <PhoneIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ props.selectedRowData.phone_number ? props.selectedRowData.phone_number : '' }}
                            </p>
                            <span class="text-gray-800 dark:text-gray-500 text-sm leading-tight">•</span>
                            <p class="dark:text-gray-400 text-sm px-2">
                                <span class="text-xs font-thin">
                                    <AtSymbolIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ props.selectedRowData.email ? props.selectedRowData.email : '' }}
                            </p>
                            <span class="text-gray-800 dark:text-gray-500 text-sm leading-tight">•</span>
                            <p class="dark:text-gray-400 text-sm pr-2">
                                <span class="text-xs font-thin">
                                    <CalendarIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ props.selectedRowData.date_of_birth ? props.selectedRowData.date_of_birth : '' }}
                            </p>
                        </div>
                        <div class="flex flex-row gap-4">
                            <p class="dark:text-gray-400 text-sm pr-2">
                                <span class="text-xs font-thin">
                                    <FlagIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ props.selectedRowData.country ? props.selectedRowData.country : '' }}
                            </p>
                            <span class="text-gray-800 dark:text-gray-500 text-sm leading-tight">•</span>
                            <p class="dark:text-gray-400 text-sm px-2">
                                <span class="text-xs font-thin">
                                    <MapIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ props.selectedRowData.address ? props.selectedRowData.address : '' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <p class="dark:text-gray-300 font-semibold text-2xl pb-2">Lead Details</p>
                    <div class="w-full p-2 sm:px-0">
                        <TabGroup>
                            <TabList class="flex space-x-1 rounded-xl bg-blue-600/30 p-1 flex-wrap md:flex-nowrap">
                                <Tab
                                    v-for="category in categories"
                                    as="template"
                                    :key="category"
                                    v-slot="{ selected }"
                                >
                                    <button
                                        :class="[
                                            'w-full rounded-lg py-2.5 text-xs font-medium leading-5',
                                            'ring-white/60 ring-offset-2 ring-offset-purple-400 focus:outline-none focus:ring-2',
                                            selected
                                                ? 'bg-white text-blue-800 shadow'
                                                : 'text-gray-300 hover:bg-white/[0.12] hover:text-white',
                                        ]"
                                    >
                                        {{ category }}
                                    </button>
                                </Tab>
                            </TabList>

                            <TabPanels class="mt-2">
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="grid grid-cols-1 sm:grid-cols-3">
                                        <CustomLabelGroup
                                            :inputId="'date_oppd_in'"
                                            :labelValue="'Date Opp&rsquo;d In:'"
                                            :dataValue="props.selectedRowData.date_oppd_in ? props.selectedRowData.date_oppd_in : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'campaign_product'"
                                            :labelValue="'Campaign Product:'"
                                            :dataValue="props.selectedRowData.campaign_product ? props.selectedRowData.campaign_product : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'sdm'"
                                            :labelValue="'Sdm:'"
                                            :dataValue="props.selectedRowData.sdm ? props.selectedRowData.sdm : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'vc'"
                                            :labelValue="'VC:'"
                                            :dataValue="props.selectedRowData.vc ? props.selectedRowData.vc : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'occupation'"
                                            :labelValue="'Occupation:'"
                                            :dataValue="props.selectedRowData.occupation ? props.selectedRowData.occupation : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'agents_book'"
                                            :labelValue="'Agents Book:'"
                                            :dataValue="props.selectedRowData.agents_book ? props.selectedRowData.agents_book : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'account_manager'"
                                            :labelValue="'Account Manager:'"
                                            :dataValue="props.selectedRowData.account_manager ? props.selectedRowData.account_manager : '-'"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="grid grid-cols-1 sm:grid-cols-3">
                                        <CustomLabelGroup
                                            :inputId="'data_source'"
                                            :labelValue="'Data Source:'"
                                            :dataValue="props.selectedRowData.data_source ? props.selectedRowData.data_source : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'data_code'"
                                            :labelValue="'Data Code:'"
                                            :dataValue="props.selectedRowData.data_code ? props.selectedRowData.data_code : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'data_type'"
                                            :labelValue="'Data Type:'"
                                            :dataValue="props.selectedRowData.data_type ? props.selectedRowData.data_type : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'appointment_label'"
                                            :labelValue="'Appointment Label:'"
                                            :dataValue="props.selectedRowData.appointment_label ? props.selectedRowData.appointment_label : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'appointment_start_at'"
                                            :labelValue="'Appointment Start:'"
                                            :dataValue="props.selectedRowData.appointment_start_at ? props.selectedRowData.appointment_start_at : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'appointment_end_at'"
                                            :labelValue="'Appointment End:'"
                                            :dataValue="props.selectedRowData.appointment_end_at ? props.selectedRowData.appointment_end_at : '-'"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="grid grid-cols-1 sm:grid-cols-3">
                                        <CustomLabelGroup
                                            :inputId="'last_called'"
                                            :labelValue="'Last Called:'"
                                            :dataValue="props.selectedRowData.last_called ? props.selectedRowData.last_called : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'contact_outcome'"
                                            :labelValue="'Contact Outcome:'"
                                            :dataValue="props.selectedRowData.contact_outcome ? props.selectedRowData.contact_outcome : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'give_up_at'"
                                            :labelValue="'Give Up?:'"
                                            :dataValue="props.selectedRowData.give_up_at ? props.selectedRowData.give_up_at : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'assignee'"
                                            :labelValue="'Assignee:'"
                                            :dataValue="props.selectedRowData.assignee ? props.selectedRowData.assignee : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'assignee_read_at'"
                                            :labelValue="'Assignee Read At:'"
                                            :dataValue="props.selectedRowData.assignee_read_at ? props.selectedRowData.assignee_read_at : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'stage'"
                                            :labelValue="'Stage:'"
                                            :dataValue="props.selectedRowData.stage ? props.selectedRowData.stage : '-'"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="grid grid-cols-1 sm:grid-cols-3">
                                        <CustomLabelGroup
                                            :inputId="'phone_number_alt_1'"
                                            :labelValue="'Alt Phone Number 1:'"
                                            :dataValue="props.selectedRowData.phone_number_alt_1 ? props.selectedRowData.phone_number_alt_1 : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'phone_number_alt_2'"
                                            :labelValue="'Alt Phone Number 2:'"
                                            :dataValue="props.selectedRowData.phone_number_alt_2 ? props.selectedRowData.phone_number_alt_2 : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'phone_number_alt_3'"
                                            :labelValue="'Alt Phone Number 3:'"
                                            :dataValue="props.selectedRowData.phone_number_alt_3 ? props.selectedRowData.phone_number_alt_3 : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'email_alt_1'"
                                            :labelValue="'Alt Email 1:'"
                                            :dataValue="props.selectedRowData.email_alt_1 ? props.selectedRowData.email_alt_1 : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'email_alt_2'"
                                            :labelValue="'Alt Email 2:'"
                                            :dataValue="props.selectedRowData.email_alt_2 ? props.selectedRowData.email_alt_2 : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'email_alt_3'"
                                            :labelValue="'Alt Email 3:'"
                                            :dataValue="props.selectedRowData.email_alt_3 ? props.selectedRowData.email_alt_3 : '-'"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="grid grid-cols-1 sm:grid-cols-3">
                                        <CustomLabelGroup
                                            :inputId="'private_remark'"
                                            :labelValue="'Private Remark'"
                                            :dataValue="props.selectedRowData.private_remark ? props.selectedRowData.private_remark : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'remark'"
                                            :labelValue="'Remark'"
                                            :dataValue="props.selectedRowData.remark ? props.selectedRowData.remark : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'created_by'"
                                            :labelValue="'Created By'"
                                            :dataValue="props.selectedRowData.created_by ? props.selectedRowData.created_by : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'delete_at'"
                                            :labelValue="'Delete At'"
                                            :dataValue="props.selectedRowData.delete_at ? props.selectedRowData.delete_at : '-'"
                                        />
                                    </div>
                                </TabPanel>
                            </TabPanels>
                        </TabGroup>
                    </div>
                </div>
                <div class="input-group">
                    <p class="dark:text-gray-300 font-semibold text-2xl pb-2">Lead Front Details</p>
                    <div class="w-full p-2 sm:px-0">
                        <TabGroup>
                            <TabList class="flex space-x-1 rounded-xl bg-blue-600/30 p-1 flex-wrap md:flex-nowrap">
                                <Tab
                                    v-for="category in leadFrontCategories"
                                    as="template"
                                    :key="category"
                                    v-slot="{ selected }"
                                >
                                    <button
                                        :class="[
                                            'w-full rounded-lg py-2.5 text-xs font-medium leading-5',
                                            'ring-white/60 ring-offset-2 ring-offset-purple-400 focus:outline-none focus:ring-2',
                                            selected
                                                ? 'bg-white text-blue-800 shadow'
                                                : 'text-gray-300 hover:bg-white/[0.12] hover:text-white',
                                        ]"
                                    >
                                        {{ category }}
                                    </button>
                                </Tab>
                            </TabList>

                            <TabPanels class="mt-2" v-if="Object.keys(leadFrontData).length > 0">
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="grid grid-cols-1 sm:grid-cols-3">
                                        <CustomLabelGroup
                                            :inputId="'leadFrontName'"
                                            :labelValue="'Name'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.name : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontVc'"
                                            :labelValue="'VC'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.vc : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontMimo'"
                                            :labelValue="'MIMO'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.mimo : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontProduct'"
                                            :labelValue="'Product'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.product : '-'"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="grid grid-cols-1 sm:grid-cols-3">
                                        <CustomLabelGroup
                                            :inputId="'leadFrontQuantity'"
                                            :labelValue="'Amount of Shares'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.quantity : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontPrice'"
                                            :labelValue="'Price per Share'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.price : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontSdm'"
                                            :labelValue="'Sdm'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.sdm : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontLiquid'"
                                            :labelValue="'Liquid'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.liquid : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontBankName'"
                                            :labelValue="'Bank Name'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.bank_name : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontBankAccount'"
                                            :labelValue="'Bank Account'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.bank_account : '-'"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="grid grid-cols-1 sm:grid-cols-3">
                                        <CustomLabelGroup
                                            :inputId="'leadFrontNote'"
                                            :labelValue="'Special Note'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.note : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontCommission'"
                                            :labelValue="'Agent Commission %'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.commission : '-'"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="grid grid-cols-1 sm:grid-cols-3">
                                        <CustomLabelGroup
                                            :inputId="'leadFrontEditedAt'"
                                            :labelValue="'Edited at'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.edited_at : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontCreatedAt'"
                                            :labelValue="'Created at'"
                                            :dataValue="leadFrontData.value ? leadFrontData.value.created_at : '-'"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="grid grid-cols-1 sm:grid-cols-3">
                                        <CustomLabelGroup
                                            :inputId="'private_remark'"
                                            :labelValue="'Private Remark:'"
                                            :dataValue="props.selectedRowData.private_remark ? props.selectedRowData.private_remark : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'remark'"
                                            :labelValue="'Remark:'"
                                            :dataValue="props.selectedRowData.remark ? props.selectedRowData.remark : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'created_by'"
                                            :labelValue="'Created By:'"
                                            :dataValue="props.selectedRowData.created_by ? props.selectedRowData.created_by : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'delete_at'"
                                            :labelValue="'Delete At:'"
                                            :dataValue="props.selectedRowData.delete_at ? props.selectedRowData.delete_at : '-'"
                                        />
                                    </div>
                                </TabPanel>
                            </TabPanels>
                            <TabPanels class="mt-2" v-else>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="p-2 sm:p-4 sm:h-46 rounded-2xl shadow-lg gap-5 select-none ">
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:p-2">
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl col-span-2" ></div>
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl" ></div>
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl" ></div>
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl col-span-2" ></div>
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl col-span-2" ></div>
                                            <div class="bg-gray-200 w-full animate-pulse h-8 rounded-2xl" ></div>
                                        </div>
                                    </div>
                                </TabPanel>
                            </TabPanels>
                        </TabGroup>
                    </div>
                </div>
            </div>
            <div class="col-span-1">
                <div class="input-group p-8 rounded-xl">
                    <p class="dark:text-gray-300 font-semibold text-2xl pb-2">Lead Notes Details</p>
                    <div class="container">
                        <TabGroup>
                            <TabList class="flex space-x-1 rounded-xl bg-blue-600/30 p-1 flex-wrap md:flex-nowrap">
                                <Tab
                                    v-for="category in leadChangelogsCategories"
                                    as="template"
                                    :key="category"
                                    v-slot="{ selected }"
                                >
                                    <button
                                        :class="[
                                            'w-full rounded-lg py-2.5 text-xs font-medium leading-5',
                                            'ring-white/60 ring-offset-2 ring-offset-purple-400 focus:outline-none focus:ring-2',
                                            selected
                                                ? 'bg-white text-blue-800 shadow'
                                                : 'text-gray-300 hover:bg-white/[0.12] hover:text-white',
                                        ]"
                                    >
                                        {{ category }}
                                    </button>
                                </Tab>
                            </TabList>
                            <TabPanels class="mt-1">
                                <TabPanel
                                    :class="[
                                        'rounded-xl pt-4 pr-2 hidden-scrollable max-h-[900px]',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <!-- For Lead Notes & Changelogs -->
                                    <div v-for="(log, index) in combinedLogsData.value" :key="index">
                                        <div class="flex flex-col md:grid grid-cols-12">
                                            <div class="flex md:contents">
                                                <div class="col-start-1 col-end-2 mr-10 md:mx-auto relative">
                                                    <div class="h-full w-3 flex items-center justify-center">
                                                        <div class="h-full w-[2px] bg-purple-200/25 pointer-events-none"></div>
                                                    </div>
                                                    <div class="w-3 h-3 absolute top-0 rounded-full bg-purple-300 shadow text-center ring ring-purple-200/25"></div>
                                                </div>
                                                <div class="bg-gray-700 dark:bg-gray-700/70 col-start-2 col-end-13 p-4 rounded-xl mb-4 mr-auto shadow-md w-full">
                                                    <div v-if="log.source === 'lead_note'">
                                                        <h3 class="font-semibold text-gray-200">
                                                            {{ log.note || '' }}
                                                        </h3>
                                                        <span class="text-xs text-gray-400">
                                                            {{ log.created_at }}
                                                        </span>
                                                        <Disclosure v-slot="{ open }">
                                                            <DisclosureButton 
                                                                class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                                            >
                                                                Lead Note Details 
                                                                <span class="text-xs font-thin pl-4">
                                                                    ( {{ log.id }} )
                                                                </span>
                                                                <span
                                                                    v-show="open || !open"
                                                                    aria-hidden="true"
                                                                    class="relative block w-6 h-6 ml-auto"
                                                                >
                                                                    <span
                                                                        :class="[
                                                                            'absolute right-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                            {
                                                                                '-rotate-45': open,
                                                                                'rotate-45': !open,
                                                                                'bg-white': open,
                                                                                'bg-gray-400': !open,
                                                                            },
                                                                        ]"
                                                                    ></span>
                                                                    <span
                                                                        :class="[
                                                                            'absolute left-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                            {
                                                                                'rotate-45': open,
                                                                                '-rotate-45': !open,
                                                                                'bg-white': open,
                                                                                'bg-gray-400': !open,
                                                                            },
                                                                        ]"
                                                                    ></span>
                                                                </span>
                                                            </DisclosureButton>
                                                            <TransitionRoot
                                                                enter="transition-all ease-in duration-500"
                                                                enter-from="transform opacity-0 scaleY-95"
                                                                enter-to="transform opacity-100 scaleY-100"
                                                                leave="transition-all ease-out duration-200"
                                                                leave-from="transform opacity-100 scaleY-100"
                                                                leave-to="transform opacity-0 scaleY-95"
                                                            >
                                                                <DisclosurePanel 
                                                                    class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3"
                                                                >
                                                                    <Label
                                                                        :inputId="'leadNotesNote'+index"
                                                                        :labelValue="'Lead Notes'"
                                                                        class="font-semibold !text-gray-200/75"
                                                                    >
                                                                        Note: {{ log.note || '-' }}
                                                                    </Label>
                                                                    <Label
                                                                        :inputId="'leadNotesUserEditable'+index"
                                                                        :labelValue="'Lead Notes'"
                                                                        class="leading-relaxed font-semibold !text-gray-200/75"
                                                                    >
                                                                        User editable: 
                                                                        <span class="inline-block align-middle">
                                                                            <component 
                                                                                :is="log.user_editable ? CheckCircleIcon : ErrorCircleIcon" 
                                                                                class="w-4 h-4 ring rounded-full ml-2"
                                                                                :class="{
                                                                                    'ring-green-700': log.user_editable,
                                                                                    'ring-red-500 ': !log.user_editable,
                                                                                }"
                                                                            />
                                                                        </span>
                                                                    </Label>
                                                                    <Label
                                                                        :inputId="'leadNotesCreatedBy'+index"
                                                                        :labelValue="'Lead Notes'"
                                                                        class="font-semibold !text-gray-200/75"
                                                                    >
                                                                        Created by: <span class="inline-block bg-cyan-800 rounded-xl py-1 px-3">{{ log.created_by || '-' }}</span>
                                                                    </Label>
                                                                </DisclosurePanel>
                                                            </TransitionRoot>
                                                        </Disclosure>
                                                    </div>
                                                    <div v-else-if="log.source === 'lead_changelog'">
                                                        <h3 class="font-semibold text-gray-200">
                                                            [System]: {{ log.description }}
                                                        </h3>
                                                        <span class="text-xs text-gray-400">
                                                            {{ log.created_at }}
                                                        </span>
                                                        <div v-if="log.lead_changes && Object.keys(log.lead_changes).length > 0">
                                                            <Disclosure v-slot="{ open }">
                                                                <DisclosureButton 
                                                                    class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                                                >
                                                                    Lead Changelogs 
                                                                    <span class="text-xs font-thin pl-4">
                                                                        ( {{ log.id }} )
                                                                    </span>
                                                                    <span
                                                                        v-show="open || !open"
                                                                        aria-hidden="true"
                                                                        class="relative block w-6 h-6 ml-auto"
                                                                    >
                                                                        <span
                                                                            :class="[
                                                                                'absolute right-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                                {
                                                                                    '-rotate-45': open,
                                                                                    'rotate-45': !open,
                                                                                    'bg-white': open,
                                                                                    'bg-gray-400': !open,
                                                                                },
                                                                            ]"
                                                                        ></span>
                                                                        <span
                                                                            :class="[
                                                                                'absolute left-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                                {
                                                                                    'rotate-45': open,
                                                                                    '-rotate-45': !open,
                                                                                    'bg-white': open,
                                                                                    'bg-gray-400': !open,
                                                                                },
                                                                            ]"
                                                                        ></span>
                                                                    </span>
                                                                </DisclosureButton>
                                                                <TransitionRoot
                                                                    enter="transition-all ease-in duration-500"
                                                                    enter-from="transform opacity-0 scaleY-95"
                                                                    enter-to="transform opacity-100 scaleY-100"
                                                                    leave="transition-all ease-out duration-200"
                                                                    leave-from="transform opacity-100 scaleY-100"
                                                                    leave-to="transform opacity-0 scaleY-95"
                                                                >
                                                                    <DisclosurePanel 
                                                                        class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3 max-h-52 overflow-auto"
                                                                    >
                                                                        <div v-for="(value, ix) in log.lead_changes" :key="ix">
                                                                            <Label
                                                                                :inputId="'leadChangelogColumn'+ix"
                                                                                :labelValue="'Lead Notes'"
                                                                                class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                                            >
                                                                                ◉ [{{ convertToHumanReadable(ix) }}]
                                                                            </Label>
                                                                            <Label
                                                                                :inputId="'leadChangelog'+ix"
                                                                                :labelValue="'Lead Notes'"
                                                                                class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                            >
                                                                                ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : "set" }}" to "{{ value.new }}".
                                                                            </Label>
                                                                        </div>
                                                                    </DisclosurePanel>
                                                                </TransitionRoot>
                                                            </Disclosure>
                                                        </div>
                                                        <div v-if="log.lead_front_changes && Object.keys(log.lead_front_changes).length > 0">
                                                            <Disclosure v-slot="{ open }">
                                                                <DisclosureButton 
                                                                    class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                                                >
                                                                    Lead Front Changelogs 
                                                                    <span class="text-xs font-thin pl-4">
                                                                        ( {{ log.id }} )
                                                                    </span>
                                                                    <span
                                                                        v-show="open || !open"
                                                                        aria-hidden="true"
                                                                        class="relative block w-6 h-6 ml-auto"
                                                                    >
                                                                        <span
                                                                            :class="[
                                                                                'absolute right-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                                {
                                                                                    '-rotate-45': open,
                                                                                    'rotate-45': !open,
                                                                                    'bg-white': open,
                                                                                    'bg-gray-400': !open,
                                                                                },
                                                                            ]"
                                                                        ></span>
                                                                        <span
                                                                            :class="[
                                                                                'absolute left-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                                {
                                                                                    'rotate-45': open,
                                                                                    '-rotate-45': !open,
                                                                                    'bg-white': open,
                                                                                    'bg-gray-400': !open,
                                                                                },
                                                                            ]"
                                                                        ></span>
                                                                    </span>
                                                                </DisclosureButton>
                                                                <TransitionRoot
                                                                    enter="transition-all ease-in duration-500"
                                                                    enter-from="transform opacity-0 scaleY-95"
                                                                    enter-to="transform opacity-100 scaleY-100"
                                                                    leave="transition-all ease-out duration-200"
                                                                    leave-from="transform opacity-100 scaleY-100"
                                                                    leave-to="transform opacity-0 scaleY-95"
                                                                >
                                                                    <DisclosurePanel 
                                                                        class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3 max-h-52 overflow-auto"
                                                                    >
                                                                        <div v-for="(value, ix) in log.lead_front_changes" :key="ix">
                                                                            <Label
                                                                                :inputId="'leadChangelogColumn'+ix"
                                                                                :labelValue="'Lead Notes'"
                                                                                class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                                            >
                                                                                ◉ [ Lead Front ID: {{ value.id }} ]: [{{ (ix === 'New') ? 'New Lead Front' : convertToHumanReadable(ix) }}] 
                                                                            </Label>
                                                                            <Label
                                                                                :inputId="'leadChangelog'+ix"
                                                                                :labelValue="'Lead Notes'"
                                                                                class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                                v-if="ix !== 'New'"
                                                                            >
                                                                                ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : 'set' }}" to "{{ value.new }}".
                                                                            </Label>
                                                                            <Label
                                                                                :inputId="'leadChangelog'+ix"
                                                                                :labelValue="'Lead Notes'"
                                                                                class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                                v-else
                                                                            >
                                                                                ➤ {{ value.description }}.
                                                                            </Label>
                                                                        </div>
                                                                    </DisclosurePanel>
                                                                </TransitionRoot>
                                                            </Disclosure>
                                                        </div>
                                                        <div v-if="log.lead_notes_changes && Object.keys(log.lead_notes_changes).length > 0">
                                                            <Disclosure v-slot="{ open }">
                                                                <DisclosureButton 
                                                                    class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                                                >
                                                                    Lead Note Changelogs 
                                                                    <span class="text-xs font-thin pl-4">
                                                                        ( {{ log.id }} )
                                                                    </span>
                                                                    <span
                                                                        v-show="open || !open"
                                                                        aria-hidden="true"
                                                                        class="relative block w-6 h-6 ml-auto"
                                                                    >
                                                                        <span
                                                                            :class="[
                                                                                'absolute right-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                                {
                                                                                    '-rotate-45': open,
                                                                                    'rotate-45': !open,
                                                                                    'bg-white': open,
                                                                                    'bg-gray-400': !open,
                                                                                },
                                                                            ]"
                                                                        ></span>
                                                                        <span
                                                                            :class="[
                                                                                'absolute left-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                                {
                                                                                    'rotate-45': open,
                                                                                    '-rotate-45': !open,
                                                                                    'bg-white': open,
                                                                                    'bg-gray-400': !open,
                                                                                },
                                                                            ]"
                                                                        ></span>
                                                                    </span>
                                                                </DisclosureButton>
                                                                <TransitionRoot
                                                                    enter="transition-all ease-in duration-500"
                                                                    enter-from="transform opacity-0 scaleY-95"
                                                                    enter-to="transform opacity-100 scaleY-100"
                                                                    leave="transition-all ease-out duration-200"
                                                                    leave-from="transform opacity-100 scaleY-100"
                                                                    leave-to="transform opacity-0 scaleY-95"
                                                                >
                                                                    <DisclosurePanel 
                                                                        class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3 max-h-52 overflow-auto"
                                                                    >
                                                                        <div v-for="leadNotesChanges in log.lead_notes_changes">
                                                                            <div v-for="(value, ix) in leadNotesChanges" :key="ix">
                                                                                <!-- {{ cl(leadNotesChanges) }} -->
                                                                                <Label
                                                                                    :inputId="'leadChangelogColumn'+ix"
                                                                                    :labelValue="'Lead Notes'"
                                                                                    class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                                                >
                                                                                    ◉ [ Lead Note ID: {{ value.id }} ]: [ {{ (ix === 'New') ? 'New Lead Note' : convertToHumanReadable(ix) }} ]
                                                                                </Label>
                                                                                <Label
                                                                                    :inputId="'leadChangelog'+ix"
                                                                                    :labelValue="'Lead Notes'"
                                                                                    class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                                    v-if="ix !== 'New'"
                                                                                >
                                                                                    ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : 'set' }}" to "{{ value.new }}".
                                                                                </Label>
                                                                                <Label
                                                                                    :inputId="'leadChangelog'+ix"
                                                                                    :labelValue="'Lead Notes'"
                                                                                    class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                                    v-else
                                                                                >
                                                                                    ➤ {{ value.description }}.
                                                                                </Label>
                                                                            </div>
                                                                        </div>
                                                                    </DisclosurePanel>
                                                                </TransitionRoot>
                                                            </Disclosure>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl pt-4 pr-2',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <!-- For Lead Notes Only -->
                                    <div v-for="(log, index) in leadNotesData.value" :key="index">
                                        <div class="flex flex-col md:grid grid-cols-12">
                                            <div class="flex md:contents">
                                                <div class="col-start-1 col-end-2 mr-10 md:mx-auto relative">
                                                    <div class="h-full w-3 flex items-center justify-center">
                                                        <div class="h-full w-[2px] bg-purple-200/25 pointer-events-none"></div>
                                                    </div>
                                                    <div class="w-3 h-3 absolute top-0 rounded-full bg-purple-300 shadow text-center ring ring-purple-200/25"></div>
                                                </div>
                                                <div class="bg-gray-700 dark:bg-gray-700/70 col-start-2 col-end-13 p-4 rounded-xl mb-4 mr-auto shadow-md w-full">
                                                    <h3 class="font-semibold text-gray-200">
                                                        {{ log.note || '' }}
                                                    </h3>
                                                    <span class="text-xs text-gray-400">
                                                        {{ log.created_at }}
                                                    </span>
                                                    <Disclosure v-slot="{ open }">
                                                        <DisclosureButton 
                                                            class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                                        >
                                                            Lead Note Details 
                                                            <span class="text-xs font-thin pl-4">
                                                                ( {{ log.id }} )
                                                            </span>
                                                            <span
                                                                v-show="open || !open"
                                                                aria-hidden="true"
                                                                class="relative block w-6 h-6 ml-auto"
                                                            >
                                                                <span
                                                                    :class="[
                                                                        'absolute right-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                        {
                                                                            '-rotate-45': open,
                                                                            'rotate-45': !open,
                                                                            'bg-white': open,
                                                                            'bg-gray-400': !open,
                                                                        },
                                                                    ]"
                                                                ></span>
                                                                <span
                                                                    :class="[
                                                                        'absolute left-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                        {
                                                                            'rotate-45': open,
                                                                            '-rotate-45': !open,
                                                                            'bg-white': open,
                                                                            'bg-gray-400': !open,
                                                                        },
                                                                    ]"
                                                                ></span>
                                                            </span>
                                                        </DisclosureButton>
                                                        <TransitionRoot
                                                            enter="transition-all ease-in duration-500"
                                                            enter-from="transform opacity-0 scaleY-95"
                                                            enter-to="transform opacity-100 scaleY-100"
                                                            leave="transition-all ease-out duration-200"
                                                            leave-from="transform opacity-100 scaleY-100"
                                                            leave-to="transform opacity-0 scaleY-95"
                                                        >
                                                            <DisclosurePanel 
                                                                class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3"
                                                            >
                                                                <Label
                                                                    :inputId="'leadNotesNote'+index"
                                                                    :labelValue="'Lead Notes'"
                                                                    class="font-semibold !text-gray-200/75"
                                                                >
                                                                    Note: {{ log.note || '-' }}
                                                                </Label>
                                                                <Label
                                                                    :inputId="'leadNotesUserEditable'+index"
                                                                    :labelValue="'Lead Notes'"
                                                                    class="leading-relaxed font-semibold !text-gray-200/75"
                                                                >
                                                                    User editable: 
                                                                    <span class="inline-block align-middle">
                                                                        <component 
                                                                            :is="log.user_editable ? CheckCircleIcon : ErrorCircleIcon" 
                                                                            class="w-4 h-4 ring rounded-full ml-2"
                                                                            :class="{
                                                                                'ring-green-700': log.user_editable,
                                                                                'ring-red-500 ': !log.user_editable,
                                                                            }"
                                                                        />
                                                                    </span>
                                                                </Label>
                                                                <Label
                                                                    :inputId="'leadNotesCreatedBy'+index"
                                                                    :labelValue="'Lead Notes'"
                                                                    class="font-semibold !text-gray-200/75"
                                                                >
                                                                    Created by: <span class="inline-block bg-cyan-800 rounded-xl py-1 px-3">{{ log.created_by || '-' }}</span>
                                                                </Label>
                                                            </DisclosurePanel>
                                                        </TransitionRoot>
                                                    </Disclosure>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl pt-4 pr-2',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <!-- For Lead Changelogs Only -->
                                    <div v-for="(log, index) in leadChangelogsData.value" :key="index">
                                        <div class="flex flex-col md:grid grid-cols-12">
                                            <div class="flex md:contents">
                                                <div class="col-start-1 col-end-2 mr-10 md:mx-auto relative">
                                                    <div class="h-full w-3 flex items-center justify-center">
                                                        <div class="h-full w-[2px] bg-purple-200/25 pointer-events-none"></div>
                                                    </div>
                                                    <div class="w-3 h-3 absolute top-0 rounded-full bg-purple-300 shadow text-center ring ring-purple-200/25"></div>
                                                </div>
                                                <div class="bg-gray-700 dark:bg-gray-700/70 col-start-2 col-end-13 p-4 rounded-xl mb-4 mr-auto shadow-md w-full">
                                                    <h3 class="font-semibold text-gray-200">
                                                        [System]: {{ log.description }}
                                                    </h3>
                                                    <span class="text-xs text-gray-400">
                                                        {{ log.created_at }}
                                                    </span>
                                                    <!-- {{ cl(Object.keys(log.lead_changes).length) }} -->
                                                    <div v-if="log.lead_changes && Object.keys(log.lead_changes).length > 0">
                                                        <Disclosure v-slot="{ open }">
                                                            <DisclosureButton 
                                                                class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                                            >
                                                                Lead Changelogs 
                                                                <span class="text-xs font-thin pl-4">
                                                                    ( {{ log.id }} )
                                                                </span>
                                                                <span
                                                                    v-show="open || !open"
                                                                    aria-hidden="true"
                                                                    class="relative block w-6 h-6 ml-auto"
                                                                >
                                                                    <span
                                                                        :class="[
                                                                            'absolute right-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                            {
                                                                                '-rotate-45': open,
                                                                                'rotate-45': !open,
                                                                                'bg-white': open,
                                                                                'bg-gray-400': !open,
                                                                            },
                                                                        ]"
                                                                    ></span>
                                                                    <span
                                                                        :class="[
                                                                            'absolute left-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                            {
                                                                                'rotate-45': open,
                                                                                '-rotate-45': !open,
                                                                                'bg-white': open,
                                                                                'bg-gray-400': !open,
                                                                            },
                                                                        ]"
                                                                    ></span>
                                                                </span>
                                                            </DisclosureButton>
                                                            <TransitionRoot
                                                                enter="transition-all ease-in duration-500"
                                                                enter-from="transform opacity-0 scaleY-95"
                                                                enter-to="transform opacity-100 scaleY-100"
                                                                leave="transition-all ease-out duration-200"
                                                                leave-from="transform opacity-100 scaleY-100"
                                                                leave-to="transform opacity-0 scaleY-95"
                                                            >
                                                                <DisclosurePanel 
                                                                    class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3 max-h-52 overflow-auto"
                                                                >
                                                                    <div v-for="(value, ix) in log.lead_changes" :key="ix">
                                                                        <Label
                                                                            :inputId="'leadChangelogColumn'+ix"
                                                                            :labelValue="'Lead Notes'"
                                                                            class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                                        >
                                                                            ◉ [{{ convertToHumanReadable(ix) }}]
                                                                        </Label>
                                                                        <Label
                                                                            :inputId="'leadChangelog'+ix"
                                                                            :labelValue="'Lead Notes'"
                                                                            class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                        >
                                                                            ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : "set" }}" to "{{ value.new }}".
                                                                        </Label>
                                                                    </div>
                                                                </DisclosurePanel>
                                                            </TransitionRoot>
                                                        </Disclosure>
                                                    </div>
                                                    <div v-if="log.lead_front_changes && Object.keys(log.lead_front_changes).length > 0">
                                                        <Disclosure v-slot="{ open }">
                                                            <DisclosureButton 
                                                                class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                                            >
                                                                Lead Front Changelogs 
                                                                <span class="text-xs font-thin pl-4">
                                                                    ( {{ log.id }} )
                                                                </span>
                                                                <span
                                                                    v-show="open || !open"
                                                                    aria-hidden="true"
                                                                    class="relative block w-6 h-6 ml-auto"
                                                                >
                                                                    <span
                                                                        :class="[
                                                                            'absolute right-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                            {
                                                                                '-rotate-45': open,
                                                                                'rotate-45': !open,
                                                                                'bg-white': open,
                                                                                'bg-gray-400': !open,
                                                                            },
                                                                        ]"
                                                                    ></span>
                                                                    <span
                                                                        :class="[
                                                                            'absolute left-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                            {
                                                                                'rotate-45': open,
                                                                                '-rotate-45': !open,
                                                                                'bg-white': open,
                                                                                'bg-gray-400': !open,
                                                                            },
                                                                        ]"
                                                                    ></span>
                                                                </span>
                                                            </DisclosureButton>
                                                            <TransitionRoot
                                                                enter="transition-all ease-in duration-500"
                                                                enter-from="transform opacity-0 scaleY-95"
                                                                enter-to="transform opacity-100 scaleY-100"
                                                                leave="transition-all ease-out duration-200"
                                                                leave-from="transform opacity-100 scaleY-100"
                                                                leave-to="transform opacity-0 scaleY-95"
                                                            >
                                                                <DisclosurePanel 
                                                                    class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3 max-h-52 overflow-auto"
                                                                >
                                                                    <div v-for="(value, ix) in log.lead_front_changes" :key="ix">
                                                                        <Label
                                                                            :inputId="'leadChangelogColumn'+ix"
                                                                            :labelValue="'Lead Notes'"
                                                                            class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                                        >
                                                                            ◉ [ Lead Front ID: {{ value.id }} ]: [{{ (ix === 'New') ? 'New Lead Front' : convertToHumanReadable(ix) }}] 
                                                                        </Label>
                                                                        <Label
                                                                            :inputId="'leadChangelog'+ix"
                                                                            :labelValue="'Lead Notes'"
                                                                            class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                            v-if="ix !== 'New'"
                                                                        >
                                                                            ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : 'set' }}" to "{{ value.new }}".
                                                                        </Label>
                                                                        <Label
                                                                            :inputId="'leadChangelog'+ix"
                                                                            :labelValue="'Lead Notes'"
                                                                            class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                            v-else
                                                                        >
                                                                            ➤ {{ value.description }}.
                                                                        </Label>
                                                                    </div>
                                                                </DisclosurePanel>
                                                            </TransitionRoot>
                                                        </Disclosure>
                                                    </div>
                                                    <div v-if="log.lead_notes_changes && Object.keys(log.lead_notes_changes).length > 0">
                                                        <Disclosure v-slot="{ open }">
                                                            <DisclosureButton 
                                                                class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                                            >
                                                                Lead Note Changelogs 
                                                                <span class="text-xs font-thin pl-4">
                                                                    ( {{ log.id }} )
                                                                </span>
                                                                <span
                                                                    v-show="open || !open"
                                                                    aria-hidden="true"
                                                                    class="relative block w-6 h-6 ml-auto"
                                                                >
                                                                    <span
                                                                        :class="[
                                                                            'absolute right-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                            {
                                                                                '-rotate-45': open,
                                                                                'rotate-45': !open,
                                                                                'bg-white': open,
                                                                                'bg-gray-400': !open,
                                                                            },
                                                                        ]"
                                                                    ></span>
                                                                    <span
                                                                        :class="[
                                                                            'absolute left-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                                            {
                                                                                'rotate-45': open,
                                                                                '-rotate-45': !open,
                                                                                'bg-white': open,
                                                                                'bg-gray-400': !open,
                                                                            },
                                                                        ]"
                                                                    ></span>
                                                                </span>
                                                            </DisclosureButton>
                                                            <TransitionRoot
                                                                enter="transition-all ease-in duration-500"
                                                                enter-from="transform opacity-0 scaleY-95"
                                                                enter-to="transform opacity-100 scaleY-100"
                                                                leave="transition-all ease-out duration-200"
                                                                leave-from="transform opacity-100 scaleY-100"
                                                                leave-to="transform opacity-0 scaleY-95"
                                                            >
                                                                <DisclosurePanel 
                                                                    class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3 max-h-52 overflow-auto"
                                                                >
                                                                    <div v-for="leadNotesChanges in log.lead_notes_changes">
                                                                        <div v-for="(value, ix) in leadNotesChanges" :key="ix">
                                                                            <!-- {{ cl(leadNotesChanges) }} -->
                                                                            <Label
                                                                                :inputId="'leadChangelogColumn'+ix"
                                                                                :labelValue="'Lead Notes'"
                                                                                class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                                            >
                                                                                ◉ [ Lead Note ID: {{ value.id }} ]: [ {{ (ix === 'New') ? 'New Lead Note' : convertToHumanReadable(ix) }} ]
                                                                            </Label>
                                                                            <Label
                                                                                :inputId="'leadChangelog'+ix"
                                                                                :labelValue="'Lead Notes'"
                                                                                class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                                v-if="ix !== 'New'"
                                                                            >
                                                                                ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : 'set' }}" to "{{ value.new }}".
                                                                            </Label>
                                                                            <Label
                                                                                :inputId="'leadChangelog'+ix"
                                                                                :labelValue="'Lead Notes'"
                                                                                class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                                v-else
                                                                            >
                                                                                ➤ {{ value.description }}.
                                                                            </Label>
                                                                        </div>
                                                                    </div>
                                                                </DisclosurePanel>
                                                            </TransitionRoot>
                                                        </Disclosure>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </TabPanel>
                            </TabPanels>
                        </TabGroup>
                        <!-- For Lead Notes
                        <div v-for="leadNote in leadNotesData">
                            <div class="flex flex-col md:grid grid-cols-12">
                                <div class="flex md:contents" v-for="(item, i) in leadNote" :key="i">
                                    <div class="col-start-1 col-end-2 mr-10 md:mx-auto relative">
                                        <div class="h-full w-3 flex items-center justify-center">
                                            <div class="h-full w-[2px] bg-purple-200/25 pointer-events-none"></div>
                                        </div>
                                        <div class="w-3 h-3 absolute top-0 rounded-full bg-purple-300 shadow text-center ring ring-purple-200/25"></div>
                                    </div>
                                    <div class="bg-gray-700 dark:bg-gray-700/70 col-start-2 col-end-13 p-4 rounded-xl mb-4 mr-auto shadow-md w-full">
                                        <div class="mb-3">
                                            <h3 class="font-semibold text-gray-200">
                                                You successfully created a new lead.
                                            </h3>
                                            <span class="text-xs text-gray-400">
                                                {{ leadNotesData.value ? item.created_at : '-' }}
                                            </span>
                                        </div>
                                        <Disclosure v-slot="{ open }">
                                            <DisclosureButton 
                                                class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 flex items-center w-full mb-1 text-sm font-bold"
                                            >
                                                Lead Note Details 
                                                <span class="text-xs font-thin pl-4">
                                                    ( {{ leadNotesData.value ? item.id : '' }} )
                                                </span>
                                                <span
                                                    v-show="open || !open"
                                                    aria-hidden="true"
                                                    class="relative block w-6 h-6 ml-auto"
                                                >
                                                    <span
                                                        :class="[
                                                            'absolute right-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                            {
                                                                '-rotate-45': open,
                                                                'rotate-45': !open,
                                                                'bg-white': open,
                                                                'bg-gray-400': !open,
                                                            },
                                                        ]"
                                                    ></span>
                                                    <span
                                                        :class="[
                                                            'absolute left-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                            {
                                                                'rotate-45': open,
                                                                '-rotate-45': !open,
                                                                'bg-white': open,
                                                                'bg-gray-400': !open,
                                                            },
                                                        ]"
                                                    ></span>
                                                </span>
                                            </DisclosureButton>
                                            <TransitionRoot
                                                enter="transition-all ease-in duration-500"
                                                enter-from="transform opacity-0 scaleY-95"
                                                enter-to="transform opacity-100 scaleY-100"
                                                leave="transition-all ease-out duration-200"
                                                leave-from="transform opacity-100 scaleY-100"
                                                leave-to="transform opacity-0 scaleY-95"
                                            >
                                                <DisclosurePanel 
                                                    class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3"
                                                >
                                                    <Label
                                                        :inputId="'leadNotesNote'+i"
                                                        :labelValue="'Lead Notes'"
                                                        class="font-semibold !text-gray-200/75"
                                                    >
                                                        Note: {{ leadNotesData.value ? item.note : '-' }}
                                                    </Label>
                                                    <Label
                                                        :inputId="'leadNotesUserEditable'+i"
                                                        :labelValue="'Lead Notes'"
                                                        class="leading-relaxed font-semibold !text-gray-200/75"
                                                    >
                                                        User editable: 
                                                        <span class="inline-block align-middle">
                                                            <component 
                                                                :is="item.user_editable ? CheckCircleIcon : ErrorCircleIcon" 
                                                                class="w-4 h-4 ring rounded-full ml-2"
                                                                :class="{
                                                                    'ring-green-700': item.user_editable,
                                                                    'ring-red-500 ': !item.user_editable,
                                                                }"
                                                            />
                                                        </span>
                                                    </Label>
                                                    <Label
                                                        :inputId="'leadNotesCreatedBy'+i"
                                                        :labelValue="'Lead Notes'"
                                                        class="font-semibold !text-gray-200/75"
                                                    >
                                                        Created by: <span class="inline-block bg-cyan-800 rounded-xl py-1 px-3">{{ leadNotesData.value ? item.created_by : '-' }}</span>
                                                    </Label>
                                                </DisclosurePanel>
                                            </TransitionRoot>
                                        </Disclosure>
                                    </div>
                                </div>
                            </div>
                        </div>

                        For Lead Changelogs - System Generated Messages
                        <div v-for="leadChangelog in leadChangelogsData">
                            <div class="flex flex-col md:grid grid-cols-12">
                                <div class="flex md:contents" v-for="(item, i) in leadChangelog" :key="i">
                                    <div class="col-start-1 col-end-2 mr-10 md:mx-auto relative">
                                        <div class="h-full w-3 flex items-center justify-center">
                                            <div class="h-full w-[2px] bg-purple-200/25 pointer-events-none"></div>
                                        </div>
                                        <div class="w-3 h-3 absolute top-0 rounded-full bg-purple-300 shadow text-center ring ring-purple-200/25"></div>
                                    </div>
                                    <div class="bg-gray-700 dark:bg-gray-700/70 col-start-2 col-end-13 p-4 rounded-xl mb-4 mr-auto shadow-md w-full">
                                        <div class="mb-3">
                                            <h3 class="font-semibold text-gray-200">
                                                [System]: {{ leadChangelogsData.value ? item.description : '-' }}
                                            </h3>
                                            <span class="text-xs text-gray-400">
                                                {{ leadChangelogsData.value ? item.created_at : '-' }}
                                            </span>
                                        </div>
                                        <Disclosure v-slot="{ open }">
                                            <DisclosureButton 
                                                class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 flex items-center w-full mb-1 text-sm font-bold"
                                            >
                                                Lead Note Changelogs
                                                <span class="text-xs font-thin pl-4">
                                                    ( {{ leadNotesData.value ? item.id : '' }} )
                                                </span>
                                                <span
                                                    v-show="open || !open"
                                                    aria-hidden="true"
                                                    class="relative block w-6 h-6 ml-auto"
                                                >
                                                    <span
                                                        :class="[
                                                            'absolute right-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                            {
                                                                '-rotate-45': open,
                                                                'rotate-45': !open,
                                                                'bg-white': open,
                                                                'bg-gray-400': !open,
                                                            },
                                                        ]"
                                                    ></span>
                                                    <span
                                                        :class="[
                                                            'absolute left-[9px] mt-[-5px] h-2 w-[2px] top-1/2 transition-all duration-200',
                                                            {
                                                                'rotate-45': open,
                                                                '-rotate-45': !open,
                                                                'bg-white': open,
                                                                'bg-gray-400': !open,
                                                            },
                                                        ]"
                                                    ></span>
                                                </span>
                                            </DisclosureButton>
                                            <TransitionRoot
                                                enter="transition-all ease-in duration-500"
                                                enter-from="transform opacity-0 scaleY-95"
                                                enter-to="transform opacity-100 scaleY-100"
                                                leave="transition-all ease-out duration-200"
                                                leave-from="transform opacity-100 scaleY-100"
                                                leave-to="transform opacity-0 scaleY-95"
                                            >
                                                <DisclosurePanel 
                                                    class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3"
                                                >
                                                <div class="flex flex-col" v-for="(value, ix) in item.lead_changes" :key="ix">
                                                    <CustomLabelGroup
                                                        :inputId="'leadChangelog'+ix"
                                                        :labelValue="'[' + convertToHumanReadable(ix) + ']'"
                                                        :dataValue="convertToHumanReadable(ix) + 'has been ' ((value.old !== null) ? 'changed from ' + value.old : 'set') + ' to ' + value.new + '.'"
                                                    />
                                                    <Label
                                                        :inputId="'leadChangelogColumn'+ix"
                                                        :labelValue="'Lead Notes'"
                                                        class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                    >
                                                        ◉ [{{ convertToHumanReadable(ix) }}]
                                                    </Label>
                                                    <Label
                                                        :inputId="'leadChangelog'+ix"
                                                        :labelValue="'Lead Notes'"
                                                        class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                    >
                                                        ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from ' + value.old : 'set' }} to {{ value.new }}.
                                                    </Label>
                                                </div>
                                                    <Label
                                                        :inputId="'leadNotesCreatedBy'+i"
                                                        :labelValue="'Lead Notes'"
                                                        class="font-semibold !text-gray-200/75"
                                                    >
                                                        Created by: <span class="inline-block bg-cyan-800 rounded-xl py-1 px-3">{{ leadNotesData.value ? item.created_by : '-' }}</span>
                                                    </Label>
                                                </DisclosurePanel>
                                            </TransitionRoot>
                                        </Disclosure>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end p-9">
            <div class="rounded-md shadow-lg flex flex-row gap-2">
                <div class="action-button-group">
                    <!-- <Button 
                        :type="'button'"
                        :variant="'info'" 
                        :size="'base'"
                        class="justify-center gap-2 px-6 py-2 form-actions"
                        @click="console.log(leadFrontData.value.name)"
                    >
                        <span>Output</span>
                    </Button> -->
                    <Button 
                        :type="'button'"
                        :variant="'info'" 
                        :size="'base'"
                        class="justify-center gap-2 px-6 py-2 form-actions"
                        @click="emit('closeModal')"
                    >
                        <span>Back</span>
                    </Button>
                    <Button 
                        :type="'button'"
                        :size="'base'" 
                        class="justify-center gap-2 px-6 py-2 form-actions"
                        @click="emit('rowEdit', props.selectedRowData)"
                    >
                        <span>Edit</span>
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
