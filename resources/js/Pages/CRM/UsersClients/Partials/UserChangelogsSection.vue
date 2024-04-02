<script setup>
import axios from "axios";
import dayjs from 'dayjs';
import { ref, onMounted, reactive } from 'vue';
import { ChevronUpIcon } from '@heroicons/vue/solid';
import { Disclosure, DisclosureButton, DisclosurePanel, TransitionRoot } from '@headlessui/vue';
import { cl, back, convertToHumanReadable } from '@/Composables';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';
import Dropdown from '@/Components/Dropdown.vue';
import { ThreeDotsVertical } from '@/Components/Icons/solid';

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    selectedRowData: {
        type: Object,
        default: () => ({}),
    },
})

const userLogEntriesData = reactive({});
let originalUserLogEntriesData = null;

onMounted(async () => {
  try {
    const userLogEntriesResponse = await axios.get(route('users-clients.getUserLogEntries', props.selectedRowData.id));
    userLogEntriesData.value = userLogEntriesResponse.data;
    originalUserLogEntriesData = userLogEntriesData.value;

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

const filterByToday = () => {
  const today = dayjs();
  userLogEntriesData.value = originalUserLogEntriesData.filter((log) =>
    dayjs(log.timestamp).isSame(today, 'day')
  );
};

const filterByPast7Days = () => {
  const sevenDaysAgo = dayjs().subtract(7, 'day');
  userLogEntriesData.value = originalUserLogEntriesData.filter((log) =>
    dayjs(log.timestamp).isAfter(sevenDaysAgo)
  );
};

const filterByThisMonth = () => {
  const startOfMonth = dayjs().startOf('month');
  userLogEntriesData.value = originalUserLogEntriesData.filter((log) =>
    dayjs(log.timestamp).isAfter(startOfMonth)
  );
};

const filterByThisYear = () => {
  const startOfYear = dayjs().startOf('year');
  userLogEntriesData.value = originalUserLogEntriesData.filter((log) =>
    dayjs(log.timestamp).isAfter(startOfYear)
  );
};

const showAll = () => {
  userLogEntriesData.value = originalUserLogEntriesData;
};

const formatLogChanges = (value) => {
    return dayjs(value, 'YYYY-MM-DD HH:mm', false).isValid() ? dayjs(value).format('YYYY-MM-DD HH:mm:ss') : value;
}
</script>

<template>
    <div class="input-group p-8 rounded-xl">
        <div class="flex justify-between">
            <p class="dark:text-gray-300 font-semibold text-xl pb-2">History</p>
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
        <div class="container hidden-scrollable max-h-[500px] pt-2">
            <!-- For User Client Changelogs -->
            <div class="w-full flex justify-center" v-if="!userLogEntriesData.value || userLogEntriesData.value.length === 0">
                <h3 class="font-semibold text-gray-200"> No records found for this user currently. </h3>
            </div>
            <div v-for="(log, index) in userLogEntriesData.value" :key="index">
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
                                [System]: {{ log.action === 0 ? 'Newly created' : 'Updated' }}
                            </h3>
                            <span class="text-xs text-gray-400">
                                {{ log.created_at }}
                            </span>
                            <div v-if="log.changes && Object.keys(log.changes).length > 0">
                                <Disclosure v-slot="{ open }">
                                    <DisclosureButton 
                                        class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                    >
                                        User Client Log Entries 
                                        <span class="text-xs font-thin pl-4">
                                            ( {{ log.id }} )
                                        </span>
                                        <ChevronUpIcon
                                            :class="[
                                                open ? 'transition-all duration-200 rotate-270 transform' : '',
                                                !open ? 'transition-all duration-200 rotate-180 transform' : '',
                                            ]"
                                            class="h-5 w-5 text-gray-400 ml-auto"
                                        />
                                    </DisclosureButton>
                                    <TransitionRoot
                                        enter="transition-all ease-in-out duration-300 overflow-hidden"
                                        enterFrom="transform scale-95 opacity-0 max-h-0"
                                        enterTo="transform scale-100 opacity-100 max-h-96"
                                        leave="transition-all ease-out duration-150 overflow-hidden"
                                        leaveFrom="transform scale-100 opacity-100 max-h-96"
                                        leaveTo="transform scale-95 opacity-0 max-h-0"
                                    >
                                        <DisclosurePanel 
                                            class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3 max-h-52 overflow-auto"
                                        >
                                            <div v-for="(value, ix) in JSON.parse(log.changes)" :key="ix">
                                                <Label
                                                    :inputId="'userClientChangelogColumn'+ix"
                                                    class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                >
                                                    ◉ [{{ (ix === 'New') ? 'New Lead' : convertToHumanReadable(ix) }}] 
                                                </Label>
                                                <Label
                                                    :inputId="'userClientChangelog'+ix"
                                                    class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                    v-if="ix === 'New' || ix === 'Delete'"
                                                >
                                                    ➤ {{ value.description }}.
                                                </Label>
                                                <Label
                                                    :inputId="'userClientChangelog'+ix"
                                                    class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                    v-else
                                                >
                                                    ➤ {{ convertToHumanReadable(ix) }} has been {{ (value[0] !== null && value[0] !== 'None') ? 'changed from "' + formatLogChanges(value[0]) : 'set from "' + formatLogChanges(value[0]) }}" to "{{ formatLogChanges(value[1]) }}".
                                                </Label>
                                            </div>
                                            <!-- <div v-for="(value, ix) in log.changes" :key="ix">
                                                <Label
                                                    :inputId="'userClientChangelogColumn'+ix"
                                                    :labelValue="'Lead Notes'"
                                                    class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                >
                                                    ◉ [ User Client ID: {{ log.users_clients_id }} ]: [{{ (ix === 'New') ? 'New User Client' : convertToHumanReadable(ix) }}] 
                                                </Label>
                                                <Label
                                                    :inputId="'userClientChangelog'+ix"
                                                    :labelValue="'Lead Notes'"
                                                    class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                    v-if="ix === 'New' || ix === 'Delete' || ix ==='Password'"
                                                >
                                                    ➤ {{ value.description }}.
                                                </Label>
                                                <Label
                                                    :inputId="'userClientChangelog'+ix"
                                                    :labelValue="'Lead Notes'"
                                                    class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                    v-else
                                                >
                                                    ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : 'set' }}" to "{{ value.new }}".
                                                </Label>
                                            </div> -->
                                        </DisclosurePanel>
                                    </TransitionRoot>
                                </Disclosure>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>