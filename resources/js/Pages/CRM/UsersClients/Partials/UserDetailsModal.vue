<script setup>
import { ref, onMounted, reactive } from 'vue'
import { cl, back, convertToHumanReadable, replaceHyphensWithEmpty } from '@/Composables'
import { AtSymbolIcon } from '@heroicons/vue/outline'
import { 
    TabGroup, TabList, Tab, TabPanels, TabPanel
} from '@headlessui/vue'
import { 
    PhoneIcon, MapIcon, FlagIcon, CalendarIcon 
} from '@heroicons/vue/solid'
import UserChangelogsSection from './UserChangelogsSection.vue'
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue'
import Button from '@/Components/Button.vue'
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
    'User Info', 'Account Info', 'KYC Status', 'Permissions', 'System'
])

const leadFrontCategories = ref([
    'Basic Info', 'Financial Details', 'Additional Info', 'Timestamps'
])

const leadFrontData = reactive({});

const emit = defineEmits(['closeModal', 'rowEdit']);

onMounted(() => {
    if (props.selectedRowData) {
        props.selectedRowData.is_active = (props.selectedRowData.is_active) ? 'Yes' : 'No';
        props.selectedRowData.has_crm_access = (props.selectedRowData.has_crm_access) ? 'Yes' : 'No';
        props.selectedRowData.has_leads_access = (props.selectedRowData.has_leads_access) ? 'Yes' : 'No';
        props.selectedRowData.is_staff = (props.selectedRowData.is_staff) ? 'Yes' : 'No';
        props.selectedRowData.is_superuser = (props.selectedRowData.is_superuser) ? 'Yes' : 'No';
    }

    // cl(props.selectedRowData);
    replaceHyphensWithEmpty(props.selectedRowData);
});

</script>

<template>
    <div class="p-6">
        <div class="p-3 rounded-lg grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
            <div class="col-span-2 flex flex-col gap-6 mb-8 lg:mb-0">
                <div class="input-group grid grid-cols-1 lg:grid-cols-12 lg:gap-4">
                    <p class="dark:text-gray-300 lg:col-span-6 font-semibold text-2xl pb-2">
                        {{ props.selectedRowData.full_legal_name ? props.selectedRowData.full_legal_name : '' }}
                        <span class="text-xs font-thin pl-4">
                            ( {{ props.selectedRowData.id ? props.selectedRowData.id : '' }} )
                        </span>
                    </p>
                    <div class="flex flex-col max-w-full lg:col-span-6 gap-5 bg-gray-100 dark:bg-gray-900/60 p-3 rounded-lg">
                        <div class="grid grid-cols-1 lg:grid-cols-12">
                            <p class="dark:text-gray-400 text-sm pr-2 lg:col-span-4">
                                <span class="text-xs font-thin">
                                    <PhoneIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ props.selectedRowData.phone_number ? props.selectedRowData.phone_number : '' }}
                            </p>
                            <span class="text-gray-800 dark:text-gray-500 text-sm leading-tight lg:col-span-1">•</span>
                            <p class="dark:text-gray-400 text-sm px-2 lg:col-span-7">
                                <span class="text-xs font-thin">
                                    <AtSymbolIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ props.selectedRowData.email ? props.selectedRowData.email : '' }}
                            </p>
                        </div>
                        <div class="flex">
                            <p class="dark:text-gray-400 text-sm pr-2">
                                <span class="text-xs font-thin">
                                    <FlagIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ props.selectedRowData.country_of_citizenship ? props.selectedRowData.country_of_citizenship : '' }}
                            </p>
                        </div>
                        <div class="flex">
                            <p class="dark:text-gray-400 text-sm">
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
                    <p class="dark:text-gray-300 font-semibold text-xl pb-2">User Details</p>
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
                                            :inputId="'site'"
                                            :labelValue="'Site:'"
                                            :dataValue="props.selectedRowData?.site ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'username'"
                                            :labelValue="'Username:'"
                                            :dataValue="props.selectedRowData?.username ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'account_id'"
                                            :labelValue="'Account ID:'"
                                            :dataValue="props.selectedRowData?.account_id ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'full_legal_name'"
                                            :labelValue="'Full Legal Name:'"
                                            :dataValue="props.selectedRowData?.full_legal_name ?? '-'"
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
                                            :inputId="'orders'"
                                            :labelValue="'Orders:'"
                                            :dataValue="'-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'application_form'"
                                            :labelValue="'Application Form:'"
                                            :dataValue="'-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'wallet_balance'"
                                            :labelValue="'Wallet Balance:'"
                                            :dataValue="'-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'account_holder'"
                                            :labelValue="'Account Holder Name:'"
                                            :dataValue="props.selectedRowData?.account_holder ?? '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'account_type'"
                                            :labelValue="'Account type:'"
                                            :dataValue="props.selectedRowData?.account_type ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'account_manager'"
                                            :labelValue="'Account manager:'"
                                            :dataValue="props.selectedRowData?.account_manager ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'rank'"
                                            :labelValue="'Rank:'"
                                            :dataValue="props.selectedRowData?.rank ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'customer_type'"
                                            :labelValue="'Customer type:'"
                                            :dataValue="props.selectedRowData?.customer_type ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'lead_status'"
                                            :labelValue="'Lead Status:'"
                                            :dataValue="props.selectedRowData?.lead_status ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'client_stage'"
                                            :labelValue="'Client stage:'"
                                            :dataValue="props.selectedRowData?.client_stage ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'previous_broker_name'"
                                            :labelValue="'Previous Broker Name:'"
                                            :dataValue="props.selectedRowData?.previous_broker_name ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'remark'"
                                            :labelValue="'Remark:'"
                                            :dataValue="props.selectedRowData?.remark ?? '-'"
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
                                            :inputId="'kyc_status'"
                                            :labelValue="'KYC Status:'"
                                            :dataValue="props.selectedRowData?.kyc_status ?? '-'"
                                            class="text-gray-700"
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
                                            :inputId="'is_active'"
                                            :labelValue="'Active:'"
                                            :dataValue="props.selectedRowData?.is_active ?? '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'has_crm_access'"
                                            :labelValue="'CRM Access:'"
                                            :dataValue="props.selectedRowData?.has_crm_access ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'has_leads_access'"
                                            :labelValue="'Lead Management Access:'"
                                            :dataValue="props.selectedRowData?.has_leads_access ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'is_staff'"
                                            :labelValue="'Staff status:'"
                                            :dataValue="props.selectedRowData?.is_staff ?? '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'is_superuser'"
                                            :labelValue="'Superuser status:'"
                                            :dataValue="props.selectedRowData?.is_superuser ?? '-'"
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
                                            :inputId="'last_login'"
                                            :labelValue="'Last login'"
                                            :dataValue="props.selectedRowData?.last_login ?? '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'date_joined'"
                                            :labelValue="'Date joined'"
                                            :dataValue="props.selectedRowData?.created_at ?? '-'"
                                        />
                                    </div>
                                </TabPanel>
                            </TabPanels>
                        </TabGroup>
                    </div>
                </div>
            </div>
            <div class="col-span-1">
                <UserChangelogsSection
                    :selectedRowData="props.selectedRowData"
                    class="w-full"
                />
            </div>
        </div>
        <div class="flex justify-end p-9">
            <div class="rounded-md shadow-lg flex flex-row gap-2">
                <div class="action-button-group">
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