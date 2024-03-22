<script setup>
import axios from "axios";
import dayjs from 'dayjs';
import { Link } from '@inertiajs/vue3';
import Label from '@/Components/Label.vue';
import Modal from '@/Components/Modal.vue';
import Button from '@/Components/Button.vue';
import { ref, onMounted, reactive } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import { ChevronUpIcon } from '@heroicons/vue/solid';
import { AtSymbolIcon } from '@heroicons/vue/outline';
import { ThreeDotsVertical } from '@/Components/Icons/solid';
import CustomLabelGroup from '@/Components/CustomLabelGroup.vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { PhoneIcon, MapIcon, FlagIcon, CalendarIcon } from '@heroicons/vue/solid';
import { cl, back, convertToHumanReadable, replaceHyphensWithEmpty } from '@/Composables';
import { Disclosure, DisclosureButton, DisclosurePanel, TransitionRoot } from '@headlessui/vue';

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    userId: {
        type: [String, Number],
    },
    isOpen: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['close', 'rowEdit']);

const userOrdersData = reactive({});
let originalUserOrdersData = null;

onMounted(async () => {
  try {
    const userOrdersResponse = await axios.get(route('users-clients.getUserOrders', props.userId));
    userOrdersData.value = userOrdersResponse.data;

    let userOrdersArray = [];
    for (let key in userOrdersData.value) {
        userOrdersArray.push(userOrdersData.value[key]);
    }

    userOrdersArray.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

    Object.keys(userOrdersData.value).forEach((col, index) => {
        userOrdersData.value[col].created_at = dayjs(userOrdersData.value[col].created_at).format('DD MMMM YYYY, hh:mm A');;
    });
    
    userOrdersData.value = userOrdersArray;
    originalUserOrdersData = userOrdersData.value;
  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

const filterByToday = () => {
  const today = dayjs();
  userOrdersData.value = originalUserOrdersData.filter((log) =>
    dayjs(log.created_at).isSame(today, 'day')
  );
};

const filterByPast7Days = () => {
  const sevenDaysAgo = dayjs().subtract(7, 'day');
  userOrdersData.value = originalUserOrdersData.filter((log) =>
    dayjs(log.created_at).isAfter(sevenDaysAgo)
  );
};

const filterByThisMonth = () => {
  const startOfMonth = dayjs().startOf('month');
  userOrdersData.value = originalUserOrdersData.filter((log) =>
    dayjs(log.created_at).isAfter(startOfMonth)
  );
};

const filterByThisYear = () => {
  const startOfYear = dayjs().startOf('year');
  userOrdersData.value = originalUserOrdersData.filter((log) =>
    dayjs(log.created_at).isAfter(startOfYear)
  );
};

const showAll = () => {
    userOrdersData.value = originalUserOrdersData;
};

let arr = ['1', '2', '3', "4", "5"]

</script>

<template>
    <Modal 
        :show="isOpen" 
        :maxWidth="'xl'" 
        :closeable="true" 
        @close="emit('close')"
    >
        <div class="p-6">
            <div class="p-3 rounded-lg">
                <div class="flex flex-col gap-6 mb-8 lg:mb-0">
                    <div class="input-group">
                        <div class="flex justify-between">
                            <p class="dark:text-gray-300 font-semibold text-xl pb-2">
                                Order List
                                <span class="text-xs font-thin pl-4">
                                    ( Total Orders: {{ userOrdersData.value.length }} )
                                </span>
                            </p>
                            <Dropdown 
                                :align="'right'" 
                                :width="50" 
                                :contentClasses="'dark:bg-dark-eval-3'"
                            >
                                <template #trigger>
                                    <ThreeDotsVertical class="flex-shrink-0 w-6 h-6 cursor-pointer mx-2" aria-hidden="true" />
                                </template>

                                <template #content>
                                    <div class="p- w-full">
                                        <p class="text-md p-2 text-gray-300 text-center">Date Filter</p>
                                        <hr class="border-b rounded-md border-gray-600 mb-2 w-10/12 mx-auto">
                                        <div class="px-6 pb-6 pt-2 flex flex-col gap-2">
                                            <Button 
                                                :type="'button'"
                                                :variant="'info'" 
                                                :size="'sm'" 
                                                class="justify-center px-6 py-2 w-full h-full whitespace-nowrap"
                                                @click="showAll"
                                            >
                                                {{ 'All' }}
                                            </Button>
                                            <Button 
                                                :type="'button'"
                                                :variant="'info'" 
                                                :size="'sm'" 
                                                class="justify-center px-6 py-2 w-full h-full whitespace-nowrap"
                                                @click="filterByToday"
                                            >
                                                {{ 'Today' }}
                                            </Button>
                                            <Button 
                                                :type="'button'"
                                                :variant="'info'" 
                                                :size="'sm'" 
                                                class="justify-center px-6 py-2 w-full h-full whitespace-nowrap"
                                                @click="filterByPast7Days"
                                            >
                                                {{ 'Past 7 days' }}
                                            </Button>
                                            <Button 
                                                :type="'button'"
                                                :variant="'info'" 
                                                :size="'sm'" 
                                                class="justify-center px-6 py-2 w-full h-full whitespace-nowrap"
                                                @click="filterByThisMonth"
                                            >
                                                {{ 'This month' }}
                                            </Button>
                                            <Button 
                                                :type="'button'"
                                                :variant="'info'" 
                                                :size="'sm'" 
                                                class="justify-center px-6 py-2 w-full h-full whitespace-nowrap"
                                                @click="filterByThisYear"
                                            >
                                                {{ 'This year' }}
                                            </Button>
                                        </div>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                        <div class="w-full p-2 sm:px-0 max-h-80 h-80 overflow-auto">
                            <div 
                                class="bg-gray-700 dark:bg-gray-700 rounded-xl text-gray-300 h-20 
                                        px-4 flex flex-col justify-center w-full mb-2" 
                                v-for="(value, ix) in userOrdersData.value" :key="ix"
                                v-if="userOrdersData.value.length > 0"
                            >
                                <div class="flex items-center w-full text-2xl font-bold">
                                    $ {{ parseFloat(value.unit_price * value.quantity).toFixed(2) }}

                                    <span class="text-sm text-gray-800 bg-yellow-600 px-4 rounded-md ml-auto">
                                        {{ value.limb_stage }}
                                    </span>
                                </div>
                                <div class="flex items-center w-full">
                                    <Link 
                                        :href="route('orders.edit', value.id)"
                                        class="text-xs text-blue-500 underline"
                                    >
                                        Trade ID: {{ value.trade_id }}
                                    </Link>
                                    <span class="text-xs text-gray-400 ml-auto">
                                        {{ value.created_at }}
                                    </span>
                                </div>
                            </div>
                            <div class="w-full flex justify-center" v-else>
                                <h3 class="font-semibold text-gray-200"> No records found for this user currently. </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end px-3">
                <div class="rounded-md shadow-lg flex flex-row gap-2">
                    <div class="action-button-group">
                        <Button 
                            :type="'button'"
                            :variant="'info'" 
                            :size="'base'"
                            class="justify-center gap-2 px-6 py-2 form-actions"
                            @click="emit('close')"
                        >
                            <span>Back</span>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>
