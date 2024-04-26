<script setup>
import axios from "axios";
import dayjs from 'dayjs';
import { ref, onMounted, reactive, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3'
import { cl, back, convertToHumanReadable, replaceHyphensWithEmpty, formatToUserTimezone } from '@/Composables';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { AtSymbolIcon } from '@heroicons/vue/outline';
import { PhoneIcon, MapIcon, FlagIcon, CalendarIcon } from '@heroicons/vue/solid';
// import UserOrdersModal from './UserOrdersModal.vue';
// import UserChangelogsSection from './UserChangelogsSection.vue';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue';

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    selectedRowData: {
        type: Object,
        default: () => ({}),
    },
})

const categories = ref([
    'Sale Order Info', 'Client Details', 'Financial Details', 'Allocation & Support', 'Sale Order Items'
])

const emit = defineEmits(['closeModal', 'rowEdit']);

const page = usePage();
const user = computed(() => page.props.auth.user)
const userId = ref();
const isUserOrdersModalOpen = ref(false);
const saleOrderItemsData = reactive({});

onMounted(async () => {
    // const saleOrderItemsResponse = await axios.get(route('sale-orders.getSaleOrderItems', props.userId));
    // saleOrderItemsData.value = saleOrderItemsResponse.data;

    replaceHyphensWithEmpty(props.selectedRowData);
});

const openUserOrdersModal = () => {
    userId.value = props.selectedRowData.id;
    isUserOrdersModalOpen.value = true;
};

const closeUserOrdersModal = () => {
    isUserOrdersModalOpen.value = false;
    userId.value = null;
};
</script>

<template>
    <div class="p-6">
        <div class="p-3 rounded-lg grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
            <div class="col-span-2 flex flex-col gap-6 mb-8 lg:mb-0">
                <div class="input-group flex flex-col">
                    <p class="dark:text-gray-300 font-semibold text-xl pb-2">
                        Client Details
                        <!-- <span class="text-xs font-thin pl-4">
                            ( {{ props.selectedRowData?.id || '' }} )
                        </span> -->
                    </p>
                    <div class="grid grid-cols-1 lg:grid-cols-12 lg:gap-4">
                        <p class="dark:text-gray-300 lg:col-span-6 font-semibold text-2xl pb-2">
                            {{ props.selectedRowData?.registered_name || '' }}
                        </p>
                        <div class="flex flex-col max-w-full lg:col-span-6 gap-5 bg-gray-100 dark:bg-gray-900/60 p-3 rounded-lg">
                            <div class="grid grid-cols-1 lg:grid-cols-12">
                                <p class="dark:text-gray-400 text-sm pr-2 lg:col-span-4">
                                    <span class="text-xs font-thin">
                                        <PhoneIcon
                                            class="h-4 w-4 text-purple-400 inline-block"
                                        />
                                    </span>
                                    | {{ props.selectedRowData?.mobile_number || '' }}
                                </p>
                                <span class="text-gray-800 dark:text-gray-500 text-sm leading-tight lg:col-span-1">•</span>
                                <p class="dark:text-gray-400 text-sm px-2 lg:col-span-7">
                                    <span class="text-xs font-thin">
                                        <AtSymbolIcon
                                            class="h-4 w-4 text-purple-400 inline-block"
                                        />
                                    </span>
                                    | {{ props.selectedRowData?.email || '' }}
                                </p>
                            </div>
                            <div class="flex">
                                <p class="dark:text-gray-400 text-sm pr-2">
                                    <span class="text-xs font-thin">
                                        <FlagIcon
                                            class="h-4 w-4 text-purple-400 inline-block"
                                        />
                                    </span>
                                    | {{ props.selectedRowData?.country || '' }}
                                </p>
                            </div>
                            <div class="flex">
                                <p class="dark:text-gray-400 text-sm">
                                    <span class="text-xs font-thin">
                                        <MapIcon
                                            class="h-4 w-4 text-purple-400 inline-block"
                                        />
                                    </span>
                                    | {{ props.selectedRowData?.address_1 || '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <p class="dark:text-gray-300 font-semibold text-xl pb-2">Sale Order Details</p>
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
                                            :dataValue="props.selectedRowData?.site?.name || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'written_date'"
                                            :labelValue="'Written Date:'"
                                            :dataValue="props.selectedRowData?.written_date || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'public_id'"
                                            :labelValue="'Sale Order Public ID:'"
                                            :dataValue="props.selectedRowData?.public_id || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'created_by'"
                                            :labelValue="'Created by:'"
                                            :dataValue="props.selectedRowData?.created_by || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'edited_at'"
                                            :labelValue="'Edited at:'"
                                            :dataValue="props.selectedRowData?.username || '-'"
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
                                        <!-- <div class="input-wrapper">
                                            <Label
                                                :value="'Orders'"
                                                :for="'orders'"
                                                class="mb-2"
                                            >
                                            </Label>
                                            <div class="my-4 ml-6">
                                                <Button 
                                                    :type="'button'"
                                                    :size="'sm'"
                                                    class="self-center justify-center gap-2 w-full lg:w-3/5 text-sm" 
                                                    @click="openUserOrdersModal"
                                                >
                                                    View Orders
                                                </Button>
                                                <UserOrdersModal 
                                                    :isOpen="isUserOrdersModalOpen" 
                                                    :userId="props.selectedRowData.id" 
                                                    @close="closeUserOrdersModal" 
                                                />
                                            </div>
                                        </div> -->
                                        <CustomLabelGroup
                                            :inputId="'registered_name'"
                                            :labelValue="'Registered Name:'"
                                            :dataValue="props.selectedRowData?.registered_name || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'contact_name'"
                                            :labelValue="'Contact Name:'"
                                            :dataValue="props.selectedRowData?.contact_name || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'date_of_birth'"
                                            :labelValue="'Date Of Birth:'"
                                            :dataValue="props.selectedRowData?.date_of_birth || '-'"
                                            class="text-gray-700"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'home_number'"
                                            :labelValue="'Home Number:'"
                                            :dataValue="props.selectedRowData?.home_number || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'mobile_number'"
                                            :labelValue="'Mobile Number:'"
                                            :dataValue="props.selectedRowData?.mobile_number || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'email'"
                                            :labelValue="'Email:'"
                                            :dataValue="props.selectedRowData?.email || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'address_1'"
                                            :labelValue="'Address 1:'"
                                            :dataValue="props.selectedRowData?.address_1 || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'address_2'"
                                            :labelValue="'Address 2:'"
                                            :dataValue="props.selectedRowData?.address_2 || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'city'"
                                            :labelValue="'City / State / PC:'"
                                            :dataValue="props.selectedRowData?.city || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'country'"
                                            :labelValue="'Country:'"
                                            :dataValue="props.selectedRowData?.country || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'office_number_1'"
                                            :labelValue="'Office Number:'"
                                            :dataValue="props.selectedRowData?.office_number_1 || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'office_number_2'"
                                            :labelValue="'Office Number 2:'"
                                            :dataValue="props.selectedRowData?.office_number_2 || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'fax_number'"
                                            :labelValue="'Fax:'"
                                            :dataValue="props.selectedRowData?.fax_number || '-'"
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
                                            :inputId="'vc'"
                                            :labelValue="'VC:'"
                                            :dataValue="props.selectedRowData?.vc || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'room_number'"
                                            :labelValue="'Room #:'"
                                            :dataValue="props.selectedRowData?.room_number || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'allo'"
                                            :labelValue="'Allo:'"
                                            :dataValue="props.selectedRowData?.allo || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'allo_name'"
                                            :labelValue="'Allo Name:'"
                                            :dataValue="props.selectedRowData?.allo_name || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'exchanged_balance_due'"
                                            :labelValue="'Balance Due EU:'"
                                            :dataValue="props.selectedRowData?.exchanged_balance_due || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'balance_due'"
                                            :labelValue="'Balance Due:'"
                                            :dataValue="props.selectedRowData?.balance_due || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'currency_pair'"
                                            :labelValue="'Exchange rate for:'"
                                            :dataValue="props.selectedRowData?.currency_pair || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'exchange_rate'"
                                            :labelValue="'Exchange Rate:'"
                                            :dataValue="props.selectedRowData?.exchange_rate || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'sell_price'"
                                            :labelValue="'Sell Price:'"
                                            :dataValue="props.selectedRowData?.sell_price || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'allocation_officer'"
                                            :labelValue="'Allocation Officer:'"
                                            :dataValue="props.selectedRowData?.allocation_officer || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'trade_plus'"
                                            :labelValue="'Trade +:'"
                                            :dataValue="props.selectedRowData?.trade_plus || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'settlement_date'"
                                            :labelValue="'Settlement Date:'"
                                            :dataValue="props.selectedRowData?.settlement_date || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'factory'"
                                            :labelValue="'Factory:'"
                                            :dataValue="props.selectedRowData?.factory || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'pay_via'"
                                            :labelValue="'Paying Via:'"
                                            :dataValue="props.selectedRowData?.pay_via || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'allo_comment'"
                                            :labelValue="'Allo Comments:'"
                                            :dataValue="props.selectedRowData?.allo_comment || '-'"
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
                                            :inputId="'sm_number'"
                                            :labelValue="'SM #:'"
                                            :dataValue="props.selectedRowData?.sm_number || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'gm_number'"
                                            :labelValue="'GM #:'"
                                            :dataValue="props.selectedRowData?.gm_number || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'fm_number'"
                                            :labelValue="'FM #:'"
                                            :dataValue="props.selectedRowData?.fm_number || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'lm_number'"
                                            :labelValue="'LM #:'"
                                            :dataValue="props.selectedRowData?.lm_number || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'ao_1'"
                                            :labelValue="'AO #1:'"
                                            :dataValue="props.selectedRowData?.ao_1 || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'ao_1_name'"
                                            :labelValue="'AO #1 Name:'"
                                            :dataValue="props.selectedRowData?.ao_1_name || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'ao_2'"
                                            :labelValue="'AO #2:'"
                                            :dataValue="props.selectedRowData?.ao_2 || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'ao_2_name'"
                                            :labelValue="'AO #2 name:'"
                                            :dataValue="props.selectedRowData?.ao_2_name || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'se_number'"
                                            :labelValue="'SE #:'"
                                            :dataValue="props.selectedRowData?.se_number || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'se_name'"
                                            :labelValue="'SE Name:'"
                                            :dataValue="props.selectedRowData?.se_name || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'bonus_comment'"
                                            :labelValue="'Bonus Comments:'"
                                            :dataValue="props.selectedRowData?.bonus_comment || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'exit_time_frame'"
                                            :labelValue="'Exit Time Frame:'"
                                            :dataValue="props.selectedRowData?.exit_time_frame || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'docs_received'"
                                            :labelValue="'Docs Received:'"
                                            :dataValue="props.selectedRowData?.docs_received || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'tc_sent'"
                                            :labelValue="'TC Sent:'"
                                            :dataValue="props.selectedRowData?.tc_sent || '-'"
                                        />
                                        <CustomLabelGroup
                                            :inputId="'tt_received'"
                                            :labelValue="'TT Received:'"
                                            :dataValue="props.selectedRowData?.tt_received || '-'"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel
                                    :class="[
                                        'rounded-xl bg-gray-200 dark:bg-gray-700 p-3',
                                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                    ]"
                                >
                                    <div class="input-group">
                                        <div class="flex justify-between">
                                            <div class="w-full p-2 sm:px-0 max-h-80 h-80 overflow-auto">
                                                <div 
                                                    class="bg-gray-700 dark:bg-gray-700 rounded-xl text-gray-300 h-20 
                                                            px-4 flex flex-col justify-center w-full mb-2" 
                                                >
                                                    <div class="flex items-center w-full text-2xl font-bold">
                                                        <!-- $ {{ parseFloat(value.unit_price * value.quantity).toFixed(2) }} -->

                                                        <span class="text-sm text-gray-800 bg-yellow-600 px-4 rounded-md ml-auto">
                                                            <!-- {{ value.limb_stage }} -->
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center w-full">
                                                        <!-- <Link 
                                                            :href="route('orders.edit', value.id)"
                                                            class="text-xs text-blue-500 underline"
                                                        >
                                                            Trade ID: {{ value.trade_id }}
                                                        </Link> -->
                                                        <span class="text-xs text-gray-400 ml-auto">
                                                            <!-- {{ dayjs(value.created_at).tz(user.timezone).format('DD MMMM YYYY, hh:mm A') }} -->
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- <div class="w-full flex justify-center" v-else>
                                                    <h3 class="font-semibold text-gray-200"> No records found for this sale order currently. </h3>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </TabPanel>
                            </TabPanels>
                        </TabGroup>
                    </div>
                </div>
            </div>
            <div class="col-span-1">
                <!-- <UserChangelogsSection
                    :selectedRowData="props.selectedRowData"
                    class="w-full"
                /> -->
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
