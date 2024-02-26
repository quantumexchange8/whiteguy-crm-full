<script setup>
import { ref, onMounted, reactive } from 'vue'
import { cl, back, convertToHumanReadable } from '@/Composables'
import { AtSymbolIcon } from '@heroicons/vue/outline'
import { 
    TabGroup, TabList, Tab, TabPanels, TabPanel
} from '@headlessui/vue'
import { 
    PhoneIcon, MapIcon, FlagIcon, CalendarIcon 
} from '@heroicons/vue/solid'
import LeadFrontChangelogsSection from './LeadFrontChangelogsSection.vue'
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

const leadFrontCategories = ref([
    'Basic Info', 'Financial Details', 'Additional Info', 'Timestamps'
])

const leadData = reactive({});

const emit = defineEmits(['closeModal', 'rowEdit']);

onMounted(async () => {
  try {
    const leadResponse = await axios.get(route('lead-fronts.getLead', props.selectedRowData.linked_lead));
    // cl(leadResponse);
    leadData.value = leadResponse.data[0];

    props.selectedRowData.sdm = (props.selectedRowData.sdm) ? 'Yes' : 'No';
    props.selectedRowData.liquid = (props.selectedRowData.liquid) ? 'Yes' : 'No';


  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

</script>

<template>
    <div class="p-6">
        <div class="p-3 rounded-lg grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
            <div class="col-span-2 flex flex-col gap-6 mb-8 lg:mb-0">
                <div class="input-group flex flex-wrap justify-between">
                    <p class="dark:text-gray-300 font-semibold text-3xl pb-2">
                        {{ leadData.value ? leadData.value.first_name : '' }} {{ leadData.value ? leadData.value.last_name : '' }}
                        <span class="text-xs font-thin pl-4">
                            ( {{ leadData.value ? leadData.value.id : '' }} )
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
                                | {{ leadData.value ? leadData.value.phone_number : '' }}
                            </p>
                            <span class="text-gray-800 dark:text-gray-500 text-sm leading-tight">•</span>
                            <p class="dark:text-gray-400 text-sm px-2">
                                <span class="text-xs font-thin">
                                    <AtSymbolIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ leadData.value ? leadData.value.email : '' }}
                            </p>
                            <span class="text-gray-800 dark:text-gray-500 text-sm leading-tight">•</span>
                            <p class="dark:text-gray-400 text-sm pr-2">
                                <span class="text-xs font-thin">
                                    <CalendarIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ leadData.value ? leadData.value.date_of_birth : '' }}
                            </p>
                        </div>
                        <div class="flex flex-row gap-4">
                            <p class="dark:text-gray-400 text-sm pr-2">
                                <span class="text-xs font-thin">
                                    <FlagIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ leadData.value ? leadData.value.country : '' }}
                            </p>
                            <span class="text-gray-800 dark:text-gray-500 text-sm leading-tight">•</span>
                            <p class="dark:text-gray-400 text-sm px-2">
                                <span class="text-xs font-thin">
                                    <MapIcon
                                        class="h-4 w-4 text-purple-400 inline-block"
                                    />
                                </span>
                                | {{ leadData.value ? leadData.value.address : '' }}
                            </p>
                        </div>
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

                            <TabPanels class="mt-2" v-if="Object.keys(props.selectedRowData).length > 0">
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
                                            :dataValue="props.selectedRowData ? props.selectedRowData.name : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontVc'"
                                            :labelValue="'VC'"
                                            :dataValue="props.selectedRowData ? props.selectedRowData.vc : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontMimo'"
                                            :labelValue="'MIMO'"
                                            :dataValue="props.selectedRowData ? props.selectedRowData.mimo : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontProduct'"
                                            :labelValue="'Product'"
                                            :dataValue="props.selectedRowData ? props.selectedRowData.product : '-'"
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
                                            :dataValue="parseFloat(props.selectedRowData ? props.selectedRowData.quantity : 0).toFixed(2)"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontPrice'"
                                            :labelValue="'Price per Share'"
                                            :dataValue="parseFloat(props.selectedRowData ? props.selectedRowData.price : 0).toFixed(2)"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontSdm'"
                                            :labelValue="'Sdm'"
                                            :dataValue="props.selectedRowData ? props.selectedRowData.sdm : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontLiquid'"
                                            :labelValue="'Liquid'"
                                            :dataValue="props.selectedRowData ? props.selectedRowData.liquid : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontBankName'"
                                            :labelValue="'Bank Name'"
                                            :dataValue="props.selectedRowData ? props.selectedRowData.bank_name : '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontBankAccount'"
                                            :labelValue="'Bank Account'"
                                            :dataValue="props.selectedRowData ? props.selectedRowData.bank_account : '-'"
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
                                            :dataValue="props.selectedRowData ? props.selectedRowData.note : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontCommission'"
                                            :labelValue="'Agent Commission %'"
                                            :dataValue="parseFloat(props.selectedRowData ? props.selectedRowData.commission : 0).toFixed(2)"
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
                                            :dataValue="props.selectedRowData ? props.selectedRowData.edited_at : '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'leadFrontCreatedAt'"
                                            :labelValue="'Created at'"
                                            :dataValue="props.selectedRowData ? dayjs(props.selectedRowData.created_at).format('YYYY-MM-DD HH:mm:ss') : '-'"
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
                <LeadFrontChangelogsSection
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
