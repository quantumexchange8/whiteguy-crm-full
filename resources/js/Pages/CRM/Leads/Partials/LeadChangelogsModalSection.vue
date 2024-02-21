<script setup>
import { ref, onMounted, reactive } from 'vue'
import { cl, back, convertToHumanReadable } from '@/Composables'
import { 
    TabGroup, TabList, Tab, TabPanels, TabPanel, Disclosure, DisclosureButton, DisclosurePanel, TransitionRoot 
} from '@headlessui/vue'
import { CheckCircleIcon, ErrorCircleIcon } from '@/Components/Icons/outline';
import { ChevronUpIcon } from '@heroicons/vue/solid'
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

const leadChangelogsCategories = ref([
    'All', 'Lead Notes', 'System'
])

const leadNotesData = reactive({});
const leadChangelogsData = reactive({});
const combinedLogsData = reactive([]);

onMounted(async () => {
  try {
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

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

</script>

<template>
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
                        <div class="w-full flex justify-center" v-if="!combinedLogsData.value || combinedLogsData.value.length === 0">
                            <h3 class="font-semibold text-gray-200"> No records found for this lead currently. </h3>
                        </div>
                        <div v-for="(log, index) in combinedLogsData.value" :key="index" v-else>
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
                                                    <ChevronUpIcon
                                                        :class="[
                                                            open ? 'transition-all duration-200 rotate-270 transform' : '',
                                                            !open ? 'transition-all duration-200 rotate-180 transform' : '',
                                                        ]"
                                                        class="h-5 w-5 text-gray-400 ml-auto"
                                                    />
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
                                                        <ChevronUpIcon
                                                            :class="[
                                                                open ? 'transition-all duration-200 rotate-270 transform' : '',
                                                                !open ? 'transition-all duration-200 rotate-180 transform' : '',
                                                            ]"
                                                            class="h-5 w-5 text-gray-400 ml-auto"
                                                        />
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
                                                        <ChevronUpIcon
                                                            :class="[
                                                                open ? 'transition-all duration-200 rotate-270 transform' : '',
                                                                !open ? 'transition-all duration-200 rotate-180 transform' : '',
                                                            ]"
                                                            class="h-5 w-5 text-gray-400 ml-auto"
                                                        />
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
                                                                    ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : 'set' }}" to "{{ value.new }}".
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
                                                        <ChevronUpIcon
                                                            :class="[
                                                                open ? 'transition-all duration-200 rotate-270 transform' : '',
                                                                !open ? 'transition-all duration-200 rotate-180 transform' : '',
                                                            ]"
                                                            class="h-5 w-5 text-gray-400 ml-auto"
                                                        />
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
                                                                    <Label
                                                                        :inputId="'leadChangelogColumn'+ix"
                                                                        :labelValue="'Lead Notes'"
                                                                        class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                                    >
                                                                        ◉ [ Lead Note ID: {{ value.id }} ]: [ {{ (ix === 'New' || ix === 'Delete') ? ix + ' Note' : convertToHumanReadable(ix) }} ]
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
                                                                        ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : 'set' }}" to "{{ value.new }}".
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
                            'rounded-xl pt-4 pr-2 hidden-scrollable max-h-[900px]',
                            'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                        ]"
                    >
                        <!-- For Lead Notes Only -->
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
                                                <ChevronUpIcon
                                                    :class="[
                                                        open ? 'transition-all duration-200 rotate-270 transform' : '',
                                                        !open ? 'transition-all duration-200 rotate-180 transform' : '',
                                                    ]"
                                                    class="h-5 w-5 text-gray-400 ml-auto"
                                                />
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
                            'rounded-xl pt-4 pr-2 hidden-scrollable max-h-[900px]',
                            'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                        ]"
                    >
                        <!-- For Lead Changelogs Only -->
                        <div class="w-full flex justify-center" v-if="!leadChangelogsData.value || leadChangelogsData.value.length === 0">
                            <h3 class="font-semibold text-gray-200"> No records found for this lead currently. </h3>
                        </div>
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
                                        <div v-if="log.lead_changes && Object.keys(log.lead_changes).length > 0">
                                            <Disclosure v-slot="{ open }">
                                                <DisclosureButton 
                                                    class="bg-gray-700 dark:bg-gray-800/50 rounded-xl text-gray-300 p-2 mt-2 flex items-center w-full mb-1 text-sm font-bold"
                                                >
                                                    Lead Changelogs 
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
                                                    <ChevronUpIcon
                                                        :class="[
                                                            open ? 'transition-all duration-200 rotate-270 transform' : '',
                                                            !open ? 'transition-all duration-200 rotate-180 transform' : '',
                                                        ]"
                                                        class="h-5 w-5 text-gray-400 ml-auto"
                                                    />
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
                                                                ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : 'set' }}" to "{{ value.new }}".
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
                                                    <ChevronUpIcon
                                                        :class="[
                                                            open ? 'transition-all duration-200 rotate-270 transform' : '',
                                                            !open ? 'transition-all duration-200 rotate-180 transform' : '',
                                                        ]"
                                                        class="h-5 w-5 text-gray-400 ml-auto"
                                                    />
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
                                                                <Label
                                                                    :inputId="'leadChangelogColumn'+ix"
                                                                    :labelValue="'Lead Notes'"
                                                                    class="font-semibold !text-gray-200/75 text-xs col-span-1 pb-1"
                                                                >
                                                                    ◉ [ Lead Note ID: {{ value.id }} ]: [ {{ (ix === 'New' || ix === 'Delete') ? ix + ' Note' : convertToHumanReadable(ix) }} ]
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
                                                                    ➤ {{ convertToHumanReadable(ix) }} has been {{ (value.old !== null) ? 'changed from "' + value.old : 'set' }}" to "{{ value.new }}".
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
        </div>
    </div>
</template>