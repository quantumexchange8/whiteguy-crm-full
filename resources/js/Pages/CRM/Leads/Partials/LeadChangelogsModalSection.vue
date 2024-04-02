<script setup>
import axios from "axios";
import { ref, onMounted, reactive } from 'vue'
import dayjs from 'dayjs';
dayjs.extend(customParseFormat);
import customParseFormat from 'dayjs/plugin/customParseFormat';
import { 
    TabGroup, TabList, Tab, TabPanels, TabPanel, Disclosure, DisclosureButton, DisclosurePanel, TransitionRoot 
} from '@headlessui/vue'
import { ChevronUpIcon } from '@heroicons/vue/solid'
import { cl, back, convertToHumanReadable } from '@/Composables'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Dropdown from '@/Components/Dropdown.vue'
import { ThreeDotsVertical } from '@/Components/Icons/solid';
import { CheckCircleIcon, ErrorCircleIcon } from '@/Components/Icons/outline';

// Get the errors thats passed back from controller if there are any error after backend validations
const props = defineProps({
    selectedRowData: {
        type: Object,
        default: () => ({}),
    },
    withHistory: {
        type: Boolean,
        default: true,
    },
})

const leadLogEntriesCategories = ref([
    'Lead Notes', 'History'
])

const leadNotesData = reactive({});
const leadLogEntriesData = reactive({});
let originalLeadNoteslogsData = null;
let originalLeadLogEntriesData = null;

onMounted(async () => {
  try {
    const leadNotesResponse = await axios.get(route('leads.getLeadNotes', props.selectedRowData.id));
    leadNotesData.value = leadNotesResponse.data;
    originalLeadNoteslogsData = leadNotesResponse.data;

    const leadLogEntriesResponse = await axios.get(route('leads.getLeadLogEntries', props.selectedRowData.id));
    leadLogEntriesData.value = leadLogEntriesResponse.data;
    originalLeadLogEntriesData = leadLogEntriesData.value;

    leadNotesData.value.forEach(note => {
        note.note = note.note.replace(/\n/g, '<br>');
    });

    // let combinedLogsArray = [];

    // for (let key in leadNotesData.value) {
    //     combinedLogsArray.push(leadNotesData.value[key]);
    // }
    // for (let key in leadChangelogsData.value) {
    //     combinedLogsArray.push(leadChangelogsData.value[key]);
    // }

    // // Sort combined data chronologically
    // combinedLogsArray.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

    // // Format the created_at dates
    // combinedLogsArray.forEach(log => {
    //   log.created_at = dayjs(log.created_at).format('DD MMMM YYYY, hh:mm A');
    // });

    // Convert sorted array back to object
    // combinedLogsData.value = combinedLogsArray;
    // originalCombinedLogsData = combinedLogsData.value;

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

const showAllNotes = () => {
    leadNotesData.value = originalLeadNoteslogsData;
};

const filterByUserNotes = () => {
    leadNotesData.value = originalLeadNoteslogsData.filter(item => item.color !== 'info');
};

const filterBySystemNotes = () => {
    leadNotesData.value = originalLeadNoteslogsData.filter(item => item.color !== 'primary');
};

const filterByToday = () => {
  const today = dayjs();
  leadNotesData.value = originalLeadNoteslogsData.filter((log) =>
    dayjs(log.created_at).isSame(today, 'day')
  );
  leadLogEntriesData.value = originalLeadLogEntriesData.filter((log) =>
    dayjs(log.timestamp).isSame(today, 'day')
  );
};

const filterByPast7Days = () => {
  const sevenDaysAgo = dayjs().subtract(7, 'day');
  leadNotesData.value = originalLeadNoteslogsData.filter((log) =>
    dayjs(log.created_at).isAfter(sevenDaysAgo)
  );
  leadLogEntriesData.value = originalLeadLogEntriesData.filter((log) =>
    dayjs(log.timestamp).isAfter(sevenDaysAgo)
  );
};

const filterByThisMonth = () => {
  const startOfMonth = dayjs().startOf('month');
  leadNotesData.value = originalLeadNoteslogsData.filter((log) =>
    dayjs(log.created_at).isAfter(startOfMonth)
  );
  leadLogEntriesData.value = originalLeadLogEntriesData.filter((log) =>
    dayjs(log.timestamp).isAfter(startOfMonth)
  );
};

const filterByThisYear = () => {
  const startOfYear = dayjs().startOf('year');
  leadNotesData.value = originalLeadNoteslogsData.filter((log) =>
    dayjs(log.created_at).isAfter(startOfYear)
  );
  leadLogEntriesData.value = originalLeadLogEntriesData.filter((log) =>
    dayjs(log.timestamp).isAfter(startOfYear)
  );
};

const showAll = () => {
    leadNotesData.value = originalLeadNoteslogsData;
    leadLogEntriesData.value = originalLeadLogEntriesData;
};

const formatLogChanges = (value) => {
    return dayjs(value, 'YYYY-MM-DD HH:mm', false).isValid() ? dayjs(value).format('YYYY-MM-DD HH:mm:ss') : value;
}

</script>

<template>
    <div class="rounded-xl" :class="[withHistory ? 'input-group p-8' : '']">
        <div class="flex justify-between" v-if="withHistory">
            <p class="dark:text-gray-300 font-semibold text-xl pb-2">Lead Notes & History</p>
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
        <div class="container">
            <TabGroup>
                <TabList class="flex space-x-1 rounded-xl bg-blue-600/30 p-1 flex-wrap md:flex-nowrap" v-if="withHistory">
                    <Tab
                        v-for="category in leadLogEntriesCategories"
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
                            'rounded-xl pt-4 pr-2 hidden-scrollable max-h-[828px]',
                            'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                        ]"
                    >
                        <!-- For Lead Notes Only -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-8 pl-2">
                            <p class="dark:text-gray-200 col-span-full md:col-span-3 font-semibold text-base pl-2 self-center">Filter By: </p>
                            <Button 
                                :type="'button'"
                                :variant="'secondary'" 
                                :size="'xs'" 
                                class="justify-center gap-2 px-6 py-2 col-span-full md:col-span-3 form-actions !bg-blue-600"
                                @click="showAllNotes"
                            >
                                <span class="text-white">All</span>
                            </Button>
                            <Button 
                                :type="'button'"
                                :size="'xs'" 
                                class="justify-center gap-2 px-6 py-2 col-span-full md:col-span-3 form-actions"
                                @click="filterByUserNotes"
                            >
                                <span>User</span>
                            </Button>
                            <Button 
                                :type="'button'"
                                :variant="'info'" 
                                :size="'xs'" 
                                class="justify-center gap-2 px-6 py-2 col-span-full md:col-span-3 form-actions"
                                @click="filterBySystemNotes"
                            >
                                <span>System</span>
                            </Button>
                        </div>
                        <div class="w-full flex justify-center" v-if="!leadNotesData.value || leadNotesData.value.length === 0">
                            <h3 class="font-semibold text-gray-200"> No records found for this lead currently. </h3>
                        </div>
                        <div v-for="(log, index) in leadNotesData.value" :key="index">
                            <div class="flex flex-col md:grid grid-cols-12">
                                <div class="flex md:contents">
                                    <div class="col-start-1 col-end-2 mr-10 md:mx-auto relative">
                                        <div class="h-full w-3 flex items-center justify-center">
                                            <div class="h-full w-[2px] bg-purple-200/25 pointer-events-none"></div>
                                        </div>
                                        <div 
                                            class="w-3 h-3 absolute top-0 rounded-full shadow text-center ring"
                                            :class="[
                                                (log.color === 'primary') ? 'bg-purple-500 ring-purple-500' : 'bg-cyan-500 ring-cyan-500',
                                            ]"
                                        >
                                        </div>
                                    </div>
                                    <div class="bg-gray-700 dark:bg-gray-700/70 col-start-2 col-end-13 p-4 rounded-xl mb-4 mr-auto shadow-md w-full">
                                        <h3 
                                            class="font-semibold text-gray-200" 
                                            v-html="(log.note.replace(/\n/g, '<br>')).substring(0, (log.note.replace(/\n/g, '<br>')).indexOf('Front form data'))" 
                                            v-if="log.note.includes('Front form data')"
                                        >
                                        </h3>
                                        <h3 class="font-semibold text-gray-200" v-html="log.note.replace(/\n/g, '<br>')" v-else></h3>
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
                                                <ChevronUpIcon
                                                    :class="[
                                                        open ? 'transition-all duration-200 rotate-270 transform' : '',
                                                        !open ? 'transition-all duration-200 rotate-180 transform' : '',
                                                    ]"
                                                    class="h-5 w-5 text-gray-400 ml-auto"
                                                />
                                            </DisclosureButton>
                                            <TransitionRoot
                                                className="transition-all duration-500"
                                                enterFrom="transform scale-95 opacity-0 max-h-0"
                                                enterTo="transform scale-100 opacity-100 max-h-96"
                                                leaveFrom="transform scale-100 opacity-100 max-h-96"
                                                leaveTo="transform scale-95 opacity-0 max-h-0"
                                            >
                                                <DisclosurePanel 
                                                    class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3"
                                                >
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
                                                        Created by: <span class="inline-block bg-cyan-800 rounded-xl py-1 px-3">{{ log.lead_note_creator.username || '' }} ({{ log.lead_note_creator.site.name || '' }})</span>
                                                    </Label>
                                                </DisclosurePanel>
                                            </TransitionRoot>
                                        </Disclosure>
                                        <Disclosure v-slot="{ open }" v-if="log.note.includes('Front form data')">
                                            <DisclosureButton 
                                                class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                            >
                                                Lead Front Form Details 
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
                                                className="transition-all duration-500"
                                                enterFrom="transform scale-95 opacity-0 max-h-0"
                                                enterTo="transform scale-100 opacity-100 max-h-96"
                                                leaveFrom="transform scale-100 opacity-100 max-h-96"
                                                leaveTo="transform scale-95 opacity-0 max-h-0"
                                            >
                                                <DisclosurePanel 
                                                    class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3"
                                                >
                                                    <div v-for="(value, ix) in JSON.parse((log.note.slice(log.note.indexOf('{')).replace(/<br>/g, '')))" :key="ix">
                                                        <Label
                                                            :inputId="'leadChangelogColumn'+ix"
                                                            class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                        >
                                                            ◉ [{{ convertToHumanReadable(ix) }}] 
                                                        </Label>
                                                        <Label
                                                            :inputId="'leadChangelog'+ix"
                                                            class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                        >
                                                            ➤ {{ convertToHumanReadable(ix) }} has been set to "{{ value }}".
                                                        </Label>
                                                    </div>
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
                            'rounded-xl pt-4 pr-2 hidden-scrollable max-h-[828px]',
                            'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                        ]"
                    >
                        <!-- For Lead Log Entries Only -->
                        <div class="w-full flex justify-center" v-if="!leadLogEntriesData.value || leadLogEntriesData.value.length === 0">
                            <h3 class="font-semibold text-gray-200"> No records found for this lead currently. </h3>
                        </div>
                        <div v-for="(log, index) in leadLogEntriesData.value" :key="index">
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
                                                    Lead Log Entries 
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
                                                    className="transition-all duration-500"
                                                    enterFrom="transform scale-95 opacity-0 max-h-0"
                                                    enterTo="transform scale-100 opacity-100 max-h-96"
                                                    leaveFrom="transform scale-100 opacity-100 max-h-96"
                                                    leaveTo="transform scale-95 opacity-0 max-h-0"
                                                >
                                                    <DisclosurePanel 
                                                        class="bg-gray-700 dark:bg-gray-900/60 rounded-xl text-gray-500 p-4 text-xs flex flex-col gap-3 max-h-52 overflow-auto"
                                                    >
                                                        <div v-for="(value, ix) in JSON.parse(log.changes)" :key="ix">
                                                            <Label
                                                                :inputId="'leadChangelogColumn'+ix"
                                                                :labelValue="'Lead Notes'"
                                                                class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                            >
                                                                ◉ [{{ (ix === 'New') ? 'New Lead' : convertToHumanReadable(ix) }}] 
                                                            </Label>
                                                            <Label
                                                                :inputId="'leadChangelog'+ix"
                                                                :labelValue="'Lead Notes'"
                                                                class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                v-if="ix === 'New' || ix === 'Delete'"
                                                            >
                                                                ➤ {{ value.description }}.
                                                            </Label>
                                                            <Label
                                                                :inputId="'leadChangelog'+ix"
                                                                :labelValue="'Lead Notes'"
                                                                class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                v-else
                                                            >
                                                                ➤ {{ convertToHumanReadable(ix) }} has been {{ (value[0] !== null && value[0] !== 'None') ? 'changed from "' + formatLogChanges(value[0]) : 'set from "' + formatLogChanges(value[0]) }}" to "{{ formatLogChanges(value[1]) }}".
                                                            </Label>
                                                        </div>
                                                        <!-- <div v-for="(value, ix) in log.changes" :key="ix">
                                                            <Label
                                                                :inputId="'leadChangelogColumn'+ix"
                                                                :labelValue="'Lead Notes'"
                                                                class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                            >
                                                                ◉ [ Lead ID: {{ value.id }} ]: [{{ (ix === 'New') ? 'New Lead' : convertToHumanReadable(ix) }}] 
                                                            </Label>
                                                            <Label
                                                                :inputId="'leadChangelog'+ix"
                                                                :labelValue="'Lead Notes'"
                                                                class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                v-if="ix === 'New' || ix === 'Delete'"
                                                            >
                                                                ➤ {{ value.description }}.
                                                            </Label>
                                                            <Label
                                                                :inputId="'leadChangelog'+ix"
                                                                :labelValue="'Lead Notes'"
                                                                class="font-semibold !text-gray-200/75 text-xs col-span-4 pl-4"
                                                                v-else
                                                            >
                                                                ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : "set" }}" to "{{ value.new }}".
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
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>
    </div>
</template>